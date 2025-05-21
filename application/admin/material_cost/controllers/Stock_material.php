<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');

class Stock_material extends BE_Controller {

    var $controller = 'stock_material';
	function __construct() {
		parent::__construct();
	}

	function index() {
        $data['tahun'] = get_data('tbl_fact_tahun_budget', [
            'where' => [
                'is_active' => 1,
            ]
        ])->result();     
        
        $arr = [
            'select' => 'a.cost_centre as kode, b.id, b.cost_centre',
            'join' => 'tbl_fact_cost_centre b on a.cost_centre = b.kode type LEFT',
            'where' => [
                'a.is_active' => 1,
                'a.id_cost_centre !=' => 0,
            ],
            'group_by' => 'a.id_cost_centre',
            'sort_by' => 'b.id', 
             ];


	    $data['cc']= get_data('tbl_fact_product a',$arr)->result();

        $access         = get_access($this->controller);
        $data['access'] = $access ;
        $data['access_additional']  = $access['access_additional'];
        render($data);
	}

    function data($tahun="",$cost_centre="",$tipe = 'table'){
		ini_set('memory_limit', '-1');
  
        $data['produk']= get_data('tbl_beginning_stock_material a',[
            'select' => 'a.*',
            'where' => [
                'a.tahun' => $tahun,
            ],
        ])->result();



        $response	= array(
            'table'		=> $this->load->view('material_cost/stock_material/table',$data,true),
        );
	   
	    render($response,'json');
    }

    
    function save_perubahan() {       
 
        $table = 'tbl_beginning_stock';

        $data   = json_decode(post('json'),true);

        foreach($data as $id => $record) {
            $result = $record;
            foreach ($result as $r => $v) {               
                update_data($table, $result,'id',$id);
             }      
        }
    }
   

    function import() {
		ini_set('memory_limit', '-1');
        ini_set('max_execution_time', -1);

        $tahun = post('tahun');
        $table = 'tbl_beginning_stock_material';
		$file = post('fileimport');
        $filter = post();


        $col = ['MATERIAL_CODE','MATERIAL_NAME' , 'UM','SUPPLIER', 'MOQ', 'ORDER_MULTIPLE', 'M_COV', 'TOTAL_STOCK'];
 
        $this->load->library('simpleexcel');
		$this->simpleexcel->define_column($col);
		$count_import = $this->simpleexcel->read($file);
        // $loop_data = $this->simpleexcel->parsing(0,326);

        // debug($loop_data);die;
        $data_imported = 0;

        // $totalData = array();
            for ($i=6; $i <= $count_import[0]; $i++) { 
                $loop_data = $this->simpleexcel->parsing(0,$i+1);

               
                $data['tahun'] = post('tahun');      
 
                $data['material_code'] = $loop_data['MATERIAL_CODE'];
                $product = get_data('tbl_material_formula a',[
                    'select' => 'a.*',
                    'where' => [
                        'component_item' => $loop_data['MATERIAL_CODE']
                    ],
                    ])->row();

                $data['material_name'] = '';
                $data['um'] = '';

                if(isset($product->component_item)) {
                    $data['material_name'] =  $product->material_name;
                    $data['um'] =  $product->um;
                }
                
                $data['supplier'] = $loop_data['SUPPLIER'];
                $data['moq'] = (isset($loop_data['MOQ']) ? str_replace(['.',','],'', $loop_data['MOQ']) : 0);

                $data['order_multiple'] = (isset($loop_data['ORDER_MULTIPLE']) ? str_replace(['.',','],'', $loop_data['ORDER_MULTIPLE']) : 0);

                $data['m_cov'] = $loop_data['M_COV'];
                $data['total_stock'] = (isset($loop_data['TOTAL_STOCK']) ? str_replace(['.',','],'', $loop_data['TOTAL_STOCK']) : 0);

                $data['update_at'] = date('Y-m-d H:i:s');
                $data['update_by'] = user('nama');

                $arr   = [
                    'select'    => 'a.*',
                    'where'     => [
                        'a.material_code' => $loop_data['material_code'],
                		'a.tahun' => $tahun,
                    ],
                ];

                $cek = get_data($table . ' a',$arr)->row();
                if(isset($cek->budget_product_code)) {	
                    $save = update_data($table, $data, [
                        'id' => $cek->id
                    ]);
                    $data_imported++;
                }else{
                    $save = insert_data($table, $data);
                    $data_imported++;
                }

            }

            $response = [];
            if($data_imported > 1){
                // @unlink($file);
                $response = [
                    'status' => 'success',
                    'message' => "$data_imported Data berhasil terimport!"
                ];
            }else{
                $response = [
                    'status' => 'failed',
                    'message' => 'Import data gagal'
                ];
            }

        render($response,'json');
	}

}