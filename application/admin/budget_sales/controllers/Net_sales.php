<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');

class Net_sales extends BE_Controller {

    var $controller = 'net_sales';
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

        $table = 'tbl_budget_netsales_' . $tahun; 
        $table2 = 'tbl_budget_netsales_' . ($tahun -1) ;


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
                'select' => 'a.*,b.product_name,b.code,b.destination, c.abbreviation as initial, c.cost_centre,
                            CASE
                                WHEN a.budget_product_sector = 1 THEN "REGULER"
                                WHEN a.budget_product_sector = 2 THEN "BPJS"
                                WHEN a.budget_product_sector = 3 THEN "INHEALTH"
                                WHEN a.budget_product_sector = 4 THEN "DPHO"
                                ELSE "SPECIAL PRICE"
                            END as segment,
                            (d.total_budget) as last_total_budget,
                            (d.total_est) as last_total_est',
                'join' =>  ['tbl_fact_product b on a.budget_product_code = b.code',
                            'tbl_fact_cost_centre c on b.id_cost_centre = c.id type LEFT',
                            $table2 . ' d on a.budget_product_code = d.budget_product_code and a.budget_product_sector = d.budget_product_sector and a.divisi = d.divisi type LEFT',

                           ],
                'where' => [
                    'a.tahun' => $tahun,
                    'a.product_line' =>$m0->product_line,
                    // 'a.budget_product_sector' => $sector
                ],
                'sort_by' => 'a.category,a.budget_product_code,a.budget_product_sector'
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
            'table'		=> $this->load->view('budget_sales/net_sales/table',$data,true),
            'table2'		=> $this->load->view('budget_sales/net_sales/table2',$data,true),
            'table3'		=> $this->load->view('budget_sales/net_sales/table3',$data,true),
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
        $table = 'tbl_budget_grsales_'.post('tahun');
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
            'title' => 'template budget netsales',
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
        $table = 'tbl_budget_netsales_' .$tahun;
		$file = post('fileimport');
        $filter = post();


        if($filter['tab'] == '#result2'){
            $col = ['PRODUCT', 'CODE', 'SECTOR', 'TOTAL'];
        }else{
            $response = [
                'status' => 'failed',
                'message' => 'Tidak bisa Import budget'
            ];

            render($response,'json');
            die;
        }       

        // $col = ['product','code','sector','est_1']

        $this->load->library('simpleexcel');
		$this->simpleexcel->define_column($col);
		$count_import = $this->simpleexcel->read($file);
        // $loop_data = $this->simpleexcel->parsing(0,326);

        // debug($loop_data);die;
        $data_imported = 0;

        // $totalData = array();
  
            for ($i=2; $i <= $count_import[0]; $i++) { 
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
                if($filter['tab'] == "#result2"){
                    $data['total_budget'] = (isset($loop_data['TOTAL']) ? str_replace(['.',','],'',$loop_data['TOTAL']) : 0);
                } 
                

            
                $data['update_at'] = date('Y-m-d H:i:s');
                $data['update_by'] = user('nama');

                $arr   = [
                    'select'    => 'a.*',
                    'where'     => [
                        'a.tahun' => $tahun,
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