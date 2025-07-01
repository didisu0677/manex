<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');

class Budget_production extends BE_Controller {

    var $controller = 'budget_production';
	function __construct() {
		parent::__construct();
	}

	function index() {
        $data['tahun'] = get_data('tbl_fact_tahun_budget', [
            'where' => [
                'is_active' => 1,
                'tahun' => user('tahun_budget')
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

        $table = 'tbl_budget_production';

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

        if($cost_centre && $cost_centre !='ALL') $arr['where']['a.cost_centre'] = $cost_centre;


	    $data['grup'][0]= get_data('tbl_fact_product a',$arr)->result();


        foreach($data['grup'][0] as $m0) {	

            $cproduk = get_data('tbl_fact_product a',[
                'where' => [
                    'a.is_active' => 1,
                    'a.id_cost_centre' => $m0->id,
                ],
                'sort_by' => 'a.id_cost_centre'
            ])->result();
            
            foreach($cproduk as $p) {   
                $cek = get_data($table . ' a',[
                    'select' => 'a.*',
                    'where' => [
                        'a.tahun' => $tahun,
                        'a.budget_product_code' => $p->code,
                        'a.product_line' => $p->product_line,
                    ]
                ])->row();
                if(!isset($cek->id)){
                    insert_data($table,
                    ['tahun' => $tahun, 'id_cost_centre' => $p->id_cost_centre ,'divisi' => $p->divisi, 'product_line' => $p->product_line, 'id_budget_product'=>$p->id, 'budget_product_code'=>$p->code, 
                    'budget_product_name' => $p->product_name, 'category' => $p->sub_product]
                );
                }
            }


            $data['produk'][$m0->id]= get_data('tbl_budget_production a',[
                'select' => 'a.*,b.code,b.product_name,b.destination, c.abbreviation as initial, c.cost_centre',
                'join' =>  ['tbl_fact_product b on a.budget_product_code = b.code',
                            'tbl_fact_cost_centre c on a.id_cost_centre = c.id type LEFT',
                           ],
                'where' => [
                    'a.tahun' => $tahun,
                    'a.id_cost_centre' =>$m0->id
                ],
                'sort_by' => 'a.id_cost_centre'
            ])->result();

        }


        $response	= array(
            'table'		=> $this->load->view('transaction/budget_production/table',$data,true),
        );
	   
	    render($response,'json');
    }

    
    function save_perubahan() {       
 
        $table = 'tbl_budget_production';

        $table2 = 'tbl_fact_allocation_qc';
        $table3 = 'tbl_fact_product_ovh';

        $data   = json_decode(post('json'),true);

        foreach($data as $id => $record) {
            $result = $record;
            foreach ($result as $r => $v) {               
                update_data($table, $result,'id',$id);

                $upd = get_data($table, 'id',$id)->row();
                $total_qty = (isset($upd->total_budget) ? $upd->total_budget : 0);

                $field = '';
                $total = 0;
                for ($i = 1; $i <= 12; $i++) { 
                    $field = 'B_' . sprintf('%02d', $i);
                    $total += $upd->$field ;
                }
                update_data($table,['total_budget' => $total],'id',$upd->id);
                update_data($table2,['product_qty' => $total], ['tahun'=>post('tahun'),'product_code'=>$upd->budget_product_code]);
                update_data($table3,['qty_production' => $total], ['tahun'=>post('tahun'),'product_code'=>$upd->budget_product_code]);

            }      
        }
    }
   

    function import() {
		ini_set('memory_limit', '-1');
        ini_set('max_execution_time', -1);

        $tahun = post('tahun');
        $table = 'tbl_budget_production';
        $table2 = 'tbl_fact_allocation_qc';
        $table3 = 'tbl_fact_product_ovh';
		$file = post('fileimport');
        $filter = post();


        $col = ['PRODUCT', 'CODE', 'B_01', 'B_02', 'B_03', 'B_04', 'B_05', 'B_06', 'B_07', 'B_08', 'B_09', 'B_10', 'B_11', 'B_12', 'TOTAL'];
 
        $this->load->library('simpleexcel');
		$this->simpleexcel->define_column($col);
		$count_import = $this->simpleexcel->read($file);
        // $loop_data = $this->simpleexcel->parsing(0,326);

        // debug($loop_data);die;
        $data_imported = 0;

        // $totalData = array();
  
            for ($i=8; $i <= $count_import[0]; $i++) { 
                $loop_data = $this->simpleexcel->parsing(0,$i+1);
                

                $data['tahun'] = post('tahun');      
                $data['product_line'] = '';
                $data['divisi'] = '';
                $data['category'] = '';
                $data['id_budget_product'] = 0;

                $product = get_data('tbl_fact_product a',[
                    'select' => 'a.*',
                    'where' => [
                        'code' => $loop_data['CODE']
                    ],
                    ])->row();

                if(isset($product->code)) {
                    $data['product_line'] =  $product->product_line;
                    $data['divisi'] =  $product->divisi;
                    $data['category'] =  $product->sub_product;
                    $data['id_budget_product'] = $product->id;
    
                }

                $data['budget_product_code'] = $loop_data['CODE'];
                $data['budget_product_name'] = $loop_data['PRODUCT'];

   

                $data['B_01'] = (isset($loop_data['B_01']) ? str_replace(['.',','],'', $loop_data['B_01']) : 0);
                $data['B_02'] = (isset($loop_data['B_02']) ? str_replace(['.',','],'', $loop_data['B_02']) : 0);
                $data['B_03'] = (isset($loop_data['B_03']) ? str_replace(['.',','],'', $loop_data['B_03']) : 0);
                $data['B_04'] = (isset($loop_data['B_04']) ? str_replace(['.',','],'', $loop_data['B_04']) : 0);
                $data['B_05'] = (isset($loop_data['B_05']) ? str_replace(['.',','],'', $loop_data['B_05']) : 0);
                $data['B_06'] = (isset($loop_data['B_06']) ? str_replace(['.',','],'', $loop_data['B_06']) : 0);
                $data['B_07'] = (isset($loop_data['B_07']) ? str_replace(['.',','],'', $loop_data['B_07']) : 0);
                $data['B_08'] = (isset($loop_data['B_08']) ? str_replace(['.',','],'', $loop_data['B_08']) : 0);
                $data['B_09'] = (isset($loop_data['B_09']) ? str_replace(['.',','],'', $loop_data['B_09']) : 0);
                $data['B_10'] = (isset($loop_data['B_10']) ? str_replace(['.',','],'', $loop_data['B_10']) : 0);
                $data['B_11'] = (isset($loop_data['B_11']) ? str_replace(['.',','],'', $loop_data['B_11']) : 0);
                $data['B_12'] = (isset($loop_data['B_12']) ? str_replace(['.',','],'', $loop_data['B_12']) : 0);
               

                $data['update_at'] = date('Y-m-d H:i:s');
                $data['update_by'] = user('nama');

                $arr   = [
                    'select'    => 'a.*',
                    'where'     => [
                        'a.budget_product_code' => $loop_data['CODE'],
                		'a.tahun' => $tahun,
                    ],
                ];

                $arr2   = [
                    'select'    => 'a.*',
                    'where'     => [
                        'a.product_code' => $loop_data['CODE'],
                		'a.tahun' => $tahun,
                    ],
                ];

                $cek = get_data($table . ' a',$arr)->row();
                $cek2 = get_data($table2 . ' a',$arr2)->row();
                $cek3 = get_data($table3 . ' a',$arr2)->row();

                $data_prod = [
                    'tahun' => $data['tahun'],
                    'id_product' =>   $data['id_budget_product'],
                    'product_code' =>  $data['budget_product_code'],
                    'id_cost_centre' =>(isset($product->id_cost_centre) ? $product->id_cost_centre : 0),
                    'product_qty' => $data['B_01']+$data['B_02']+$data['B_03']+$data['B_04']+$data['B_05']+$data['B_06']+$data['B_07']+$data['B_08']+$data['B_09']+$data['B_10']+$data['B_11']+$data['B_12'],
                    'qty_production' => $data['B_01']+$data['B_02']+$data['B_03']+$data['B_04']+$data['B_05']+$data['B_06']+$data['B_07']+$data['B_08']+$data['B_09']+$data['B_10']+$data['B_11']+$data['B_12'],
                 ] ;


                if(isset($cek->budget_product_code)) {	
                    
                    $save = update_data($table, $data, [
                        'id' => $cek->id
                    ]);

                    $this->db->set('total_budget', '(B_01+B_02+B_03+B_04+B_05+B_06+B_07+B_08+B_09+B_10+B_11+B_12)', FALSE);
                    $this->db->where('id', $cek->id);
                    $this->db->update($table);

                    if($save){
                        $data_imported++;
                    };
                }

                $data_qc = $data_prod;
                $data_ovh = $data_prod;

                unset($data_qc['qty_production']);
                unset($data_ovh['product_qty']);

                if(isset($cek2->product_code)) {	
                    $save2 = update_data($table2, $data_qc, [
                        'id' => $cek2->id
                    ]);
                }else{
                    $save2 = insert_data($table2, $data_qc);
                }

                if(isset($cek3->product_code)) {	
                    $save3 = update_data($table3, $data_ovh, [
                        'id' => $cek3->id
                    ]);
                }else{
                    $save3 = insert_data($table3, $data_ovh);
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