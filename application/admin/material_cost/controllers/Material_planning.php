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
                'b.parent_item' => 'CIPTLRHPDM'
                // 'a.material_code' => 'TMRTGRASUR',
            ],
        ])->result();

        foreach($data['produk'] as $d) {
            $data['prod'][$d->material_code] = get_data($table_mat . ' a',[
                'select' => 'a.*',
                'join'   => 'tbl_material_formula b on a.material_code = b.component_item and b.tahun ="'.$tahun.'" type LEFT',
                'where'  => [
                    'b.tahun' => $tahun,
                    'a.posting_code' => 'PRD',
                    'a.material_code' => $d->material_code,
                    'b.parent_item' => 'CIPTLRHPDM'
                ],
            ])->row_array();

            $data['arival'][$d->material_code] = get_data($table_mat . ' a',[
                'select' => 'a.*',
                'join'   => 'tbl_material_formula b on a.material_code = b.component_item and b.tahun ="'.$tahun.'" type LEFT',
                'where'  => [
                    'b.tahun' => $tahun,
                    'a.posting_code' => 'PBL',
                    'a.material_code' => $d->material_code,
                    'b.parent_item' => 'CIPTLRHPDM'
                ],
            ])->row_array();

            $data['pakai'][$d->material_code] = get_data($table_mat . ' a',[
                'select' => 'a.*',
                'join'   => 'tbl_material_formula b on a.material_code = b.component_item and b.tahun ="'.$tahun.'" type LEFT',
                'where'  => [
                    'b.tahun' => $tahun,
                    'a.posting_code' => 'PMK',
                    'a.material_code' => $d->material_code,
                    'b.parent_item' => 'CIPTLRHPDM'
                ],
            ])->result_array();

            // debug($data['pakai']);die;

            $data['iventory'][$d->material_code] = get_data($table_mat . ' a',[
                'select' => 'a.*',
                'join'   => 'tbl_material_formula b on a.material_code = b.component_item and b.tahun ="'.$tahun.'" type LEFT',
                'where'  => [
                    'b.tahun' => $tahun,
                    'a.posting_code' => 'STE',
                    'a.material_code' => $d->material_code,
                    'b.parent_item' => 'CIPTLRHPDM'
                ],
            ])->row_array();

            $data['cov'][$d->material_code] = get_data($table_mat . ' a',[
                'select' => 'a.*',
                'join'   => 'tbl_material_formula b on a.material_code = b.component_item and b.tahun ="'.$tahun.'" type LEFT',
                'where'  => [
                    'b.tahun' => $tahun,
                    'a.posting_code' => 'COV',
                    'a.material_code' => $d->material_code,
                    'b.parent_item' => 'CIPTLRHPDM'
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

        $table_prod = 'tbl_production_planning_' . $tahun ;
        $table_mat = 'tbl_material_planning_' . $tahun ;
 
        $arr = [
            'select' => 'a.component_item,a.material_name,a.um,e.supplier,e.moq,e.m_cov,e.order_multiple, 
                        sum(a.total * b.P_01) as P_01, sum(a.total * b.P_02) as P_02, sum(a.total * b.P_03) as P_03, 
                        sum(a.total * b.P_04) as P_04, sum(a.total * b.P_05) as P_05, sum(a.total * b.P_06) as P_06, 
                        sum(a.total * b.P_07) as P_07, sum(a.total * b.P_08) as P_08, sum(a.total * b.P_09) as P_09, 
                        sum(a.total * b.P_10) as P_10, sum(a.total * b.P_11) as P_11, sum(a.total * b.P_12) as P_12,
                       
                        sum(a.total * (c.P_01 * d.batch_size)) as EP_01, sum(a.total * (c.P_02 * d.batch_size)) as EP_02, sum(a.total * (c.P_03 * d.batch_size)) as EP_03, 
                        sum(a.total * (c.P_04 * d.batch_size)) as EP_04, sum(a.total * (c.P_05 * d.batch_size)) as EP_05, sum(a.total * (c.P_06 * d.batch_size)) as EP_06, 
                        sum(a.total * (c.P_07 * d.batch_size)) as EP_07, sum(a.total * (c.P_08 * d.batch_size)) as EP_08, sum(a.total * (c.P_09 * d.batch_size)) as EP_09, 
                        sum(a.total * (c.P_10 * d.batch_size)) as EP_10, sum(a.total * (c.P_11 * d.batch_size)) as EP_11, sum(a.total * (c.P_12 * d.batch_size)) as EP_12',
            'join'   => [$table_prod .' b on a.parent_item = b.product_code type LEFT c.posting_code ="PRD"',
                         $table_prod .' c on a.parent_item = c.product_code and c.posting_code ="EPR" type LEFT',
                         'tbl_beginning_stock d on a.parent_item = d.budget_product_code type LEFT',
                         'tbl_beginning_stock_material e on a.component_item = e.material_code type LEFT',
                        ],
            'where' => [
                'a.tahun' => $tahun,
                'd.tahun' => $tahun,
                'e.tahun' => $tahun,
                'a.parent_item' => 'CIPTLRHPDM',
                'b.posting_code' => 'PRD',
                'c.posting_code' => 'EPR',
                // 'a.component_item' => 'CILBSPOTH1'
            ],
            'group_by' => 'a.component_item,a.material_name',
            'sort_by' => 'a.component_item'
        ];

        $prod = get_data('tbl_material_formula  a',$arr)->result();

        // debug($prod);die;

        foreach($prod as $s) {
            $data_prod = [
                'revision' => 0,
                'posting_code' => 'PRD',
                'material_code' => $s->component_item,
                'material_name' => $s->material_name,
                'um' => $s->um,
                'supplier' => $s->supplier ?? '',
                'moq' => $s->moq ?? 0,
                'order_multiple' => $s->order_multiple ?? 0,
                'P_01' => $s->P_01 ,
                'P_02' => $s->P_02,
                'P_03' => $s->P_03,
                'P_04' => $s->P_04,
                'P_05' => $s->P_05,
                'P_06' => $s->P_06,
                'P_07' => $s->P_07,
                'P_08' => $s->P_08,
                'P_09' => $s->P_09,
                'P_10' => $s->P_10,
                'P_11' => $s->P_11,
                'P_12' => $s->P_12,
            ];

            $cek = get_data($table_mat,[
                'where' => [
                    'revision' => 0,
                    'material_code' => $s->component_item,
                    'posting_code' => 'PRD',
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
                $data_cov[$field] = $s->m_cov;
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

            ///

        }

        /// stock awal
        $arrs = [
            'select' => 'a.material_code,a.material_name,a.um,a.supplier,a.moq,a.m_cov,a.order_multiple,a.total_stock',
            'join' => ['tbl_material_formula b on a.material_code = b.component_item and b.tahun ="'.$tahun.'"'],
            'where' => [
                'a.tahun' => $tahun,
                'b.tahun' => $tahun,
                'b.parent_item' => 'CIPTLRHPDM',
                'a.material_code !=' => '',
            ],
            'group_by' => 'a.material_code'
        ];

        $stock = get_data('tbl_beginning_stock_material a',$arrs)->result();

        if($stock) {
            foreach($stock as $s) {
                $data_stoa = [
                    'revision' => 0,
                    'posting_code' => 'STA',
                    'material_code' => $s->material_code,
                    'material_name' => $s->material_name,
                    'um' => $s->um,
                    'supplier' => $s->supplier ?? '',
                    'moq' => $s->moq ?? 0,
                    'order_multiple' => $s->order_multiple ?? 0,
                    'P_01' => $s->total_stock,
                ];

                $cek = get_data($table_mat,[
                    'where' => [
                        'revision' => 0,
                        'material_code' => $s->material_code,
                        'posting_code' => 'STA',
                    ],
                ])->row();

                if(!isset($cek->id)){
                    insert_data($table_mat, $data_stoa);
                }else{
                    update_data($table_mat, $data_stoa,['id'=>$cek->id]);
                }
            }

            /// end cari stock awal //
            
            // isi stock end //
            foreach($stock as $s) {
                $data_stoe = [
                    'revision' => 0,
                    'posting_code' => 'STE',
                    'material_code' => $s->material_code,
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
                        'material_code' => $s->material_code,
                        'posting_code' => 'STE',
                    ],
                ])->row();

                if(!isset($cek->id)){
                    insert_data($table_mat,$data_stoe);
                }else{
                    update_data($table_mat,$data_stoe,['id'=>$cek->id]);
                }

  
                $this->pembelian_awal($s->material_code,$tahun);
            }
        }

		render([
			'status'	=> 'success',
			'message'	=> 'MRP Process has benn succesfuly'
		],'json');	
	}

    function pembelian_awal($material_code, $tahun) {
        ini_set('memory_limit', '-1');
		ini_set('max_execution_time', 0);
        
        $table_mat = 'tbl_material_planning_' . $tahun ;

        $c = get_data($table_mat . ' a',[
            'select' => 'a.*,b.m_cov, b.total_stock, b.moq',
            'join'   => ['tbl_beginning_stock_material b on a.material_code = b.material_code and b.tahun ="'.$tahun.'" type LEFT',
                         'tbl_material_formula c on a.material_code = c.component_item and c.tahun ="'.$tahun.'"'
                        ],
            'where' => [
                'a.posting_code' => 'STA',
                'a.material_code' => $material_code,
                'c.parent_item' => 'CIPTLRHPDM',
            ],
        ])->row();

        if($c) {
           $cek_mat1 = get_data($table_mat,[
            'where' => [
                'material_code' => $material_code,
                'posting_code' => 'PBL',
            ],
            ])->row();

            $data_pbl = [
                'revision' => 0,
                'posting_code' => 'PBL',
                'material_code' => $material_code,
                'material_name' => $c->material_name,
                'um' => $c->um,
                'supplier' => $c->supplier ?? '',
                'moq' => $c->moq ?? 0,
                'order_multiple' => $c->order_multiple ?? 0,
            ];

            $field = '';
            for ($i = 1; $i <= 12; $i++) {
                $field = 'P_' . sprintf('%02d', $i);
                $data_pbl[$field] = 0;
            }
            

            if (!isset($cek_mat1->id)) {
                insert_data($table_mat, $data_pbl);
            } else {
                update_data($table_mat, $data_pbl, ['id' => $cek_mat1->id]);
            }
            
            $next_data = [
                'beginning_stock' => 0,
                'pembelian' => 0,
                'pemakaian' => 0,
                'produksi' => 0,
                'end_stock' => 0,
                'coverage' => 0,
            ];

            $data_produksi = get_data('tbl_material_planning_'.$tahun, [
                'where' => [
                    'material_code' => $material_code,
                    'posting_code' => 'PRD'
                ]
            ])->row_array();

             // if($c->batch_size > 0){
                for ($i = 1; $i <= 12; $i++) {
                    $produksi = $data_produksi['P_'.sprintf('%02d', $i)] ?? 0;
                    if($i == 1){
                        $tmp_data = $this->init_data($material_code, sprintf('%02d', $i), $tahun);
                        $tmp_data['beginning_stock'] = $c->total_stock ;
                    } else {
                        $tmp_data = [
                            'beginning_stock' => $next_data['beginning_stock'],
                            'pembelian' => $next_data['pembelian'],
                            'pemakaian' => $next_data['pemakaian'],
                            'produksi' => $next_data['produksi'],
                            'end_stock' => $next_data['end_stock'],
                            'coverage' => $next_data['coverage'],
                        ];
                    }

                    $value_end_stock = $tmp_data['beginning_stock'];
                    $value_coverage = 0;
                    $value_pembelian = $c->moq ?? 0;
                    $value_pemakaian = 0;
                    $average_produksi_per_4_month = 0;
                    $total_produksi = 0;
                    $pembagi = 0;
                    for($j=0;$j<4;$j++){
                        if($i+$j<13){
                            $total_produksi += $data_produksi['P_'.sprintf('%02d', $i+$j)] ?? 0;
                            $pembagi++;
                        }
                    }
                    


                    $average_produksi_per_4_month = 0;

                    if($total_produksi != 0 && $pembagi != 0){

                        $average_produksi_per_4_month = $total_produksi / $pembagi;
                        while($value_coverage < $c->m_cov){
                            $value_pembelian += $c->order_multiple ;
                            // $tmp_data['produksi'] = $produksi;
                            $value_end_stock = ($tmp_data['beginning_stock'] + $value_pembelian) - $tmp_data['produksi'];



                            $value_pemakaian = $value_pembelian + $tmp_data['beginning_stock'];

                            $total_produksi2 = 0;
                            for($j=0;$j<3;$j++){
                                if($i+$j<13){
                                    $total_produksi2 += $data_produksi['P_'.sprintf('%02d', $i+$j)] ?? 0;
                                    if($value_end_stock > $total_produksi2){
                                        $value_coverage = $value_end_stock / $average_produksi_per_4_month;
                                    }
                                }
                            }

                          
                            
                        }
                    } else {
                        // $value_xproduction = 0;
                        $value_pembelian = 0;
                    }

                    if(!($i+1>=13)){
                        $next_data = [
                            'beginning_stock' => $value_end_stock,
                            'produksi' => $data_produksi['P_'.sprintf('%02d', $i + 1)] ?? 0,
                            'pemakaian' => 0,
                            'end_stock' => 0,
                            'pembelian' => 0,
                            'coverage' => 0,
                        ];
                    }

                    # sales
                    update_data($table_mat, [
                        'P_'.sprintf('%02d', $i) => $tmp_data['beginning_stock'],
                    ], [
                        'material_code' => $material_code,
                        'posting_code' => 'STA'
                    ]);

                    # end stock
                    update_data($table_mat, [
                        'P_'.sprintf('%02d', $i) => $value_end_stock,
                    ], [
                        'material_code' => $material_code,
                        'posting_code' => 'STE'
                    ]);

                    # corverage
                    update_data($table_mat, [
                        'P_'.sprintf('%02d', $i) => $value_coverage,
                    ], [
                        'material_code' => $material_code,
                        'posting_code' => 'COV'
                    ]);

                    # pembelian
                    update_data($table_mat, [
                        'P_'.sprintf('%02d', $i) => $value_pembelian,
                    ], [
                        'material_code' => $material_code,
                        'posting_code' => 'PBL'
                    ]);

                    # pemakaian
                    update_data($table_mat, [
                        'P_'.sprintf('%02d', $i) => $value_pemakaian,
                        'update_at' => date('Y-m-d H:i:s')
                    ], [
                        'material_code' => $material_code,
                        'posting_code' => 'PMK',
                    ]);

                }
            // }

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
            'beginning_stock' => 0,
            'produksi' => 0,
            'pemakaian' => 0,
            'end_stock' => 0,
            'pembelian' => 0,
            'coverage' => 0,
        ];

        if($data_mat){
            foreach($data_mat as $v){
                switch($v['posting_code']){
                    case 'PRD':
                        $data['produksi'] = $v['value'];
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
                    case 'PBL':
                        $data['pembelian'] = $v['value'];
                    break;
                    case 'PMK':
                        $data['pemakaian'] = $v['value'];
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