<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');

class Rencana_pembelian extends BE_Controller {

    var $controller = 'rencana_pembelian';
	function __construct() {
		parent::__construct();
	}

	function index() {
        $table = 'tbl_material_planning_' . user('tahun_budget');

        $data['tahun'] = get_data('tbl_fact_tahun_budget', [
            'where' => [
                'is_active' => 1,
                'tahun' => user('tahun_budget')
            ]
        ])->result();     

        // Tambahkan data supplier seperti di material planning
        $data['supplier'] = get_data('tbl_m_supplier', [
            'where' => [
                'is_active' => 1,
            ]
        ])->result();
        
        // Mengikuti pola material planning - ambil dari tbl_material_formula
		$arr = [
            'select' => 'distinct a.component_item as material_code, a.material_name',
            'join' =>  'tbl_budget_production b on b.budget_product_code= a.parent_item and b.tahun="'.user('tahun_budget').'" type LEFT',
            'where' => [
                'a.tahun' => user('tahun_budget'),
                'a.total !=' => 0,
                'b.total_budget !=' => 0
            ],
        ];

        $data['produk_items'] = get_data('tbl_material_formula a', $arr)->result();
        $access         = get_access($this->controller);
        $data['access'] = $access ;
        $data['access_additional']  = $access['access_additional'];
        render($data);
	}

    function data($tahun="",$supplier="",$tipe = 'table'){
		ini_set('memory_limit', '-1');
        ini_set('max_execution_time', -1);
        $table = 'tbl_material_planning_' . $tahun ;

        // Optimasi: Ambil data material dengan join yang lebih efisien
        $arr = [
            'select' => 'a.*, d.m_cov, d.moq, d.order_multiple, c.kode_supplier',
            'join' => ['tbl_material_formula b on a.material_code = b.component_item and b.tahun = "' . $tahun . '"',
                        'tbl_material_supplier c on a.material_code = c.material_code type LEFT',
                        'tbl_beginning_stock_material d on a.material_code = d.material_code and d.tahun = "' . $tahun . '" type LEFT',
            ],
            'where' => [
                'b.tahun' => $tahun,
                'a.posting_code' => 'STA', // Ambil dari STA dulu seperti di material planning
            ],
            'group_by' => 'a.material_code'
        ];

        if($supplier && $supplier != 'ALL' && $supplier != '') {
	    	$arr['where']['c.kode_supplier']	= $supplier;	
	    }

        // Ambil produk STA (base data) seperti di material planning
        $data['produk'] = get_data($table . ' a', $arr)->result();
        
        // Debug untuk melihat apakah ada data
        // debug($data['produk']);die;

        // Optimasi: Ambil semua data posting code sekaligus untuk material yang diperlukan
        $material_codes = array_column($data['produk'], 'material_code');
        
        if(!empty($material_codes)) {
            // Daftar posting_code yang diperlukan untuk rencana pembelian
            $posting_codes = ['ARQ', 'PBL', 'PMK', 'AVA', 'STE', 'COV', 'ERQ', 'ERD'];
            
            // Query sekaligus untuk semua posting code dan material
            $all_data = get_data($table . ' a', [
                'select' => 'a.*, b.tahun',
                'join' => 'tbl_material_formula b on a.material_code = b.component_item and b.tahun = "' . $tahun . '" type LEFT',
                'where' => [
                    'b.tahun' => $tahun,
                    'a.posting_code' => $posting_codes,
                    'a.material_code' => $material_codes,
                ],
            ])->result_array();

            // Organisir data berdasarkan posting_code dan material_code
            $organized_data = [];
            foreach($all_data as $row) {
                $posting_code = $row['posting_code'];
                $material_code = $row['material_code'];
                
                // Map posting code ke key yang digunakan di view
                $key_mapping = [
                    'ARQ' => 'prod',
                    'PBL' => 'arival',
                    'PMK' => 'pakai',
                    'AVA' => 'available',
                    'STE' => 'inventory',
                    'COV' => 'cov',
                    'ERQ' => 'erq',
                    'ERD' => 'erd',
                ];
                
                if(isset($key_mapping[$posting_code])) {
                    $key = $key_mapping[$posting_code];
                    $organized_data[$key][$material_code] = $row;
                }
            }

            // Assign ke data array
            $data['prod'] = $organized_data['prod'] ?? [];
            $data['arival'] = $organized_data['arival'] ?? [];
            $data['pakai'] = $organized_data['pakai'] ?? [];
            $data['available'] = $organized_data['available'] ?? [];
            $data['inventory'] = $organized_data['inventory'] ?? [];
            $data['cov'] = $organized_data['cov'] ?? [];
            $data['erq'] = $organized_data['erq'] ?? [];
            $data['erd'] = $organized_data['erd'] ?? [];
        } else {
            // Jika tidak ada data, inisialisasi array kosong
            $data['prod'] = [];
            $data['arival'] = [];
            $data['pakai'] = [];
            $data['available'] = [];
            $data['inventory'] = [];
            $data['cov'] = [];
            $data['erq'] = [];
            $data['erd'] = [];
        }

        $response = [
            'table' => $this->load->view('material_cost/rencana_pembelian/table',$data,true),
            'erq' => $data['erq'] ?? [],
            'erd' => $data['erd'] ?? [],
        ];
	   
	    render($response,'json');
    }

    
    function save_perubahan() {       
        ini_set('memory_limit', '-1');
        ini_set('max_execution_time', -1);  
        render($response,'json');
	}

    function sync_qtyproductions($tahun) {       
        ini_set('memory_limit', '-1');
        ini_set('max_execution_time', -1);

        $table = 'tbl_budget_production';

        $table2 = 'tbl_fact_allocation_qc';
        $table3 = 'tbl_fact_product_ovh';

        $production = get_data($table,[
            'select' => 'id_budget_product, budget_product_code, total_budget',
            'where' => [
                'tahun' => $tahun,
                'total_budget !=' => 0,
            ],
        ])->result();

        foreach($production as $p) {        
            update_data($table2,['product_qty' => $p->total_budget], ['tahun'=>$tahun,'product_code'=>$p->budget_product_code]);
            update_data($table3,['qty_production' => $p->total_budget], ['tahun'=>$tahun,'product_code'=>$p->budget_product_code]);

        }

        echo 'succcess';

    }
}