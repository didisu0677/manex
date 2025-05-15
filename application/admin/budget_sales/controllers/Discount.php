<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');

class Discount extends BE_Controller {
    var $controller = 'discount';

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

        $table = 'tbl_budget_discount_' . $tahun; 

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

            $arr            = [
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
                    $arr['where']['a.divisi'] = $divisi; 
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
            'table'		=> $this->load->view('budget_sales/discount/table',$data,true),
            'table2'		=> $this->load->view('budget_sales/discount/table2',$data,true),
            'table3'		=> $this->load->view('budget_sales/discount/table3',$data,true),
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
        $table = 'tbl_budget_discount_'.post('tahun');
        $data   = json_decode(post('json'),true);

        foreach($data as $id => $record) {
            $result = $record;
            foreach ($result as $r => $v) {               
                update_data($table, $result,'id',$id);

                $upd = get_data($table, 'id',$id)->row();
                $field = '';
                $total = 0;
                $fieldest = '';
                $totalest = '';
                for ($i = 1; $i <= 12; $i++) { 
                    $field = 'B_' . sprintf('%02d', $i);
                    $fieldest = 'EST_' . sprintf('%02d', $i);
                    $total += $upd->$field ;
                    $totalest += $upd->$fieldest ;
                }
                update_data($table,['total_budget' => $total, 'total_est' => $totalest],'id',$upd->id);
                $this->recalculate_sales(post('tahun'),$upd->budget_product_code,$upd->budget_product_sector);
            }        
        }
    }

    function template() {
        ini_set('memory_limit', '-1');

        $tahun = get('tahun');
        $data = get_data('tbl_budget_pricelist_'.$tahun)->result_array();
    
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
        $table = 'tbl_budget_discount_' .$tahun;
		$file = post('fileimport');
        $filter = post();

        if($filter['tab'] == '#result2'){
            $col = ['PRODUCT', 'CODE', 'SECTOR', 'JANUARY','FEBRUARY', 'MARCH', 'APRIL', 'MAY', 'JUNE', 'JULY', 'AUGUST', 'SEPTEMBER', 'OCTOBER', 'NOVEMBER', 'DECEMBER'];
        } elseif($filter['tab'] == '#result') {
            $col = ['PRODUCT', 'CODE', 'SECTOR', 'EST_01', 'EST_02', 'EST_03', 'EST_04', 'EST_05', 'EST_06', 'EST_07', 'EST_08', 'EST_09', 'EST_10', 'EST_11', 'EST_12'];
        } elseif($filter['tab'] == '#result3'){
            $col = ['PRODUCT', 'CODE', 'SECTOR', 'THN_Y0','THN_Y1', 'THN_01', 'THN_02', 'THN_03', 'THN_04', 'THN_05', 'THN_06', 'THN_07', 'THN_08', 'THN_09', 'THN_10'];
        }
        // $col = ['product','code','sector','est_1']

        $this->load->library('simpleexcel');
		$this->simpleexcel->define_column($col);
		$count_import = $this->simpleexcel->read($file);
        // $loop_data = $this->simpleexcel->parsing(0,326);

        // debug($loop_data);die;
        $data_imported = 0;

        // $totalData = array();
  
        // $type = $loop_data = $this->simpleexcel->parsing(0,6);

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
                    
                    // if($cek->budget_product_code == 'TMTPLT12DM') {
                    //     $save = update_data($table, $data, [
                    //         'id1' => $cek->id
                    //     ]);
                    // }else{
                        $save = update_data($table, $data, [
                            'id' => $cek->id
                        ]);
                    // }

                    $this->db->set('total_budget', '(B_01+B_02+B_03+B_04+B_05+B_06+B_07+B_08+B_09+B_10+B_11+B_12)', FALSE);
                    $this->db->set('total_est', '(EST_01+EST_02+EST_03+EST_04+EST_05+EST_06+EST_07+EST_08+EST_09+EST_10+EST_11+EST_12)', FALSE);
                    $this->db->where('id', $cek->id);
                    $this->db->update($table);
                    

                    $this->recalculate_sales($tahun,$loop_data['CODE'],$sector);

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

    function recalculate_sales($tahun="",$product="",$sector="") {
        ini_set('memory_limit', '-1');

        $table1 = 'tbl_budget_pricelist_' . $tahun ;
        $table2 = 'tbl_budget_qtysales_' . $tahun ;
        $table3 = 'tbl_budget_grsales_' . $tahun ;

        $table4 = 'tbl_budget_discount_' . $tahun ;
        $table5 = 'tbl_budget_netsales_' . $tahun ;

        $sales_amount = get_data($table1 . ' a',[
            'select' => 'a.budget_product_code, 
                        (a.EST_01 * b.EST_01) as EST_01, (a.EST_02 * b.EST_02) as EST_02, (a.EST_03 * b.EST_03) as EST_03, (a.EST_04 * b.EST_04) as EST_04,
                        (a.EST_05 * b.EST_05) as EST_05, (a.EST_06 * b.EST_06) as EST_06, (a.EST_07 * b.EST_07) as EST_07, (a.EST_08 * b.EST_08) as EST_08,
                        (a.EST_09 * b.EST_09) as EST_09, (a.EST_10 * b.EST_10) as EST_10, (a.EST_11 * b.EST_11) as EST_11, (a.EST_12 * b.EST_12) as EST_12,
                        (a.B_01 * b.B_01) as B_01, (a.B_02 * b.B_02) as B_02, (a.B_03 * b.B_03) as B_03, (a.B_04 * b.B_04) as B_04,
                        (a.B_05 * b.B_05) as B_05, (a.B_06 * b.B_06) as B_06, (a.B_07 * b.B_07) as B_07, (a.B_08 * b.B_08) as B_08,
                        (a.B_09 * b.B_09) as B_09, (a.B_10 * b.B_10) as B_10, (a.B_11 * b.B_11) as B_11, (a.B_12 * b.B_12) as B_12,
                        (a.THN_01 * b.THN_01) as THN_01, (a.THN_02 * b.THN_02) as THN_02, (a.THN_03 * b.THN_03) as THN_03, (a.THN_04 * b.THN_04) as THN_04,
                        (a.THN_05 * b.THN_05) as THN_05, (a.THN_06 * b.THN_06) as THN_06, (a.THN_07 * b.THN_07) as THN_07, (a.THN_08 * b.THN_08) as THN_08,
                        (a.THN_09 * b.THN_09) as THN_09, (a.THN_10 * b.THN_10) as THN_10,

                        (a.EST_01 * b.EST_01) + c.EST_01 as nEST_01, (a.EST_02 * b.EST_02) + c.EST_02 as nEST_02, (a.EST_03 * b.EST_03) + c.EST_03 as nEST_03, (a.EST_04 * b.EST_04) + c.EST_04 as nEST_04,
                        (a.EST_05 * b.EST_05) + c.EST_05 as nEST_05, (a.EST_06 * b.EST_06) + c.EST_06 as nEST_06, (a.EST_07 * b.EST_07) + c.EST_07 as nEST_07, (a.EST_08 * b.EST_08) + c.EST_08 as nEST_08,
                        (a.EST_09 * b.EST_09) + c.EST_09 as nEST_09, (a.EST_10 * b.EST_10) + c.EST_10 as nEST_10, (a.EST_11 * b.EST_11) + c.EST_11 as nEST_11, (a.EST_12 * b.EST_12) + c.EST_12 as nEST_12,
                        
                        (a.B_01 * b.B_01) + c.B_01 as nB_01, (a.B_02 * b.B_02) + c.B_02 as nB_02, (a.B_03 * b.B_03) + c.B_03 as nB_03, (a.B_04 * b.B_04) + c.B_04 as nB_04,
                        (a.B_05 * b.B_05) + c.B_05 as nB_05, (a.B_06 * b.B_06) + c.B_06 as nB_06, (a.B_07 * b.B_07) + c.B_07 as nB_07, (a.B_08 * b.B_08) + c.B_08 as nB_08,
                        (a.B_09 * b.B_09) + c.B_09 as nB_09, (a.B_10 * b.B_10) + c.B_10 as nB_10, (a.B_11 * b.B_11) + c.B_11 as nB_11, (a.B_12 * b.B_12) + c.B_12 as nB_12,
                       
                        (a.THN_01 * b.THN_01) + c.THN_01 as nTHN_01, (a.THN_02 * b.THN_02) + c.THN_02 as nTHN_02, (a.THN_03 * b.THN_03) + c.THN_03 as nTHN_03, (a.THN_04 * b.THN_04) + c.THN_04 as nTHN_04,
                        (a.THN_05 * b.THN_05) + c.THN_05 as nTHN_05, (a.THN_06 * b.THN_06) + c.THN_06 as nTHN_06, (a.THN_07 * b.THN_07) + c.THN_07 as nTHN_07, (a.THN_08 * b.THN_08) + c.THN_08 as nTHN_08,
                        (a.THN_09 * b.THN_09) + c.THN_09 as nTHN_09, (a.THN_10 * b.THN_10) + c.THN_10 as nTHN_10'
                        ,          
            'join'   => [$table2 . ' b on a.budget_product_code = b.budget_product_code and a.budget_product_sector = b.budget_product_sector',
                         $table4 . ' c on a.budget_product_code = c.budget_product_code and a.budget_product_sector = c.budget_product_sector'
                        ],
            'where'  => [
                'a.tahun' => $tahun,
                'a.budget_product_code' => $product,
                'a.budget_product_sector' => $sector, 
            ],
        ])->row();
        if(isset($sales_amount->budget_product_code)) {
             update_data($table3,
                ['EST_01' => $sales_amount->EST_01,'EST_02' => $sales_amount->EST_02,'EST_03' => $sales_amount->EST_03,'EST_04' => $sales_amount->EST_04,
                'EST_05' => $sales_amount->EST_05,'EST_06' => $sales_amount->EST_06,'EST_07' => $sales_amount->EST_07,'EST_08' => $sales_amount->EST_08,
                'EST_09' => $sales_amount->EST_09,'EST_10' => $sales_amount->EST_10,'EST_11' => $sales_amount->EST_11,'EST_12' => $sales_amount->EST_12,
                'B_01' => $sales_amount->B_01,'B_02' => $sales_amount->B_02,'B_03' => $sales_amount->B_03,'B_04' => $sales_amount->B_04,
                'B_05' => $sales_amount->B_05,'B_06' => $sales_amount->B_06,'B_07' => $sales_amount->B_07,'B_08' => $sales_amount->B_08,
                'B_09' => $sales_amount->B_09,'B_10' => $sales_amount->B_10,'B_11' => $sales_amount->B_11,'B_12' => $sales_amount->B_12,
                'THN_01' => $sales_amount->THN_01,'THN_02' => $sales_amount->THN_02,'THN_03' => $sales_amount->THN_03,'THN_04' => $sales_amount->THN_04,
                'THN_05' => $sales_amount->THN_05,'THN_06' => $sales_amount->THN_06,'THN_07' => $sales_amount->THN_07,'THN_08' => $sales_amount->THN_08,
                'THN_09' => $sales_amount->THN_09,'THN_10' => $sales_amount->THN_10
                ],
                ['budget_product_code'=>$product,'tahun'=>$tahun,'budget_product_sector'=>$sector]);
                
            update_data($table5,
                ['EST_01' => $sales_amount->nEST_01,'EST_02' => $sales_amount->nEST_02,'EST_03' => $sales_amount->nEST_03,'EST_04' => $sales_amount->nEST_04,
                'EST_05' => $sales_amount->nEST_05,'EST_06' => $sales_amount->nEST_06,'EST_07' => $sales_amount->nEST_07,'EST_08' => $sales_amount->nEST_08,
                'EST_09' => $sales_amount->nEST_09,'EST_10' => $sales_amount->nEST_10,'EST_11' => $sales_amount->nEST_11,'EST_12' => $sales_amount->nEST_12,
                'B_01' => $sales_amount->nB_01,'B_02' => $sales_amount->nB_02,'B_03' => $sales_amount->nB_03,'B_04' => $sales_amount->nB_04,
                'B_05' => $sales_amount->nB_05,'B_06' => $sales_amount->nB_06,'B_07' => $sales_amount->nB_07,'B_08' => $sales_amount->nB_08,
                'B_09' => $sales_amount->nB_09,'B_10' => $sales_amount->nB_10,'B_11' => $sales_amount->nB_11,'B_12' => $sales_amount->nB_12,
                'THN_01' => $sales_amount->nTHN_01,'THN_02' => $sales_amount->nTHN_02,'THN_03' => $sales_amount->nTHN_03,'THN_04' => $sales_amount->nTHN_04,
                'THN_05' => $sales_amount->nTHN_05,'THN_06' => $sales_amount->nTHN_06,'THN_07' => $sales_amount->nTHN_07,'THN_08' => $sales_amount->nTHN_08,
                'THN_09' => $sales_amount->nTHN_09,'THN_10' => $sales_amount->nTHN_10
                ],
                ['budget_product_code'=>$product,'tahun'=>$tahun,'budget_product_sector'=>$sector]);
            }

            if($sector == 1) {
				$budget_product_sector = "REGULER";
			}elseif($sector == 2){
				$budget_product_sector = "E-CATALOG";
			}elseif($sector == 3) {
				$budget_product_sector = "IN-HEALTH";
			}elseif($sector == 4) {
				$budget_product_sector = "ASKES";
			}elseif($sector == 5) {
				$budget_product_sector = "HARGA KHUSUS";
			}else{
				$budget_product_sector = "";
			}
            
            $actual = get_data('tbl_actual_gross_profit',[
                'select' => 'product_code,bulan,sector,sum(qty_sales) as qty_sales, sum(sales_amount) as sales_amount,
                             sum(discount) as discount, sum(cogs) as cogs',
                'where' => [
                    'tahun' => $tahun,
                    'product_code' => 'product',
                    'sector' => $budget_product_sector,
                ],
                'group_by' => 'product_code,bulan,sector',
                ])->result();

           foreach($actual as $a) {

                switch ($a->bulan) {
                    case "01"; 
                        $qB_01 = ($a->qty_sales != 0 ? $a->qty_sales : 0);
                        $pB_01 = ($a->qty_sales != 0 ? $a->sales_amount / $a->qty_sales : 0);
                        $dB_01 = ($a->discount != 0 ? $a->discount : 0);
                        $cB_01 = ($a->cogs != 0 ? $a->cogs : 0);
                        $uB_01 = (($a->qty_sales != 0 && $a->cogs != 0) ? $a->cogs / $a->qty_sales : 0);
                        $gB_01 = $a->sales_amount ;
                        $nB_01 = ($a->sales_amount + $a->discount);
                        break;
                    case "02":
                        $qB_02 = ($a->qty_sales != 0 ? $a->qty_sales : 0);
                        $pB_02 = ($a->qty_sales != 0 ? $a->sales_amount / $a->qty_sales : 0);
                        $dB_02 = ($a->discount != 0 ? $a->discount : 0);
                        $cB_02 = ($a->cogs != 0 ? $a->cogs : 0);
                        $uB_02 = (($a->qty_sales != 0 && $a->cogs != 0) ? $a->cogs / $a->qty_sales : 0);
                        $gB_02 = $a->sales_amount ;
                        $nB_02 = ($a->sales_amount + $a->discount);
                        break;
                    case "03":
                        $qB_03 = ($a->qty_sales != 0 ? $a->qty_sales : 0);
                        $pB_03 = ($a->qty_sales != 0 ? $a->sales_amount / $a->qty_sales : 0);
                        $dB_03 = ($a->discount != 0 ? $a->discount : 0);
                        $cB_03 = ($a->cogs != 0 ? $a->cogs : 0);
                        $uB_03 = (($a->qty_sales != 0 && $a->cogs != 0) ? $a->cogs / $a->qty_sales : 0);
                        $gB_03 = $a->sales_amount ;
                        $nB_03 = ($a->sales_amount + $a->discount);
                        break;
                    case "04";
                        $qB_04 = ($a->qty_sales != 0 ? $a->qty_sales : 0);
                        $pB_04 = ($a->qty_sales != 0 ? $a->sales_amount / $a->qty_sales : 0);
                        $dB_04 = ($a->discount != 0 ? $a->discount  : 0);
                        $cB_04 = ($a->cogs != 0 ? $a->cogs : 0);
                        $uB_04 = (($a->qty_sales != 0 && $a->cogs != 0) ? $a->cogs / $a->qty_sales : 0);
                        $gB_04 = $a->sales_amount ;
                        $nB_04 = ($a->sales_amount + $a->discount);
                        break;
                    case "05":
                        $qB_05 = ($a->qty_sales != 0 ? $a->qty_sales : 0);
                        $pB_05 = ($a->qty_sales != 0 ? $a->sales_amount / $a->qty_sales : 0);
                        $dB_05 = ($a->discount != 0 ? $a->discount : 0);
                        $cB_05 = ($a->cogs != 0 ? $a->cogs : 0);
                        $uB_05 = (($a->qty_sales != 0 && $a->cogs != 0) ? $a->cogs / $a->qty_sales : 0);
                        $gB_05 = $a->sales_amount ;
                        $nB_05 = ($a->sales_amount + $a->discount);
                        break;
                    case "06":
                        $qB_06 = ($a->qty_sales != 0 ? $a->qty_sales : 0);
                        $pB_06 = ($a->qty_sales != 0 ? $a->sales_amount / $a->qty_sales : 0);
                        $dB_06 = ($a->discount != 0 ? $a->discount  : 0);
                        $cB_06 = ($a->cogs != 0 ? $a->cogs : 0);
                        $uB_06 = (($a->qty_sales != 0 && $a->cogs != 0) ? $a->cogs / $a->qty_sales : 0);
                        $gB_06 = $a->sales_amount ;
                        $nB_06 = ($a->sales_amount + $a->discount);
                        break;
                    case "07";
                        $qB_07 = ($a->qty_sales != 0 ? $a->qty_sales : 0);
                        $pB_07 = ($a->qty_sales != 0 ? $a->sales_amount / $a->qty_sales : 0);
                        $dB_07 = ($a->discount != 0 ? $a->discount : 0);
                        $cB_07 = ($a->cogs != 0 ? $a->cogs : 0);
                        $uB_07 = (($a->qty_sales != 0 && $a->cogs != 0) ? $a->cogs / $a->qty_sales : 0);
                        $gB_07 = $a->sales_amount ;
                        $nB_07 = ($a->sales_amount + $a->discount);
                        break;
                    case "08":
                        $qB_08 = ($a->qty_sales != 0 ? $a->qty_sales : 0);
                        $pB_08 = ($a->qty_sales != 0 ? $a->sales_amount / $a->qty_sales : 0);
                        $dB_08 = ($a->discount != 0 ? $a->discount : 0);
                        $cB_08 = ($a->cogs != 0 ? $a->cogs : 0);
                        $uB_08 = (($a->qty_sales != 0 && $a->cogs != 0) ? $a->cogs / $a->qty_sales : 0);
                        $gB_08 = $a->sales_amount ;
                        $nB_08 = ($a->sales_amount + $a->discount);
                        break;
                    case "09":
                        $qB_09 = ($a->qty_sales != 0 ? $a->qty_sales : 0);
                        $pB_09 = ($a->qty_sales != 0 ? $a->sales_amount / $a->qty_sales : 0);
                        $dB_09 = ($a->discount != 0 ? $a->discount : 0);
                        $cB_09 = ($a->cogs != 0 ? $a->cogs : 0);
                        $uB_09 = (($a->qty_sales != 0 && $a->cogs != 0) ? $a->cogs / $a->qty_sales : 0);
                        $gB_09 = $a->sales_amount ;
                        $nB_09 = ($a->sales_amount + $a->discount);
                        break;
                    case "10":
                        $qB_10 = ($a->qty_sales != 0 ? $a->qty_sales : 0);
                        $pB_10 = ($a->qty_sales != 0 ? $a->sales_amount / $a->qty_sales : 0);
                        $dB_10 = ($a->discount != 0 ? $a->discount  : 0);
                        $cB_10 = ($a->cogs != 0 ? $a->cogs : 0);
                        $uB_10 = (($a->qty_sales != 0 && $a->cogs != 0) ? $a->cogs / $a->qty_sales : 0);
                        $gB_10 = $a->sales_amount ;
                        $nB_10 = ($a->sales_amount + $a->discount);
                        break;
                    case "11":
                        $qB_11 = ($a->qty_sales != 0 ? $a->qty_sales : 0);
                        $pB_11 = ($a->qty_sales != 0 ? $a->sales_amount / $a->qty_sales : 0);
                        $dB_11 = ($a->discount != 0 ? $a->discount : 0);
                        $cB_11 = ($a->cogs != 0 ? $a->cogs : 0);
                        $uB_11 = (($a->qty_sales != 0 && $a->cogs != 0) ? $a->cogs / $a->qty_sales : 0);
                        $gB_11 = $a->sales_amount ;
                        $nB_11 = ($a->sales_amount + $a->discount);
                    case "12":
                        $qB_12 = ($a->qty_sales != 0 ? $a->qty_sales : 0);
                        $pB_12 = ($a->qty_sales != 0 ? $a->sales_amount / $a->qty_sales : 0);
                        $dB_12 = ($a->discount != 0 ? $a->discount  : 0);
                        $cB_12 = ($a->cogs != 0 ? $a->cogs : 0);
                        $uB_12 = (($a->qty_sales != 0 && $a->cogs != 0) ? $a->cogs / $a->qty_sales : 0);
                        $gB_12 = $a->sales_amount ;
                        $nB_12 = ($a->sales_amount + $a->discount);
                        break;
                    default:
                        echo "The color is neither red, blue, nor green!";
                }

                $arr            = [
                    'select'    => 'a.*',
                    'where'     => [
                        'a.budget_product_code' => $a->product_code,
                        'a.budget_product_sector' => $budget_product_sector,
                    ],
                ];
    
                $cek = get_data($table3 . ' a',$arr)->row();

                if(isset($cek->budget_product_code)) {
                    $field1 = "" ;
                    $field = "";
                    $field = 'EST_' . sprintf('%02d', $a->bulan);
                    $field1 = 'gB_' . sprintf('%02d', $a->bulan);   

                    update_data($table3,[$field=>$$field1],['id'=>$cek->id]); 
                } 
           }
    }

}