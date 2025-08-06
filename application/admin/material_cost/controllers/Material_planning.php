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
        $table_mat = 'tbl_material_planning_' . $tahun ;


        $data['produk'] = get_data($table_mat . ' a',[
            'select' => 'a.*',
            'join'   => 'tbl_material_formula b on a.material_code = b.component_item and b.tahun ="'.$tahun.'" type LEFT',
            'where' => [
                'b.tahun' => $tahun,
                'a.posting_code' => 'STA',
                // 'b.parent_item' => 'CIGSAL22DM',
                // 'a.material_code' => 'CILBSBAL53',
            ],
        ])->result();

        foreach($data['produk'] as $d) {
            $data['prod'][$d->material_code] = get_data($table_mat . ' a',[
                'select' => 'a.*',
                'join'   => 'tbl_material_formula b on a.material_code = b.component_item and b.tahun ="'.$tahun.'" type LEFT',
                'where'  => [
                    'b.tahun' => $tahun,
                    'a.posting_code' => 'ARQ',
                    'a.material_code' => $d->material_code,
                    // 'b.parent_item' => 'CIGSAL22DM'
                ],
            ])->row_array();

            $data['arival'][$d->material_code] = get_data($table_mat . ' a',[
                'select' => 'a.*',
                'join'   => 'tbl_material_formula b on a.material_code = b.component_item and b.tahun ="'.$tahun.'" type LEFT',
                'where'  => [
                    'b.tahun' => $tahun,
                    'a.posting_code' => 'PBL',
                    'a.material_code' => $d->material_code,
                    // 'b.parent_item' => 'CIGSAL22DM'
                ],
            ])->row_array();

            $data['pakai'][$d->material_code] = get_data($table_mat . ' a',[
                'select' => 'a.*',
                'join'   => 'tbl_material_formula b on a.material_code = b.component_item and b.tahun ="'.$tahun.'" type LEFT',
                'where'  => [
                    'b.tahun' => $tahun,
                    'a.posting_code' => 'PMK',
                    'a.material_code' => $d->material_code,
                    // 'b.parent_item' => 'CIGSAL22DM'
                ],
            ])->row_array();

            $data['available'][$d->material_code] = get_data($table_mat . ' a',[
                'select' => 'a.*',
                'join'   => 'tbl_material_formula b on a.material_code = b.component_item and b.tahun ="'.$tahun.'" type LEFT',
                'where'  => [
                    'b.tahun' => $tahun,
                    'a.posting_code' => 'AVA',
                    'a.material_code' => $d->material_code,
                    // 'b.parent_item' => 'CIGSAL22DM'
                ],
            ])->row_array();

            // debug($data['pakai']);die;

            $data['iventory'][$d->material_code] = get_data($table_mat . ' a',[
                'select' => 'a.*',
                'join'   => 'tbl_material_formula b on a.material_code = b.component_item and b.tahun ="'.$tahun.'" type LEFT',
                'where'  => [
                    'b.tahun' => $tahun,
                    'a.posting_code' => 'STE',
                    'a.material_code' => $d->material_code,
                    // 'b.parent_item' => 'CIGSAL22DM'
                ],
            ])->row_array();

            $data['cov'][$d->material_code] = get_data($table_mat . ' a',[
                'select' => 'a.*',
                'join'   => 'tbl_material_formula b on a.material_code = b.component_item and b.tahun ="'.$tahun.'" type LEFT',
                'where'  => [
                    'b.tahun' => $tahun,
                    'a.posting_code' => 'COV',
                    'a.material_code' => $d->material_code,
                    // 'b.parent_item' => 'CIGSAL22DM'
                ],
            ])->row_array();
        }

  
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

        $table_prod = 'tbl_budget_production' ;
        $table_mat = 'tbl_material_planning_' . $tahun ;
 
        $arr = [
            'select' => 'a.component_item,a.material_name,a.um,e.supplier,e.moq,e.m_cov,e.order_multiple,e.total_stock, 
                        sum(a.total * b.B_01) as B_01, sum(a.total * b.B_02) as B_02, sum(a.total * b.B_03) as B_03, 
                        sum(a.total * b.B_04) as B_04, sum(a.total * b.B_05) as B_05, sum(a.total * b.B_06) as B_06, 
                        sum(a.total * b.B_07) as B_07, sum(a.total * b.B_08) as B_08, sum(a.total * b.B_09) as B_09, 
                        sum(a.total * b.B_10) as B_10, sum(a.total * b.B_11) as B_11, sum(a.total * b.B_12) as B_12',
             'join'   => [$table_prod .' b on a.parent_item = b.budget_product_code and b.tahun ="'.$tahun.'" type LEFT',
                         'tbl_beginning_stock d on a.parent_item = b.budget_product_code and b.tahun ="'.$tahun.'"',
                         'tbl_beginning_stock_material e on a.component_item = e.material_code and e.tahun ="'.$tahun.'" type LEFT',
                        ],
            'where' => [
                'a.tahun' => $tahun,
                // 'a.parent_item' => 'CIGSAL22DM',
                // 'a.component_item' => 'CILBSBAL53'
            ],
            'group_by' => 'a.component_item,a.material_name',
            'sort_by' => 'a.component_item'
        ];

        $prod = get_data('tbl_material_formula  a',$arr)->result();

        // debug($prod);die;

        foreach($prod as $s) {
            $data_prod = [
                'revision' => 0,
                'posting_code' => 'PMK',
                'material_code' => $s->component_item,
                'material_name' => $s->material_name,
                'um' => $s->um,
                'supplier' => $s->supplier ?? '',
                'moq' => $s->moq ?? 0,
                'order_multiple' => $s->order_multiple ?? 0,
            ];
            $field0 = '';
            $field1 = '';
            for ($i = 1; $i <= 12; $i++) {
                $field0 = 'P_' . sprintf('%02d', $i);
                $field1 = 'B_' . sprintf('%02d', $i);
                $data_prod[$field0] = $s->$field1;
            }

            $cek = get_data($table_mat,[
                'where' => [
                    'revision' => 0,
                    'material_code' => $s->component_item,
                    'posting_code' => 'PMK',
                ],
            ])->row();

            if(!isset($cek->id)){
                insert_data($table_mat,$data_prod);
            }else{
                update_data($table_mat,$data_prod,['id'=>$cek->id]);
            }

            $data_cov = [
                'revision' => 0,
                'posting_code' => 'COV',
                'material_code' => $s->component_item,
                'material_name' => $s->material_name,
                'um' => $s->um,
                'supplier' => $s->supplier ?? '',
                'moq' => $s->moq ?? 0,
                'order_multiple' => $s->order_multiple ?? 0,
            ];

            $field = '';
            for ($i = 1; $i <= 12; $i++) {
                $field = 'P_' . sprintf('%02d', $i);
                $data_cov[$field] = $s->m_cov??0;
            }

            $cek_cov = get_data($table_mat,[
                'select' => 'id',
                'where' => [
                    'revision' => 0,
                    'material_code' => $s->component_item,
                    'posting_code' => 'COV',
                ],
            ])->row();

            if(!isset($cek_cov->id)){
                insert_data($table_mat,$data_cov);
            }else{
                update_data($table_mat,$data_cov,['id'=>$cek_cov->id]);
            }


            /// unit for use

            $data_use = [
                'revision' => 0,
                'posting_code' => 'PMK',
                'material_code' => $s->component_item,
                'material_name' => $s->material_name,
                'um' => $s->um,
                'supplier' => $s->supplier ?? '',
                'moq' => $s->moq ?? 0,
                'order_multiple' => $s->order_multiple ?? 0,
            ];

            $cek_use = get_data($table_mat,[
                'select' => 'id',
                'where' => [
                    'revision' => 0,
                    'material_code' => $s->component_item,
                    'posting_code' => 'PMK',
                ],
            ])->row();

            if(!isset($cek_use->id)){
                insert_data($table_mat,$data_use);
            }else{
                update_data($table_mat,$data_use,['id'=>$cek_use->id]);
            }

            /// stock awal 

            $data_stoa = [
                'revision' => 0,
                'posting_code' => 'STA',
                'material_code' => $s->component_item,
                'material_name' => $s->material_name,
                'um' => $s->um,
                'supplier' => $s->supplier ?? '',
                'moq' => $s->moq ?? 0,
                'order_multiple' => $s->order_multiple ?? 0,
                'P_01' => $s->total_stock ?? 0,
            ];

            $cek = get_data($table_mat,[
                'where' => [
                    'revision' => 0,
                    'material_code' => $s->component_item,
                    'posting_code' => 'STA',
                ],
            ])->row();

            if(!isset($cek->id)){
                insert_data($table_mat, $data_stoa);
            }else{
                update_data($table_mat, $data_stoa,['id'=>$cek->id]);
            }

            // end stock awal


            // stock end 

            $data_stoe = [
                'revision' => 0,
                'posting_code' => 'STE',
                'material_code' => $s->component_item,
                'material_name' => $s->material_name,
                'um' => $s->um,
                'supplier' => $s->supplier ?? '',
                'moq' => $s->moq ?? 0,
                'order_multiple' => $s->order_multiple ?? 0,
                'P_01' => 0,
            ];

            $cek = get_data($table_mat,[
                'select' => 'id',
                'where' => [
                    'revision' => 0,
                    'material_code' => $s->component_item,
                    'posting_code' => 'STE',
                ],
            ])->row();

            if(!isset($cek->id)){
                insert_data($table_mat,$data_stoe);
            }else{
                update_data($table_mat,$data_stoe,['id'=>$cek->id]);
            }

            /// end stock end
            $this->pembelian_awal($s->component_item,$tahun);

        }

		render([
			'status'	=> 'success',
			'message'	=> 'MRP Process has benn succesfuly'
		],'json');	
	}

    function pembelian_awal($material_code, $tahun) 
       {

        $table_prod = 'tbl_material_planning_' . $tahun;

        $c = get_data($table_prod . ' a', [
            'select' => 'a.*,b.destination, d.total_stock, d.m_cov',
            'join'   => [
                'tbl_material_formula e on a.material_code = e.component_item',
                'tbl_fact_product b on e.parent_item = b.code type LEFT',
                'tbl_fact_cost_centre c on b.id_cost_centre = c.id type LEFT',
                'tbl_beginning_stock_material d on a.material_code = d.material_code and d.tahun ="' . $tahun . '" type LEFT'
            ],
            'where' => [
                'a.posting_code' => 'STA',
                'a.material_code' => $material_code,
                // 'd.is_active' => 1
            ],
        ])->row();
        
        // debug($c);die;

        if ($c) {
            $list_posting_code = ['ERQ', 'ARQ', 'AVA'];
            foreach ($list_posting_code as $pc) {
                $cek_mat = get_data($table_prod, [
                    'where' => [
                        'material_code' => $material_code,
                        'posting_code' => $pc,
                    ],
                ])->row();

                $data_mat = [
                    'revision' => 0,
                    'posting_code' => $pc,
                    'material_code' => $c->material_code,
                    'material_name' => $c->material_name,
                    'um' => $c->um,
                    'supplier' => $c->supplier ?? '',
                    'moq' => $c->moq ?? 0,
                    'order_multiple' => $c->order_multiple ?? 0,
                    'P_01' => 0,
                ];

                $field = '';
                for ($i = 1; $i <= 12; $i++) {
                    $field = 'P_' . sprintf('%02d', $i);
                    $data_mat[$field] = 0;
                }

                if (!isset($cek_mat->id)) {
                    insert_data($table_prod, $data_mat);
                }
            }


            $field = "";
            $next_data = [
                'unit_used' => 0,
                'unit_available' => 0,
                'beginning_stock' => 0,
                'end_stock' => 0,
                'coverage' => 0,
                'arrival_qty' => 0,
                'e_arrival' => 0,
            ];

            $data_sales = get_data('tbl_material_planning_' . $tahun, [
                'where' => [
                    'material_code' => $material_code,
                    'posting_code' => 'PMK'
                ]
            ])->row_array();
            if ($c->moq > 0) {
                for ($i = 1; $i <= 12; $i++) {
                    $sales = @$data_sales['P_' . sprintf('%02d', $i)] ?? 0;
                    if ($i == 1) {
                        $tmp_data = $this->init_data($material_code, sprintf('%02d', $i), $tahun);
                        $tmp_data['beginning_stock'] = $c->total_stock;
                    } else {

                        $tmp_data = [
                            'unit_used' => $next_data['unit_used'],
                            'beginning_stock' => $next_data['beginning_stock'],
                            'end_stock' => $next_data['end_stock'],
                            'coverage' => $next_data['coverage'],
                            'arrival_qty' => $next_data['arrival_qty'],
                            'e_arrival' => $next_data['e_arrival'],
                        ];
                    }

                    // cari data yang di edit 
                    $cari_epr = get_data('tbl_material_planning_' . $tahun, [
                        'select' => 'P_' . sprintf('%02d', $i) . ' as jml',
                        'where' => [
                            'material_code' => $material_code,
                            'posting_code' => 'EPR'
                        ]
                    ])->row_array();


                    $value_xproduction = $c->moq;
                    $value_end_stock = 0;
                    $value_coverage = 0;
                    $value_production = 0;
                    $unit_available = 0;

                    $average_sales_per_4_month = 0;
                    $total_sales = 0;
                    $pembagi = 0;

                    for ($j = 0; $j < 4; $j++) {
                        if ($i + $j < 13) {
                            $total_sales += @$data_sales['P_' . sprintf('%02d', $i + $j)] ?? 0;
                            $pembagi++;
                        }
                    }
                    $average_sales_per_4_month = 0;

                    if ($total_sales != 0 && $pembagi != 0) {
                        $average_sales_per_4_month = $total_sales / $pembagi;       

                        // cari value_production  sampai value_coverage > $c1->m_cov value production dimulai dari $c->moq  
                        if ($value_coverage < $c->m_cov) {
                            $value_production = $c->moq;
                            $value_end_stock = $tmp_data['beginning_stock'] + $value_production - $sales;
                            $unit_available = $tmp_data['beginning_stock'] + $value_production;
                            $value_coverage = $value_end_stock / $average_sales_per_4_month;


                            while ($value_coverage < $c->m_cov) {
                                $value_production += $c->moq;
                                $value_end_stock = $tmp_data['beginning_stock'] + $value_production - $sales;
                                $unit_available = $tmp_data['beginning_stock'] + $value_production;
                                $value_coverage = $value_end_stock / $average_sales_per_4_month;
                            }
                        } else {
                            if (isset($cari_epr['jml']) && !empty($cari_epr['jml'])) {
                                if ($cari_epr['jml'] > 0) {
                                    // jika ada data yang sudah di edit
                                    // gunakan value production yang sudah di edit
                                    $value_production = $cari_epr['jml'];
                                } else {
                                    // jika tidak ada data yang sudah di edit
                                    // gunakan value production yang sudah dihitung
                                    $value_production = 0;
                                }
                            } else {
                                // jika tidak ada data yang sudah di edit
                                // gunakan value production yang sudah dihitung
                                $value_production = 0;
                            }
                        }

                    } else {
                        $value_production = 0;
                    }


                    if (!($i + 1 >= 13)) {
                        $next_data = [
                            'unit_used' => @$data_sales['P_' . sprintf('%02d', $i + 1)] ?? 0,
                            'beginning_stock' => $value_end_stock,
                            'unit_available' => $unit_available,
                            'end_stock' => 0,
                            'coverage' => 0,
                            'arrival_qty' => 0,
                            'e_arrival' => 0
                        ];
                    }

                    # sales
                    update_data($table_prod, [
                        'P_' . sprintf('%02d', $i) => $tmp_data['beginning_stock'],
                    ], [
                        'material_code' => $material_code,
                        'posting_code' => 'STA'
                    ]);

                    update_data($table_prod, [
                        'P_' . sprintf('%02d', $i) => $unit_available,
                    ], [
                        'material_code' => $material_code,
                        'posting_code' => 'AVA'
                    ]);

                    # end stock
                    update_data($table_prod, [
                        'P_' . sprintf('%02d', $i) => $value_end_stock,
                    ], [
                        'material_code' => $material_code,
                        'posting_code' => 'STE'
                    ]);

                    # corverage
                    update_data($table_prod, [
                        'P_' . sprintf('%02d', $i) => $value_coverage,
                    ], [
                        'material_code' => $material_code,
                        'posting_code' => 'COV'
                    ]);

                    $data_epd = get_data($table_prod, [
                        'where' => [
                            'material_code' => $material_code,
                            'posting_code' => 'ERQ'
                        ]
                    ])->row_array();

                    if(empty($data_epd) || @$data_epd['P_' . sprintf('%02d', $i)] <= 0){
                        # production
                        update_data($table_prod, [
                            'P_' . sprintf('%02d', $i) => $value_production,
                        ], [
                            'material_code' => $material_code,
                            'posting_code' => 'ARQ'
                        ]);
                    }

                    # x production
                    update_data($table_prod, [
                        'P_' . sprintf('%02d', $i) => $value_production,
                        'update_at' => date('Y-m-d H:i:s')
                    ], [
                        'material_code' => $material_code,
                        'posting_code' => 'EPR',
                    ]);
                }
            }

            # Save X Production
            // if (!isset($cek_xprod->id)) {
            //     insert_data($table_prod, $data_xprod);
            // } else {
            //     update_data($table_prod, $data_xprod, ['id' => $cek_xprod->id]);
            // }

            // $this->xxend_stock($material_code, $tahun);
            // $this->month_coverage($material_code, $tahun);
        }
    }

    private function init_data($material_code, $month, $year){
        
        $data_mat = get_data('tbl_material_planning_'.$year.' a', [
            'select' => 'P_'.sprintf('%02d', $month).' as value, posting_code',
            'where' => [
                'a.material_code' => $material_code
            ]
        ])->result_array();

        $data = [
            'unit_used' => 0,
            'unit_available' => 0,
            'beginning_stock' => 0,
            'end_stock' => 0,
            'coverage' => 0,
            'arrival_qty' => 0,
            'e_arrival' => 0,
        ];

        if($data_mat){
            foreach($data_mat as $v){
                switch($v['posting_code']){
                    case 'ARQ':
                        $data['arrival_qty'] = $v['value'];
                    break;
                    case 'STA':
                        $data['beginning_stock'] = $v['value'];
                    break;
                    case 'STE':
                        $data['end_stock'] = $v['value'];
                    break;
                    case 'COV':
                        $data['coverage'] = $v['value'];
                    break;
                    case 'PMK':
                        $data['unit_used'] = $v['value'];
                    break;
                }
            }
        }

        return $data;
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




}