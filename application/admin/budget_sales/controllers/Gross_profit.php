<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');

class Gross_profit extends BE_Controller {
    var $controller = 'gross_profit';

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

        $table1 = 'tbl_budget_netsales_' . $tahun;
        $table2 = 'tbl_budget_cogs_' . $tahun; 
        $table3 = 'tbl_budget_cogsidle_' . $tahun; 
        $table4 = 'tbl_budget_cogsloss_' . $tahun; 

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

                                a.EST_01 - (f.EST_01 + d.EST_01 + e.EST_01) as EST_01,  a.EST_02 - (f.EST_02 + d.EST_02 + e.EST_02) as EST_02, a.EST_03 - (f.EST_03 + d.EST_03 + e.EST_03) as EST_03, a.EST_04 - (f.EST_04 + d.EST_04 + e.EST_04) as EST_04,
                                a.EST_05 - (f.EST_05 + d.EST_05 + e.EST_05) as EST_05,  a.EST_06 - (f.EST_06 + d.EST_06 + e.EST_06) as EST_06, a.EST_07 - (f.EST_07 + d.EST_07 + e.EST_07) as EST_07, a.EST_08 - (f.EST_08 + d.EST_08 + e.EST_08) as EST_08,
                                a.EST_09 - (f.EST_09 + d.EST_09 + e.EST_09) as EST_09,  a.EST_10 - (f.EST_10 + d.EST_10 + e.EST_10) as EST_10, a.EST_11 - (f.EST_11 + d.EST_11 + e.EST_11) as EST_11, a.EST_12 - (f.EST_12 + d.EST_12 + e.EST_12) as EST_12,
                               
                                a.B_01 - (f.B_01 + d.B_01 + e.B_01) as B_01, a.B_02 - (f.B_02 + d.B_02 + e.B_02) as B_02, a.B_03 - (f.B_03 + d.B_03 + e.B_03) as B_03, a.B_04 - (f.B_04 + d.B_04 + e.B_04) as B_04,
                                a.B_05 - (f.B_05 + d.B_05 + e.B_05) as B_05, a.B_06 - (f.B_06 + d.B_06 + e.B_06) as B_06, a.B_07 - (f.B_07 + d.B_07 + e.B_07) as B_07, a.B_08 - (f.B_08 + d.B_08 + e.B_08) as B_08,
                                a.B_09 - (f.B_09 + d.B_09 + e.B_09) as B_09, a.B_10 - (f.B_10 + d.B_10 + e.B_10) as B_10, a.B_11 - (f.B_11 + d.B_11 + e.B_11) as B_11, a.B_12 - (f.B_12 + d.B_12 + e.B_12) as B_12,
 
                                a.THN_01 - (f.THN_01 + d.THN_01 + e.THN_01) as THN_01, a.THN_02 - (f.THN_02 + d.THN_02 + e.THN_02) as THN_02, a.THN_03 - (f.THN_03 + d.THN_03 + e.THN_03) as nTHN_03, a.THN_04 - (f.THN_04 + d.THN_04 + e.THN_04) as THN_04,
                                a.THN_05 - (f.THN_05 + d.THN_05 + e.THN_05) as THN_05, a.THN_06 - (f.THN_06 + d.THN_06 + e.THN_06) as THN_06, a.THN_07 - (f.THN_07 + d.THN_07 + e.THN_07) as nTHN_07, a.THN_08 - (f.THN_08 + d.THN_08 + e.THN_08) as THN_08,
                                a.THN_09 - (f.THN_09 + d.THN_09 + e.THN_09) as THN_09, a.THN_10 - (f.THN_10 + d.THN_10 + e.THN_10) as THN_10,

                                a.total_budget - (f.total_budget+d.total_budget+e.total_budget) as total_budget,
                                a.total_est - (f.total_est+d.total_est+e.total_est) as total_est',
                                
                'join' =>  ['tbl_fact_product b on a.budget_product_code = b.code',
                            'tbl_fact_cost_centre c on b.id_cost_centre = c.id type LEFT',
                            $table2 . ' d on a.id_budget_product = d.id_budget_product and a.product_line = d.product_line and a.budget_product_code = d.budget_product_code and a.budget_product_sector = d.budget_product_sector and a.divisi = d.divisi type LEFT',
					        $table3 . ' e on a.id_budget_product = e.id_budget_product and a.product_line = e.product_line and a.budget_product_code = e.budget_product_code and a.budget_product_sector = e.budget_product_sector and a.divisi = e.divisi type LEFT',
                            $table4 .  ' f on a.id_budget_product = f.id_budget_product and a.product_line = f.product_line and a.budget_product_code = f.budget_product_code and a.budget_product_sector = f.budget_product_sector and a.divisi = f.divisi type LEFT'

                           ],
                'where' => [
                    'a.tahun' => $tahun,
                    'a.product_line' =>$m0->product_line,
                    // 'a.budget_product_sector' => $sector
                ],
                'sort_by' => 'a.budget_product_code'
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
            
            $data['produk'][$m0->product_line]= get_data($table1 . ' a',$arr)->result();

        }

        $response	= array(
            'table'		=> $this->load->view('budget_sales/gross_profit/table',$data,true),
            'table2'		=> $this->load->view('budget_sales/gross_profit/table2',$data,true),
            'table3'		=> $this->load->view('budget_sales/gross_profit/table3',$data,true),
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
        $table = 'tbl_budget_cogs_'.post('tahun');
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
            }        
        }
    }

    function template() {
        ini_set('memory_limit', '-1');

        $tahun = get('tahun');
        $data = get_data('tbl_budget_cogs_'.$tahun)->result_array();
    
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
            'title' => 'template budget cogs',
            'header' => $arr,
            'data' => $all_data, // Menggunakan semua data yang sudah dikumpulkan
        ];
    
        $this->load->library('simpleexcel', $config);
        $this->simpleexcel->export();
    }
    

    function import() {
		ini_set('memory_limit', '-1');

        $tahun = post('tahun');
        $table = 'tbl_budget_cogs_' .$tahun;
		$file = post('fileimport');

        $col = ['ID', 'DIVISI', 'CATEGORY', 'CODE', 'PRODUCT', 'SECTOR', 'JANUARY', 'FEBRUARY', 'MARCH', 'APRIL', 'MAY', 'JUNE', 'JULY', 'AUGUST', 'SEPTEMBER', 'OCTOBER', 'NOVEMBER', 'DECEMBER'];

        $this->load->library('simpleexcel');
		$this->simpleexcel->define_column($col);
		$count_import = $this->simpleexcel->read($file);

        // $loop_data = $this->simpleexcel->parsing(0,326);

        // debug($loop_data);die;

        $data_imported = 0;
        for ($i=1; $i < $count_import[0]; $i++) { 
            $loop_data = $this->simpleexcel->parsing(0,$i+1);

            $data['id'] = $loop_data['ID'];
            $data['budget_product_code'] = $loop_data['CODE'];
            $data['budget_product_name'] = $loop_data['PRODUCT'];
            $data['budget_product_sector'] = $loop_data['SECTOR'];

            $data['B_01'] = (isset($loop_data['JANUARY']) ? str_replace(['.',','],'',$loop_data['JANUARY']) : 0);
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
            $data['total_budget'] = $data['B_01'] + $data['B_02'] + $data['B_03'] + $data['B_04'] + $data['B_05'] + $data['B_06'] + $data['B_07'] + $data['B_08'] + $data['B_09'] + $data['B_10'] + $data['B_11'] + $data['B_12'];

            $data['update_at'] = date('Y-m-d H:i:s');
            $data['update_by'] = user('nama');

            $save = update_data($table, $data, [
                'id' => $data['id'],
                'budget_product_name' => $data['budget_product_name'],
                'budget_product_sector' => $data['budget_product_sector'],
            ]);

            if($save){
                $data_imported++;
            };
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