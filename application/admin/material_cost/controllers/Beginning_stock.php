<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');

class Beginning_stock extends BE_Controller {

    var $controller = 'beginning_stock';
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

        $table = 'tbl_beginning_stock';

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


            $data['produk'][$m0->id]= get_data('tbl_beginning_stock a',[
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
            'table'		=> $this->load->view('material_cost/beginning_stock/table',$data,true),
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
        $table = 'tbl_beginning_stock';
		$file = post('fileimport');
        $filter = post();


        $col = ['PRODUCT', 'CODE', 'BATCH_SIZE', 'YIELD' , 'TOTAL_STOCK'];
 
        $this->load->library('simpleexcel');
		$this->simpleexcel->define_column($col);
		$count_import = $this->simpleexcel->read($file);
        // $loop_data = $this->simpleexcel->parsing(0,326);

        // debug($loop_data);die;
        $data_imported = 0;

        // $totalData = array();
  
            for ($i=7; $i <= $count_import[0]; $i++) { 
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
                $data['batch_size'] = (isset($loop_data['BATCH_SIZE']) ? str_replace(['.',','],'', $loop_data['BATCH_SIZE']) : 0);
                $data['yield'] = $loop_data['YIELD'];
                $data['TOTAL_STOCK'] = (isset($loop_data['TOTAL_STOCK']) ? str_replace(['.',','],'', $loop_data['TOTAL_STOCK']) : 0);
               

                $data['update_at'] = date('Y-m-d H:i:s');
                $data['update_by'] = user('nama');

                $arr   = [
                    'select'    => 'a.*',
                    'where'     => [
                        'a.budget_product_code' => $loop_data['CODE'],
                		'a.tahun' => $tahun,
                    ],
                ];

                $cek = get_data($table . ' a',$arr)->row();
                if(isset($cek->budget_product_code)) {	
                    
                    $save = update_data($table, $data, [
                        'id' => $cek->id
                    ]);

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