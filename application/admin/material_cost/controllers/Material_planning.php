<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');

class Material_planning extends BE_Controller {

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
		ini_set('max_execution_time', 0);

        $table = 'tbl_budget_production';
        $table_prod = 'tbl_production_planning_' . $tahun ;


        $data['produk']= get_data('tbl_beginning_stock_material a',[
            'select' => 'a.*',
            'where' => [
                'a.tahun' => $tahun,
                // 'a.material_code' => 'TMRTGRASUR',
            ],
        ])->result();

  
        $response	= array(
            'table'		=> $this->load->view('material_cost/material_planning/table',$data,true),
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
                'a.budget_product_code' => 'CIPTLRHPDM',
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
                'a.budget_product_code' => 'CIPTLRHPDM'
            ],
            'group_by' => 'a.budget_product_code'
        ];

        if(!empty($factory) && $factory != 'ALL') $arr['where']['b.cost_centre'] = $factory;

        $stock = get_data('tbl_beginning_stock a',$arrs)->result();

        if($stock) {
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
            }

            /// end stock awal //
            
            // stock end //
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
                $this->production($s->budget_product_code,$tahun);
                $this->end_stock($s->budget_product_code,$tahun);
                $this->month_coverage($s->budget_product_code,$tahun);
                $this->xxend_stock($s->budget_product_code,$tahun);
                //
            }
        }

		render([
			'status'	=> 'success',
			'message'	=> 'MRP Process has benn succesfuly'
		],'json');	
	}

    function save_perubahan() {       
        
        $tahun = post('tahun');

        $table = 'tbl_production_planning_' . $tahun ;

        $data   = json_decode(post('json'),true);

        foreach($data as $id => $record) {
            $result = $record;
             foreach ($result as $r => $v) {       
                update_data($table, $result,'id',$id);
            }      
        }
    }

    function xxend_stock($product_code ="",$tahun="") {
        ini_set('memory_limit', '-1');
		ini_set('max_execution_time', 0);
        $table_prod = 'tbl_production_planning_' . $tahun ;
        $p = get_data($table_prod . ' a',[
            'select' => 'a.product_code,a.product_name,a.cost_centre,a.id_cost_centre,a.product_line,a.dest,
                                MAX(CASE WHEN a.posting_code = "SLS" THEN P_01 END) AS SLS_01,
                                MAX(CASE WHEN a.posting_code = "COV" THEN P_01 END) AS COV_01,
                                MAX(CASE WHEN a.posting_code = "STA" THEN P_01 END) AS STA_01,
                                MAX(CASE WHEN a.posting_code = "STE" THEN P_01 END) AS STE_01,
                                MAX(CASE WHEN a.posting_code = "PRD" THEN P_01 END) AS PRD_01,
                                MAX(CASE WHEN a.posting_code = "XPR" THEN P_01 END) AS XPR_01,
                                MAX(CASE WHEN a.posting_code = "SLS" THEN P_02 END) AS SLS_02,
                                MAX(CASE WHEN a.posting_code = "COV" THEN P_02 END) AS COV_02,
                                MAX(CASE WHEN a.posting_code = "STA" THEN P_02 END) AS STA_02,
                                MAX(CASE WHEN a.posting_code = "STE" THEN P_02 END) AS STE_02,
                                MAX(CASE WHEN a.posting_code = "PRD" THEN P_02 END) AS PRD_02,
                                MAX(CASE WHEN a.posting_code = "XPR" THEN P_02 END) AS XPR_02,
                                MAX(CASE WHEN a.posting_code = "SLS" THEN P_03 END) AS SLS_03,
                                MAX(CASE WHEN a.posting_code = "COV" THEN P_04 END) AS COV_03,
                                MAX(CASE WHEN a.posting_code = "STA" THEN P_03 END) AS STA_03,
                                MAX(CASE WHEN a.posting_code = "STE" THEN P_03 END) AS STE_03,
                                MAX(CASE WHEN a.posting_code = "PRD" THEN P_03 END) AS PRD_03,
                                MAX(CASE WHEN a.posting_code = "XPR" THEN P_03 END) AS XPR_03,
                                MAX(CASE WHEN a.posting_code = "SLS" THEN P_04 END) AS SLS_04,
                                MAX(CASE WHEN a.posting_code = "COV" THEN P_04 END) AS COV_04,
                                MAX(CASE WHEN a.posting_code = "STA" THEN P_04 END) AS STA_04,
                                MAX(CASE WHEN a.posting_code = "STE" THEN P_04 END) AS STE_04,
                                MAX(CASE WHEN a.posting_code = "PRD" THEN P_04 END) AS PRD_04,
                                MAX(CASE WHEN a.posting_code = "XPR" THEN P_04 END) AS XPR_04,
                                MAX(CASE WHEN a.posting_code = "SLS" THEN P_05 END) AS SLS_05,
                                MAX(CASE WHEN a.posting_code = "COV" THEN P_05 END) AS COV_05,
                                MAX(CASE WHEN a.posting_code = "STA" THEN P_05 END) AS STA_05,
                                MAX(CASE WHEN a.posting_code = "STE" THEN P_05 END) AS STE_05,
                                MAX(CASE WHEN a.posting_code = "PRD" THEN P_05 END) AS PRD_05,
                                MAX(CASE WHEN a.posting_code = "XPR" THEN P_05 END) AS XPR_05,
                                MAX(CASE WHEN a.posting_code = "SLS" THEN P_06 END) AS SLS_06,
                                MAX(CASE WHEN a.posting_code = "COV" THEN P_06 END) AS COV_06,
                                MAX(CASE WHEN a.posting_code = "STA" THEN P_06 END) AS STA_06,
                                MAX(CASE WHEN a.posting_code = "STE" THEN P_06 END) AS STE_06,
                                MAX(CASE WHEN a.posting_code = "PRD" THEN P_06 END) AS PRD_06,
                                MAX(CASE WHEN a.posting_code = "XPR" THEN P_06 END) AS XPR_06,
                                MAX(CASE WHEN a.posting_code = "SLS" THEN P_07 END) AS SLS_07,
                                MAX(CASE WHEN a.posting_code = "COV" THEN P_07 END) AS COV_07,
                                MAX(CASE WHEN a.posting_code = "STA" THEN P_07 END) AS STA_07,
                                MAX(CASE WHEN a.posting_code = "STE" THEN P_07 END) AS STE_07,
                                MAX(CASE WHEN a.posting_code = "PRD" THEN P_07 END) AS PRD_07,
                                MAX(CASE WHEN a.posting_code = "XPR" THEN P_08 END) AS XPR_07,
                                MAX(CASE WHEN a.posting_code = "SLS" THEN P_08 END) AS SLS_08,
                                MAX(CASE WHEN a.posting_code = "COV" THEN P_08 END) AS COV_08,
                                MAX(CASE WHEN a.posting_code = "STA" THEN P_08 END) AS STA_08,
                                MAX(CASE WHEN a.posting_code = "STE" THEN P_08 END) AS STE_08,
                                MAX(CASE WHEN a.posting_code = "PRD" THEN P_08 END) AS PRD_08,
                                MAX(CASE WHEN a.posting_code = "XPR" THEN P_08 END) AS XPR_08,
                                MAX(CASE WHEN a.posting_code = "SLS" THEN P_09 END) AS SLS_09,
                                MAX(CASE WHEN a.posting_code = "COV" THEN P_09 END) AS COV_09,
                                MAX(CASE WHEN a.posting_code = "STA" THEN P_09 END) AS STA_09,
                                MAX(CASE WHEN a.posting_code = "STE" THEN P_09 END) AS STE_09,
                                MAX(CASE WHEN a.posting_code = "PRD" THEN P_09 END) AS PRD_09,
                                MAX(CASE WHEN a.posting_code = "XPR" THEN P_10 END) AS XPR_09,
                                MAX(CASE WHEN a.posting_code = "SLS" THEN P_10 END) AS SLS_10,
                                MAX(CASE WHEN a.posting_code = "COV" THEN P_10 END) AS COV_10,
                                MAX(CASE WHEN a.posting_code = "STA" THEN P_10 END) AS STA_10,
                                MAX(CASE WHEN a.posting_code = "STE" THEN P_10 END) AS STE_10,
                                MAX(CASE WHEN a.posting_code = "PRD" THEN P_10 END) AS PRD_10,
                                MAX(CASE WHEN a.posting_code = "XPR" THEN P_10 END) AS XPR_10,
                                MAX(CASE WHEN a.posting_code = "SLS" THEN P_11 END) AS SLS_11,
                                MAX(CASE WHEN a.posting_code = "COV" THEN P_11 END) AS COV_11,
                                MAX(CASE WHEN a.posting_code = "STA" THEN P_11 END) AS STA_11,
                                MAX(CASE WHEN a.posting_code = "STE" THEN P_11 END) AS STE_11,
                                MAX(CASE WHEN a.posting_code = "PRD" THEN P_11 END) AS PRD_11,
                                MAX(CASE WHEN a.posting_code = "XPR" THEN P_11 END) AS XPR_11,
                                MAX(CASE WHEN a.posting_code = "SLS" THEN P_12 END) AS SLS_12,
                                MAX(CASE WHEN a.posting_code = "COV" THEN P_12 END) AS COV_12,
                                MAX(CASE WHEN a.posting_code = "STA" THEN P_12 END) AS STA_12,
                                MAX(CASE WHEN a.posting_code = "STE" THEN P_12 END) AS STE_12,
                                MAX(CASE WHEN a.posting_code = "PRD" THEN P_12 END) AS PRD_12,
                                MAX(CASE WHEN a.posting_code = "XPR" THEN P_12 END) AS XPR_12,',                     
            'join'   => ['tbl_fact_product b on a.product_code = b.code type LEFT',
                        'tbl_fact_cost_centre c on a.id_cost_centre = c.id type LEFT',
                        ],
            'where' => [
                'a.product_code' => $product_code,
            ],
        ])->row();

        // debug($p);die;


        if($p) {
            
            for ($i = 1; $i <= 12; $i++) {	
                $field1 = 'SLS_' . sprintf('%02d', $i);
                $field2 = 'STA_' . sprintf('%02d', $i);
                $field3 = 'STE_' . sprintf('%02d', $i);
                $field4 = 'PRO_' . sprintf('%02d', $i);
                $field5 = 'XPR_' . sprintf('%02d', $i);
                $field6 = 'COV_' . sprintf('%02d', $i);

                $$field1 = 0;
                $$field2 = 0;
                $$field3 = 0;
                $$field4 = 0;
                $$field5 = 0;
                $$field5 = 0;
                $stockawal = 0;
            }


                $data_sls = [
                    'revision' => 0,
                    'product_code' => $product_code,
                    'product_name' => $p->product_name,
                    'cost_centre' => $p->cost_centre,
                    'dest' => $p->dest,
                    'id_cost_centre' => ($p->id_cost_centre == null) ? 0 : $p->id_cost_centre,
                    'product_line' => $p->product_line,
                    'posting_code' => 'STE'
                ];

                $data_sla = [
                    'revision' => 0,
                    'product_code' => $product_code,
                    'product_name' => $p->product_name,
                    'cost_centre' => $p->cost_centre,
                    'dest' => $p->dest,
                    'id_cost_centre' => ($p->id_cost_centre == null) ? 0 : $p->id_cost_centre,
                    'product_line' => $p->product_line,
                    'posting_code' => 'STA'
                ];


                for ($i = 1; $i <= 12; $i++) {	
                    $field0 = 'P_' . sprintf('%02d', $i);
                    $field1 = 'COV_' . sprintf('%02d', $i);
                    $field2 = 'SLS_' . sprintf('%02d', $i);
                    $field3 = 'STA_' . sprintf('%02d', $i);
                    $field4 = 'STE_' . sprintf('%02d', $i);
                    $field5 = 'PRD_' . sprintf('%02d', $i);
                    $field6 = 'XPR_' . sprintf('%02d', $i);
                

                    if($i == 1) {
                        $$field4= ($p->$field3 + $p->$field5) - $p->$field2 ;
                        $data_sls[$field0] = $$field4 ;
                    }else{
                        $field01 = 'COV_' . sprintf('%02d', $i);
                        $field02 = 'SLS_' . sprintf('%02d', ($i-1));
                        $field03 = 'STA_' . sprintf('%02d', ($i-1));
                        $field04 = 'STE_' . sprintf('%02d', ($i-1));
                        $field05 = 'PRD_' . sprintf('%02d', ($i-1));
                        $field06 = 'XPR_' . sprintf('%02d', ($i-1));
                        $$field04 = ($p->$field03 + $p->$field05) - $p->$field02 ;
                        $data_sla[$field0] = $$field04 ;
                        $data_sls[$field0] = ($$field04 + $p->$field05) - $p->$field02;

                    }

                    $cek = get_data($table_prod,[
                        'where' => [
                            'product_code' => $product_code,
                            'posting_code' => 'STE'
                        ],
                    ])->row();
                        
                    // if(!isset($cek->id)){
                    //     insert_data($table_prod,$data_sls);
                    // }else{
                    //     update_data($table_prod,$data_sls,['id'=>$cek->id]);
                    // }

                    for ($ix = 1; $ix <= 2; $ix++) {	
                        if($ix == 1) {
                            $data_sls['posting_code'] = 'STE';
                            $cek = get_data($table_prod,[
                                'where' => [
                                    'product_code' => $product_code,
                                    'posting_code' => 'STE'
                                ],
                            ])->row();
                                
                            if(!isset($cek->id)){
                                insert_data($table_prod,$data_sls);
                            }else{
                                update_data($table_prod,$data_sls,['id'=>$cek->id]);
                            }
                        }else{
                            $data_sls['posting_code'] = 'STA';
                            $ceka = get_data($table_prod,[
                                'where' => [
                                    'product_code' => $product_code,
                                    'posting_code' => 'STA'
                                ],
                            ])->row();
                                
                            if(!isset($ceka->id)){
                                insert_data($table_prod,$data_sla);
                            }else{
                                update_data($table_prod,$data_sla,['id'=>$ceka->id]);
                            }

                        }
                    }
                    
                }
                
            
        }

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
                        case "PRD":
                            $$field5 = $p->$field1 ;
                            break;
                        case "STE":
                            $$field4 = $p->$field1 ;
                            break;
                    }

                    // debug($STA_01);die;


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

    // end stodk end //

    // month coverage //
    function month_coverage($product_code="",$tahun="") {
        ini_set('memory_limit', '-1');
		ini_set('max_execution_time', 0);
        $table_prod = 'tbl_production_planning_' . $tahun ;
        $select1 = '';
        for ($i = 1; $i <= 12; $i++) {
            if($select1==""){
                $select1 = "MAX(CASE WHEN posting_code = 'SLS' THEN " ."P_" . sprintf('%02d',$i) . " END) AS " . "S_" . sprintf('%02d',$i);
            }else{
                $select1 .= " ," . "MAX(CASE WHEN posting_code = 'SLS' THEN " ."P_" . sprintf('%02d',$i) . " END) AS " . "S_" . sprintf('%02d',$i);
            }
        }

        $select2 = '';
        for ($i = 1; $i <= 12; $i++) {
            if($select2==""){
                $select2 = "MAX(CASE WHEN a.posting_code = 'STE' THEN " ."P_" . sprintf('%02d',$i) . " END) AS " . "E_" . sprintf('%02d',$i);
            }else{
                $select2 .= " ," . "MAX(CASE WHEN a.posting_code = 'STE' THEN " ."P_" . sprintf('%02d',$i) . " END) AS " . "E_" . sprintf('%02d',$i);
            }
        }

        $select = $select1 . ' , ' . $select2 ;

        $prod = get_data($table_prod . ' a',[
            'select' => 'a.id,a.product_code,a.product_name,a.cost_centre,a.id_cost_centre,a.product_line,
                         b.destination, ' . $select ,
            'join'   => ['tbl_fact_product b on a.product_code = b.code type LEFT',
                        'tbl_fact_cost_centre c on a.id_cost_centre = c.id type LEFT',
                        ],
            'where' => [
                'a.product_code' => $product_code,
            ],
        ])->row();

        $data_sls = [
            'revision' => 0,
            'posting_code' => 'COV',
            'product_code' => $product_code,
            'product_name' => $prod->product_name,
            'cost_centre' => $prod->cost_centre,
            'dest' => $prod->destination,
            'id_cost_centre' => ($prod->id_cost_centre == null) ? 0 : $prod->id_cost_centre,
            'product_line' => $prod->product_line,
        ];

        for ($i = 1; $i <= 12; $i++) {
            $mCov = 'MC_' . sprintf('%02d',$i);
            $field = 'P_' . sprintf('%02d',$i);
            if($i==1) {
                $$mCov = (($prod->S_01+$prod->S_02+$prod->S_03+$prod->S_04) / 4) != 0 ? $prod->E_01 / (($prod->S_01+$prod->S_02+$prod->S_03+$prod->S_04) / 4) : 0 ;
            }elseif($i==2){
                $$mCov = (($prod->S_02+$prod->S_03+$prod->S_04+$prod->S_05) / 4) != 0 ? $prod->E_02 / (($prod->S_02+$prod->S_03+$prod->S_04+$prod->S_05) / 4) : 0 ;
            }elseif($i==3){
                $$mCov = (($prod->S_03+$prod->S_04+$prod->S_05+$prod->S_06) / 4) != 0 ? $prod->E_03 / (($prod->S_03+$prod->S_04+$prod->S_05+$prod->S_06) / 4) : 0 ;
            }elseif($i==4){
                $$mCov = (($prod->S_04+$prod->S_05+$prod->S_06+$prod->S_07) / 4) != 0 ? $prod->E_04 / (($prod->S_04+$prod->S_05+$prod->S_06+$prod->S_07) / 4) : 0 ;
            }elseif($i==5){
                $$mCov = (($prod->S_05+$prod->S_06+$prod->S_07+$prod->S_08) / 4) != 0 ? $prod->E_05 / (($prod->S_05+$prod->S_06+$prod->S_07+$prod->S_08) / 4) : 0 ;
            }elseif($i==6){
                $$mCov = (($prod->S_06+$prod->S_07+$prod->S_08+$prod->S_09) / 4) != 0 ? $prod->E_06 / (($prod->S_06+$prod->S_07+$prod->S_08+$prod->S_09) / 4) : 0 ;
            }elseif($i==7){
                $$mCov = (($prod->S_07+$prod->S_08+$prod->S_09+$prod->S_10) / 4) != 0 ? $prod->E_07 / (($prod->S_07+$prod->S_08+$prod->S_09+$prod->S_10) / 4) : 0 ;
            }elseif($i==8){
                $$mCov = (($prod->S_08+$prod->S_09+$prod->S_10+$prod->S_11) / 4) != 0 ? $prod->E_08 / (($prod->S_08+$prod->S_09+$prod->S_10+$prod->S_11) / 4) : 0 ;
            }elseif($i==9){
                $$mCov = (($prod->S_09+$prod->S_10+$prod->S_11+$prod->S_12) / 4) != 0 ? $prod->E_09 / (($prod->S_09+$prod->S_10+$prod->S_11+$prod->S_12) / 4) : 0 ;
            }elseif($i==10){
                $$mCov = (($prod->S_10+$prod->S_11+$prod->S_12) / 3) != 0 ? $prod->E_10 / (($prod->S_10+$prod->S_11+$prod->S_12) / 3) : 0 ;
            }elseif($i==11){
                $$mCov = (($prod->S_11+$prod->S_12) / 2) != 0 ? $prod->E_11 / (($prod->S_11+$prod->S_12) / 2) : 0 ;
            }else{
                $$mCov = $prod->S_12 != 0 ? $prod->E_12 / $prod->S_12 : 0 ;
            }
            $data_sls[$field] = $$mCov ;
        }

        $cek = get_data($table_prod,[
            'where' => [
                'product_code' => $product_code,
                'posting_code' => 'COV',
            ],
        ])->row();

        if(!isset($cek->id)){
            insert_data($table_prod,$data_sls);
        }else{
            update_data($table_prod,$data_sls,['id'=>$cek->id]);
        }
    }

    // end month coverage //
    function production($product_code="",$tahun="") {
        ini_set('memory_limit', '-1');
		ini_set('max_execution_time', 0);
        
        $table_prod = 'tbl_production_planning_' . $tahun ;

        $cov = get_data($table_prod . ' a',[
            'select' => 'a.*,b.destination, d.batch_size',
            'join'   => ['tbl_fact_product b on a.product_code = b.code type LEFT',
                        'tbl_fact_cost_centre c on a.id_cost_centre = c.id type LEFT',
                        'tbl_beginning_stock d on a.product_code = d.budget_product_code and d.tahun ="'.$tahun.'" type LEFT'
                        ],
            'where' => [
                'posting_code' => 'COV',
                'product_code' => $product_code
            ],
        ])->result();
        
        if($cov) {
            foreach($cov as $c) {
                $cek_prod = get_data($table_prod,[
                'where' => [
                    'product_code' => $product_code,
                    'posting_code' => 'PRD',
                ],
                ])->row();

                $data_prod = [
                    'revision' => 0,
                    'posting_code' => 'PRD',
                    'product_code' => $product_code,
                    'product_name' => $c->product_name,
                    'cost_centre' => $c->cost_centre,
                    'dest' => $c->destination,
                    'id_cost_centre' => ($c->id_cost_centre == null) ? 0 : $c->id_cost_centre,
                    'product_line' => $c->product_line,
                ];

                $field = '';
                for ($i = 1; $i <= 12; $i++) {
                    $field = 'P_' . sprintf('%02d',$i);
                    if(($c->$field * -1) < 1.98 && $c->$field != 0) {
                        $data_prod[$field] = $c->batch_size ;
                    }else{
                        $data_prod[$field] = 0;
                    }
                }
                
                if(!isset($cek_prod->id)){
                    insert_data($table_prod,$data_prod);
                }else{
                    update_data($table_prod,$data_prod,['id'=>$cek_prod->id]);
                }

                $cek_xprod = get_data($table_prod,[
                'where' => [
                    'product_code' => $product_code,
                    'posting_code' => 'XPR',
                ],
                ])->row();

                $data_xprod = [
                    'revision' => 0,
                    'posting_code' => 'XPR',
                    'product_code' => $product_code,
                    'product_name' => $c->product_name,
                    'cost_centre' => $c->cost_centre,
                    'dest' => $c->destination,
                    'id_cost_centre' => ($c->id_cost_centre == null) ? 0 : $c->id_cost_centre,
                    'product_line' => $c->product_line,
                ];

                $field ="";
                for ($i = 1; $i <= 12; $i++) {
                    $field = 'P_' . sprintf('%02d',$i);
                    if(($c->$field * -1) < 1.98 && $c->$field != 0) {
                        $data_xprod[$field] = 1 ;
                    }else{
                        $data_xprod[$field] = 0 ;
                    }
                }
                    

                if(!isset($cek_xprod->id)){
                    insert_data($table_prod,$data_xprod);
                }else{
                    update_data($table_prod,$data_xprod,['id'=>$cek_xprod->id]);
                }

                // $this->xxend_stock($product_code,$tahun);
            }
        }
    }


}