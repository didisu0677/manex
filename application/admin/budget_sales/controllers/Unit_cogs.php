<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');

class Unit_cogs extends BE_Controller {

    var $controller = 'unit_cogs';
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
        switch_database('budget_ho');
        $arr = [
            'select' => 'distinct a.kode as bisunit,a.divisi as divisi',
            'where' => [
                // 'a.status' => 1,
                // 'a.bisunit' => ['CI','TM','EXPORT'],
            ],
        ];

        if(user('id_group')==30) {
            $arr['where']['a.kode'] = user('divisi');
        }else{
            $arr['where']['a.kode'] = ['CI','TM','EXPORT'];
        }


        $data['divisi'] = get_data('tbl_divisi a', $arr)->result(); 


        switch_database();
        $data['sector'] = get_data('tbl_sector_price', 'is_active',1)->result_array();
        $access         = get_access($this->controller);
        $data['access_additional']  = $access['access_additional'];
        render($data);
	}

 

    function data($tahun="",$divisi="",$category="",$sector="",$tipe = 'table'){
        ini_set('memory_limit', '-1');

        $table = 'tbl_budget_unitcogs_' . $tahun; 

        $arr            = [
	        'select'	=> 'distinct a.product_line,a.sub_product',
	        'where'     => [
	            'a.is_active' => 1,
                'a.product_line !=' => '',
	        ],
	    ];

        if($divisi != 'ALL'){
            if($divisi && $divisi != 'EXPORT') {
                $arr['where']['a.divisi'] = $divisi; 
                $arr['where']['a.destination'] = 'DOM'; 
            }else{
                $arr['where']['a.destination'] = 'EXP'; 
            }
        }

        if($category && $category != 'ALL') {
            $arr['where']['a.product_line'] = $category; 
        } else {
            $sub_product = getSubAccountByUser(user('id'));
            if(count($sub_product ?? [])) $arr['where']['a.product_line'] = $sub_product; 
        }

        $data['grup'][0]= get_data('tbl_fact_product a',$arr)->result();

        // debug($data['grup'][0]);die;

        foreach($data['grup'][0] as $m0) {	

            $arr = [
                'select' => 'a.*',
                'where' => [
                    'a.is_active' => 1,
                    'a.product_line' => $m0->product_line,
                ],
                'sort_by' => 'a.id_cost_centre'
            ];
            
            if($divisi != 'ALL'){
                if($divisi && $divisi != 'EXPORT') {
                    // $arr['where']['a.divisi'] = $divisi; 
                    $arr['where']['a.destination'] = 'DOM'; 
                }else{
                    $arr['where']['a.destination'] = 'EXP'; 
                }
            }


            $cproduk = get_data('tbl_fact_product a',$arr)->result();

            
            
            // foreach($cproduk as $p) {   
            //     $cek = get_data($table . ' a',[
            //         'select' => 'a.*',
            //         'where' => [
            //             'a.tahun' => $tahun,
            //             'a.budget_product_code' => $p->code,
            //             'a.product_line' => $p->product_line,
            //             'a.budget_product_sector' => $sector,
            //         ]
            //     ])->row();
            //     if(!isset($cek->id)){
            //         insert_data($table,
            //         ['tahun' => $tahun, 'divisi' => $divisi, 'product_line' => $p->product_line, 'id_budget_product'=>$p->id, 'budget_product_code'=>$p->code, 
            //         'budget_product_name' => $p->product_name, 'category' => $p->sub_product,'budget_product_sector'=>$sector]
            //     );
            //     }
            // }

            $arr = [
                'select' => 'a.*,b.product_name,b.code,,b.destination, c.abbreviation as initial, c.cost_centre,
                            CASE
                                WHEN a.budget_product_sector = 1 THEN "REGULER"
                                WHEN a.budget_product_sector = 2 THEN "BPJS"
                                WHEN a.budget_product_sector = 3 THEN "INHEALTH"
                                WHEN a.budget_product_sector = 4 THEN "DPHO"
                                ELSE "SPECIAL PRICE"
                            END as segment',
                'join' =>  ['tbl_fact_product b on a.budget_product_code = b.code',
                            'tbl_fact_cost_centre c on b.id_cost_centre = c.id type LEFT',
                           ],
                'where' => [
                    'a.tahun' => $tahun,
                    'a.product_line' =>$m0->product_line,
                    // 'a.budget_product_sector' => $sector
 
                ],
                'sort_by' => 'a.category'
            ];

            if($divisi != 'ALL'){
                if($divisi && $divisi != 'EXPORT') {
                    // $arr['where']['a.divisi'] = $divisi; 
                    $arr['where']['b.destination'] = 'DOM'; 
                }else{
                    $arr['where']['b.destination'] = 'EXP'; 
                }
            }

            if($sector != 'ALL') {
                $arr['where']['a.budget_product_sector'] = $sector;
            }

            $arr['sort_by'] = 'a.budget_product_code,a.budget_product_sector';

            $data['produk'][$m0->product_line]= get_data($table . ' a',$arr)->result();

        }

        $response	= array(
            'table'		=> $this->load->view('budget_sales/unit_cogs/table',$data,true),
            'table2'		=> $this->load->view('budget_sales/unit_cogs/table2',$data,true),
            'table3'		=> $this->load->view('budget_sales/unit_cogs/table3',$data,true),
        );
	   
	    render($response,'json');
    }

    
    function get_subaccount(){

        if(user('id_group') == GROUP_SALES_MARKETING){
            $sub_product = getSubAccountByUser(user('id'));
        } else {
            $sub_product = $sub_product = json_decode(user('sub_product'));
        }

		$divisi = post('divisi');
        switch_database('budget_ho');
        if($divisi != 'EXPORT') {
            $arr =  [
                'where' => [
                    'bisunit' => $divisi,
                    'parent_id' => 0,
                    'status' => 1
                ]
            ];
            
            if(count($sub_product ?? [])) $arr['where']['subaccount_code'] = $sub_product;
            $r = get_data('tbl_subaccount',$arr)->result_array(); 
        } else {
            switch_database();
            $subexp = [];
            $exp = get_data('tbl_fact_product',[
                'select' => 'product_line',
                'where' => [
                    'destination' => 'EXP'
                ],
                'group_by' => 'product_line'
            ])->result();
            foreach($exp as $e) {
                $subexp[] = $e->product_line;
            }

            switch_database('budget_ho');

            $arr = [
                    'where' => [
                        'subaccount_code' => $subexp,
                        'parent_id' => 0,
                        'status' => 1
                    ]
                ];

                if(count($sub_product ?? [])) $arr['where']['subaccount_code'] = $sub_product;

            $r = get_data('tbl_subaccount', $arr)->result_array(); 
        }


        $res['sub_acc'] = $r;
        switch_database();
		render($res['sub_acc'], 'json');
	}

    function save_perubahan() {       

        // $table = 'tbl_fact_lstbudget_' . user('tahun_budget');
        $table = 'tbl_budget_unitcogs_'.post('tahun');
        $data   = json_decode(post('json'),true);

        foreach($data as $id => $record) {
            $result = $record;
            foreach ($result as $r => $v) {               
                update_data($table, $result,'id',$id);

                $upd = get_data($table, 'id',$id)->row();
                $field = '';
                $total = 0;
                for ($i = 1; $i <= 12; $i++) { 
                    $field = 'B_' . sprintf('%02d', $i);
                    $total += $upd->$field ;
                }
                update_data($table,['total_budget' => $total],'id',$upd->id);
            }        
        }
    }

    function template() {
        ini_set('memory_limit', '-1');

        $tahun = get('tahun');
        $data = get_data('tbl_budget_unitcogs_'.$tahun)->result_array();
    
        $arr = [
            'id' => 'id',
            // 'tahun' => 'tahun',
            'divisi' => 'divisi',
            'category' => 'category',
            'budget_product_code' => 'code',
            'budget_product_name' => 'product',
            'budget_product_sector' => 'sector',
            'B_01' => 'january',
            'B_02' => 'february',
            'B_03' => 'march',
            'B_04' => 'april',
            'B_05' => 'may',
            'B_06' => 'june',
            'B_07' => 'july',
            'B_08' => 'august',
            'B_09' => 'september',
            'B_10' => 'october',
            'B_11' => 'november',
            'B_12' => 'december',
        ];
    
        // Inisialisasi array kosong untuk menyimpan semua data
        $all_data = [];
    
        foreach ($data as $item) {
            // Menambahkan setiap data ke dalam array all_data
            $all_data[] = $item;
        }
    
        $config = [
            'title' => 'template budget pricelist',
            'header' => $arr,
            'data' => $all_data, // Menggunakan semua data yang sudah dikumpulkan
        ];
    
        $this->load->library('simpleexcel', $config);
        $this->simpleexcel->export();
    }
    

    function import() {
		ini_set('memory_limit', '-1');
        ini_set('max_execution_time', -1);

        $tahun = post('tahun');
        $table = 'tbl_budget_unitcogs_' .$tahun;
		$file = post('fileimport');
        $filter = post();

        if($filter['tab'] == '#result2'){
            $col = ['PRODUCT', 'CODE', 'SECTOR', 'JANUARY','FEBRUARY', 'MARCH', 'APRIL', 'MAY', 'JUNE', 'JULY', 'AUGUST', 'SEPTEMBER', 'OCTOBER', 'NOVEMBER', 'DECEMBER'];
        } elseif($filter['tab'] == '#result') {
            $col = ['PRODUCT', 'CODE', 'SECTOR', 'EST_01', 'EST_02', 'EST_03', 'EST_04', 'EST_05', 'EST_06', 'EST_07', 'EST_08', 'EST_09', 'EST_10', 'EST_11', 'EST_12'];
        } elseif($filter['tab'] == '#result3'){
            $col = ['PRODUCT', 'CODE', 'SECTOR', 'THN_Y0','THN_Y1' , 'THN_01', 'THN_02', 'THN_03', 'THN_04', 'THN_05', 'THN_06', 'THN_07', 'THN_08', 'THN_09', 'THN_10'];
        }
        // $col = ['product','code','sector','est_1']

        $this->load->library('simpleexcel');
		$this->simpleexcel->define_column($col);
		$count_import = $this->simpleexcel->read($file);
        // $loop_data = $this->simpleexcel->parsing(0,326);

        // debug($loop_data);die;
        $data_imported = 0;

        // $totalData = array();
  
            for ($i=10; $i <= $count_import[0]; $i++) { 
                $loop_data = $this->simpleexcel->parsing(0,$i+1);
                
                // debug($loop_data);

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

                switch ($loop_data['SECTOR']) {
                    case "REGULER":
                    //code block
                    $sector = 1;
                    break;
                    case "BPJS":
                        $sector = 2;
                    //code block;
                    break;
                    case "INHEALTH":
                        $sector = 3;
                    //code block
                    break;
                    case "DPHO":
                        $sector = 4;
                    //code block
                    break;
                    case "SPECIAL PRICE":
                        $sector = 5;
                    //code block
                    break;
                    default:
                    //code block
                }

                $data['budget_product_sector'] = $sector;
                if($filter['tab'] == "#result"){
                    $data['EST_01'] = (isset($loop_data['EST_01']) ? str_replace(['.',','],'',$loop_data['EST_01']) : 0);
                    $data['EST_02'] = (isset($loop_data['EST_02']) ? str_replace(['.',','],'',$loop_data['EST_02']) : 0);
                    $data['EST_03'] = (isset($loop_data['EST_03']) ? str_replace(['.',','],'',$loop_data['EST_03']) : 0);
                    $data['EST_04'] = (isset($loop_data['EST_04']) ? str_replace(['.',','],'',$loop_data['EST_04']) : 0);
                    $data['EST_05'] = (isset($loop_data['EST_05']) ? str_replace(['.',','],'',$loop_data['EST_05']) : 0);
                    $data['EST_06'] = (isset($loop_data['EST_06']) ? str_replace(['.',','],'',$loop_data['EST_06']) : 0);
                    $data['EST_07'] = (isset($loop_data['EST_07']) ? str_replace(['.',','],'',$loop_data['EST_07']) : 0);
                    $data['EST_08'] = (isset($loop_data['EST_08']) ? str_replace(['.',','],'',$loop_data['EST_08']) : 0);
                    $data['EST_09'] = (isset($loop_data['EST_09']) ? str_replace(['.',','],'',$loop_data['EST_09']) : 0);
                    $data['EST_10'] = (isset($loop_data['EST_10']) ? str_replace(['.',','],'',$loop_data['EST_10']) : 0);
                    $data['EST_11'] = (isset($loop_data['EST_11']) ? str_replace(['.',','],'',$loop_data['EST_11']) : 0);
                    $data['EST_12'] = (isset($loop_data['EST_12']) ? str_replace(['.',','],'',$loop_data['EST_12']) : 0);

                    $field = "" ;
                    for ($j = 1; $j <= setting('actual_budget'); $j++) {
                        $field = 'EST_' . sprintf('%02d', $j);
                        unset($data[$field]) ;                    
                    }
                    
                } else if($filter['tab'] == '#result2'){
                    $data['B_01'] = (isset($loop_data['JANUARY']) ? str_replace(['.',','],'', $loop_data['JANUARY']) : 0);
                    $data['B_02'] = (isset($loop_data['FEBRUARY']) ? str_replace(['.',','],'',$loop_data['FEBRUARY']) : 0);
                    $data['B_03'] = (isset($loop_data['MARCH']) ? str_replace(['.',','],'',$loop_data['MARCH']) : 0);
                    $data['B_04'] = (isset($loop_data['APRIL']) ? str_replace(['.',','],'',$loop_data['APRIL']) : 0);
                    $data['B_05'] = (isset($loop_data['MAY']) ? str_replace(['.',','],'',$loop_data['MAY']) : 0);
                    $data['B_06'] = (isset($loop_data['JUNE']) ? str_replace(['.',','],'',$loop_data['JUNE']) : 0);
                    $data['B_07'] = (isset($loop_data['JULY']) ? str_replace(['.',','],'',$loop_data['JULY']) : 0);
                    $data['B_08'] = (isset($loop_data['AUGUST']) ? str_replace(['.',','],'',$loop_data['AUGUST']) : 0);
                    $data['B_09'] = (isset($loop_data['SEPTEMBER']) ? str_replace(['.',','],'',$loop_data['SEPTEMBER']) : 0);
                    $data['B_10'] = (isset($loop_data['OCTOBER']) ? str_replace(['.',','],'',$loop_data['OCTOBER']) : 0);
                    $data['B_11'] = (isset($loop_data['NOVEMBER']) ? str_replace(['.',','],'',$loop_data['NOVEMBER']) : 0);
                    $data['B_12'] = (isset($loop_data['DECEMBER']) ? str_replace(['.',','],'',$loop_data['DECEMBER']) : 0);
                } else if($filter['tab'] == '#result3'){
                    $data['THN_01'] = (isset($loop_data['THN_01']) ? str_replace(['.',','],'',$loop_data['THN_01']) : 0);
                    $data['THN_02'] = (isset($loop_data['THN_02']) ? str_replace(['.',','],'',$loop_data['THN_02']) : 0);
                    $data['THN_03'] = (isset($loop_data['THN_03']) ? str_replace(['.',','],'',$loop_data['THN_03']) : 0);
                    $data['THN_04'] = (isset($loop_data['THN_04']) ? str_replace(['.',','],'',$loop_data['THN_04']) : 0);
                    $data['THN_05'] = (isset($loop_data['THN_05']) ? str_replace(['.',','],'',$loop_data['THN_05']) : 0);
                    $data['THN_06'] = (isset($loop_data['THN_06']) ? str_replace(['.',','],'',$loop_data['THN_06']) : 0);
                    $data['THN_07'] = (isset($loop_data['THN_07']) ? str_replace(['.',','],'',$loop_data['THN_07']) : 0);
                    $data['THN_08'] = (isset($loop_data['THN_08']) ? str_replace(['.',','],'',$loop_data['THN_08']) : 0);
                    $data['THN_09'] = (isset($loop_data['THN_09']) ? str_replace(['.',','],'',$loop_data['THN_09']) : 0);
                    $data['THN_10'] = (isset($loop_data['THN_10']) ? str_replace(['.',','],'',$loop_data['THN_10']) : 0);
                }


                $data['update_at'] = date('Y-m-d H:i:s');
                $data['update_by'] = user('nama');

                $arr   = [
                    'select'    => 'a.*',
                    'where'     => [
                        'a.budget_product_code' => $loop_data['CODE'],
                		'a.budget_product_sector' => $sector,
                    ],
                ];
                $cek = get_data($table . ' a',$arr)->row();

                if(isset($cek->budget_product_code)) {	
                    
                    $save = update_data($table, $data, [
                        'id' => $cek->id
                    ]);

                    if($save){
                        $data_imported++;
                    };
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