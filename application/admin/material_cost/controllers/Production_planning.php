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
		ini_set('max_execution_time', 0);

        $table = 'tbl_budget_production';
        $table_prod = 'tbl_production_planning_' . $tahun ;

        $arr = [
            'select' => 'a.cost_centre as kode, b.id, b.cost_centre, c.kapasitas,
                        WD_01,WD_02,WD_03,WD_04,WD_05,WD_06,WD_07,WD_08,WD_09,WD_10,WD_11,WD_12',
            'join' => ['tbl_fact_cost_centre b on a.cost_centre = b.kode type LEFT',
                       'tbl_kapasitas_produksi c on a.cost_centre = c.cost_centre type LEFT'
                      ],
            'where' => [
                'a.is_active' => 1,
                'a.id_cost_centre !=' => 0,
                'a.cost_centre' => '2110'
                // 'a.code' => 'CIPTLRHPDM'
            ],
            'group_by' => 'a.id_cost_centre',
            'sort_by' => 'b.id', 
             ];

        if($cost_centre && $cost_centre !='ALL') $arr['where']['a.cost_centre'] = $cost_centre;


	    $data['grup'][0]= get_data('tbl_fact_product a',$arr)->result();

        $data['kprod'] = [];
        $data['wday'] = [];
        $data['sprod'] = [];
        foreach($data['grup'][0] as $m0) {	
            $data['kprod'][$m0->id] = $m0->kapasitas;
     
            $field1 = '';
            $field2 = '';
            for ($i = 1; $i <= 12; $i++) { 
                $field1 = 'WD_' . sprintf('%02d', $i);
                $data['wday'][$m0->id][$i] = $m0->$field1;
                $data['sprod'][$m0->id][$i] = ($m0->kapasitas * $m0->$field1);

                if($field2 == '') {
                    $field2 = 'sum('. 'P_' . sprintf('%02d', $i).')' . ' as ' . 'P_' . sprintf('%02d', $i);
                }else{
                    $field2 = $field2 . ' , ' . 'sum('. 'P_' . sprintf('%02d', $i).')' . ' as ' . 'P_' . sprintf('%02d', $i);
    
                }
            }
            
            $data['prod'][$m0->id] = get_data($table_prod,[
                'select' => 'id_cost_centre,cost_centre, ' . $field2 ,
                'where'  => [
                    'posting_code' => 'PRD',
                    'id_cost_centre' => $m0->id,
                ],
                'group_by' => 'id_cost_centre',
            ])->row_array();

            // debug($data['prod']);die;

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
                    'd.tahun' => $tahun,
                    'a.id_cost_centre' =>$m0->id,
                    'a.budget_product_code' => 'CIPTLRHPDM'
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

            $data['m_cov'][$m0->id] = get_data($table_prod .' a',[
                'select' => 'a.*',
                    'join' =>  ['tbl_fact_product b on a.product_code = b.code',
                                'tbl_fact_cost_centre c on a.cost_centre = c.kode type LEFT',
                                ],
                'where' => [
                    'c.id' => $m0->id,
                    'a.posting_code' => 'COV',
                ]
            ])->result();

            $data['xprod'][$m0->id] = get_data($table_prod .' a',[
                'select' => 'a.*',
                    'join' =>  ['tbl_fact_product b on a.product_code = b.code',
                                'tbl_fact_cost_centre c on a.cost_centre = c.kode type LEFT',
                                ],
                'where' => [
                    'c.id' => $m0->id,
                    'a.posting_code' => 'XPR',
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
                $this->end_stock($s->budget_product_code,$tahun);
                $this->month_coverage($s->budget_product_code,$tahun);
                $this->production($s->budget_product_code,$tahun);
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

                            // debug
                            debug($$field3);
                            debug($$field5);
                           debug($$field2);

                           die;

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

            }
        }
    }


}