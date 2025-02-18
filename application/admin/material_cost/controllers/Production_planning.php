<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');

class Production_planning extends BE_Controller {

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

        // debug($data);die;

		render($data);
	}

    function data($tahun="",$cost_centre="",$tipe = 'table'){
		ini_set('memory_limit', '-1');

        $table = 'tbl_budget_production';
        $table_prod = 'tbl_production_planning_' . $tahun ;

        $arr = [
            'select' => 'a.cost_centre as kode, b.id, b.cost_centre',
            'join' => 'tbl_fact_cost_centre b on a.cost_centre = b.kode type LEFT',
            'where' => [
                'a.is_active' => 1,
                'a.id_cost_centre !=' => 0,
                // 'a.cost_centre' => '2110'
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
                'select' => 'a.*,b.code,b.product_name,b.destination, c.abbreviation as initial, c.cost_centre, d.batch_size',
                'join' =>  ['tbl_fact_product b on a.budget_product_code = b.code',
                            'tbl_fact_cost_centre c on a.id_cost_centre = c.id type LEFT',
                            'tbl_beginning_stock d on a.budget_product_code = d.budget_product_code'
                           ],
                'where' => [
                    'a.tahun' => $tahun,
                    'a.id_cost_centre' =>$m0->id,
                    // 'a.budget_product_code' => 'CIHODD5PDM'
                ],
                'sort_by' => 'a.id_cost_centre'
            ])->result();

            $data['sales'][$m0->id] = get_data($table_prod .' a',[
                'select' => 'a.*',
                    'join' =>  ['tbl_fact_product b on a.product_code = b.code',
                                'tbl_fact_cost_centre c on a.cost_centre = c.kode type LEFT',
                                ],
                'where' => [
                    'c.id' => $m0->id,
                    'a.posting_code' => 'SLS',
                ]
            ])->result();

            $data['sto_awal'][$m0->id] = get_data($table_prod .' a',[
                'select' => 'a.*',
                    'join' =>  ['tbl_fact_product b on a.product_code = b.code',
                                'tbl_fact_cost_centre c on a.cost_centre = c.kode type LEFT',
                                ],
                'where' => [
                    'c.id' => $m0->id,
                    'a.posting_code' => 'STA',
                ]
            ])->result();

            $data['sto_end'][$m0->id] = get_data($table_prod .' a',[
                'select' => 'a.*',
                    'join' =>  ['tbl_fact_product b on a.product_code = b.code',
                                'tbl_fact_cost_centre c on a.cost_centre = c.kode type LEFT',
                                ],
                'where' => [
                    'c.id' => $m0->id,
                    'a.posting_code' => 'STE',
                ]
            ])->result();
   
        }


        $response	= array(
            'table'		=> $this->load->view('material_cost/production_planning/table',$data,true),
        );
	   
	    render($response,'json');
    }

    function proses(){
		ini_set('memory_limit', '-1');
		ini_set('max_execution_time', 0);

		$tahun = post('tahun');
        $factory = post('factory');

        $table_sales = 'tbl_budget_qtysales_' . $tahun ;  
        $table_prod = 'tbl_production_planning_' . $tahun ;
 
        $field = '';
        for ($i = 1; $i <= 12; $i++) { 
            if($field == '') {
                $field = 'sum('. 'B_' . sprintf('%02d', $i).')' . ' as ' . 'B_' . sprintf('%02d', $i);
            }else{
                $field = $field . ' , ' . 'sum('. 'B_' . sprintf('%02d', $i).')' . ' as ' . 'B_' . sprintf('%02d', $i);
 
            }
        }
        $arr = [
            'select' => 'a.budget_product_code, a.budget_product_name, b.product_line,b.destination,  
                        b.cost_centre,c.id as id_cost_centre, ' . $field ,
            'join'   => ['tbl_fact_product b on a.budget_product_code = b.code type LEFT',
                         'tbl_fact_cost_centre c on b.cost_centre = c.kode type LEFT',
                        ],
            'where' => [
                'a.tahun' => $tahun,
                'a.budget_product_code' => 'CIHODD5PDM',
            ],
            'group_by' => 'a.budget_product_code'
        ];

        if(!empty($factory) && $factory != 'ALL') $arr['where']['b.cost_centre'] = $factory;

        $sales = get_data($table_sales. ' a',$arr)->result();

        foreach($sales as $s) {
            $data_sls = [
                'revision' => 0,
                'posting_code' => 'SLS',
                'product_code' => $s->budget_product_code,
                'product_name' => $s->budget_product_name,
                'cost_centre' => $s->cost_centre,
                'dest' => $s->destination,
                'id_cost_centre' => ($s->id_cost_centre == null) ? 0 : $s->id_cost_centre,
                'product_line' => $s->product_line,
                'P_01' => $s->B_01,
                'P_02' => $s->B_02,
                'P_03' => $s->B_03,
                'P_04' => $s->B_04,
                'P_05' => $s->B_05,
                'P_06' => $s->B_06,
                'P_07' => $s->B_07,
                'P_08' => $s->B_08,
                'P_09' => $s->B_09,
                'P_10' => $s->B_10,
                'P_11' => $s->B_11,
                'P_12' => $s->B_12,
            ];

            $cek = get_data($table_prod,[
                'where' => [
                    'revision' => 0,
                    'product_code' => $s->budget_product_code,
                    'posting_code' => 'SLS',
                ],
            ])->row();

            if(!isset($cek->id)){
                insert_data($table_prod,$data_sls);
            }else{
                update_data($table_prod,$data_sls,['id'=>$cek->id]);
            }

        }

        /// stock awal
        $arrs = [
            'select' => 'a.budget_product_code, a.budget_product_name, b.product_line,b.destination,  
                        b.cost_centre,c.id as id_cost_centre, a.total_stock',
            'join'   => ['tbl_fact_product b on a.budget_product_code = b.code type LEFT',
                         'tbl_fact_cost_centre c on a.id_cost_centre = c.id type LEFT',
                        ],
            'where' => [
                'a.tahun' => $tahun,
                'a.budget_product_code' => 'CIHODD5PDM'
            ],
            'group_by' => 'a.budget_product_code'
        ];

        if(!empty($factory) && $factory != 'ALL') $arr['where']['b.cost_centre'] = $factory;

        $stock = get_data('tbl_beginning_stock a',$arrs)->result();

        foreach($stock as $s) {
            $data_sls = [
                'revision' => 0,
                'posting_code' => 'STA',
                'product_code' => $s->budget_product_code,
                'product_name' => $s->budget_product_name,
                'cost_centre' => $s->cost_centre,
                'dest' => $s->destination,
                'id_cost_centre' => ($s->id_cost_centre == null) ? 0 : $s->id_cost_centre,
                'product_line' => $s->product_line,
                'P_01' => $s->total_stock,
            ];

            $cek = get_data($table_prod,[
                'where' => [
                    'revision' => 0,
                    'product_code' => $s->budget_product_code,
                    'posting_code' => 'STA',
                ],
            ])->row();

            if(!isset($cek->id)){
                insert_data($table_prod,$data_sls);
            }else{
                update_data($table_prod,$data_sls,['id'=>$cek->id]);
            }

            

            // proses end stock - 
            $this->end_stock($s->budget_product_code,$tahun);
            //
        }

        foreach($stock as $s) {
            $data_sls = [
                'revision' => 0,
                'posting_code' => 'STE',
                'product_code' => $s->budget_product_code,
                'product_name' => $s->budget_product_name,
                'cost_centre' => $s->cost_centre,
                'dest' => $s->destination,
                'id_cost_centre' => ($s->id_cost_centre == null) ? 0 : $s->id_cost_centre,
                'product_line' => $s->product_line,
                'P_01' => 0,
            ];

            $cek = get_data($table_prod,[
                'where' => [
                    'revision' => 0,
                    'product_code' => $s->budget_product_code,
                    'posting_code' => 'STE',
                ],
            ])->row();

            if(!isset($cek->id)){
                insert_data($table_prod,$data_sls);
            }else{
                update_data($table_prod,$data_sls,['id'=>$cek->id]);
            }

            

            // proses end stock - 
            $this->end_stock($s->budget_product_code,$tahun);
            //
        }

		render([
			'status'	=> 'success',
			'message'	=> 'MRP Process has benn succesfuly'
		],'json');	
	}

    function end_stock($product_code ="",$tahun="") {
        ini_set('memory_limit', '-1');
		ini_set('max_execution_time', 0);
        $table_prod = 'tbl_production_planning_' . $tahun ;
        $prod = get_data($table_prod . ' a',[
            'select' => 'a.*,b.destination',
            'join'   => ['tbl_fact_product b on a.product_code = b.code type LEFT',
                        'tbl_fact_cost_centre c on a.id_cost_centre = c.id type LEFT',
                        ],
            'where' => [
                'a.product_code' => $product_code,
            ],
        ])->result();


        if($prod) {
            
            for ($i = 1; $i <= 12; $i++) {	
                $field1 = 'P_' . sprintf('%02d', $i);
                $field2 = 'SLS_' . sprintf('%02d', $i);
                $field3 = 'STA_' . sprintf('%02d', $i);
                $field4 = 'STE_' . sprintf('%02d', $i);
                $field5 = 'PRO_' . sprintf('%02d', $i);

                $$field1 = 0;
                $$field2 = 0;
                $$field3 = 0;
                $$field4 = 0;
                $$field5 = 0;
                $stockawal = 0;
            }

            foreach ($prod as $p) {
                for ($i = 1; $i <= 12; $i++) {	
                    $post = trim($p->posting_code);
                    $field1 = 'P_' . sprintf('%02d', $i);
                    $field2 = 'SLS_' . sprintf('%02d', $i);
                    $field3 = 'STA_' . sprintf('%02d', $i);
                    $field4 = 'STE_' . sprintf('%02d', $i);
                    $field5 = 'PRO_' . sprintf('%02d', $i);

                    switch ($post) {
                        case "SLS":
                            $$field2 = $p->$field1 ;
                            break;
                        case "STA":
                            $$field3 = $p->$field1 ;
                            break;
                        case "PRO":
                            $$field5 = $p->$field1 ;
                            break;
                        case "STE":
                            $$field4 = $p->$field1 ;
                            break;
                    }

                    // debug($STA_01);die;
                    $data_sls = [
                        'revision' => 0,
                        'posting_code' => $p->posting_code,
                        'product_code' => $product_code,
                        'product_name' => $p->product_name,
                        'cost_centre' => $p->cost_centre,
                        'dest' => $p->destination,
                        'id_cost_centre' => ($p->id_cost_centre == null) ? 0 : $p->id_cost_centre,
                        'product_line' => $p->product_line,
                    ];

                    if($i == 1) {
                        $$field4 = ($$field3 + $$field5) - $$field2 ;

                        if($p->posting_code == 'STE'){
                            $data_sls[$field1] = $$field4 ;
                        }
                        
                    }else{
                        $field01 = 'P_' . sprintf('%02d', ($i-1));
                        $field02 = 'SLS_' . sprintf('%02d', ($i-1));
                        $field03 = 'STA_' . sprintf('%02d', ($i-1));
                        $field04 = 'STE_' . sprintf('%02d', ($i-1));
                        $field05 = 'PRO_' . sprintf('%02d', ($i-1));


                        // $$field4 = 0;
 
                        if($p->posting_code == 'STA'){
                            $$field04 = ($$field03 + $$field05) - $$field02 ;
                            $data_sls[$field1] = $$field04 ;
                        }

                        if($p->posting_code == 'STE'){
                            $$field4 = ($$field3 + $$field5) - $$field2 ;
                            $data_sls[$field1] = $$field4 ;
                        }
                    }

                    // if($i==2){
                    //     debug($field04);die;
                    // }

                    $cek = get_data($table_prod,[
                        'where' => [
                            'product_code' => $product_code,
                            'posting_code' => $p->posting_code
                        ],
                    ])->row();

                    if(!isset($cek->id)){
                        insert_data($table_prod,$data_sls);
                    }else{
                        update_data($table_prod,$data_sls,['id'=>$cek->id]);
                    }

                }
            }
        }

    }

    function month_coverage() {

    }
}