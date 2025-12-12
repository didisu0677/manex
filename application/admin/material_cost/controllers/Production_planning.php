<?php if (! defined('BASEPATH')) exit('No direct script access allowed');

class Production_planning extends BE_Controller
{

    function __construct()
    {
        parent::__construct();
    }

    function index()
    {
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
                'b.cost_centre !=' => '',
            ],
            'group_by' => 'b.id, b.cost_centre',
            'sort_by' => 'b.id',
        ];


        $data['cc'] = get_data('tbl_fact_product a', $arr)->result();

        $data['submit'] = FALSE;

        $s = get_data('tbl_scm_submit', [
            'where' => [
                'code_submit' => 'PROD',
                'is_submit' => 1,
                'tahun' => user('tahun_budget')
            ],
        ])->row();

        if (isset($s->id)) {
            $data['submit'] = TRUE;
        }

        // debug($data);die;

        render($data);
    }

    function data($tahun = "", $cost_centre = "", $tipe = 'table')
    {
        ini_set('memory_limit', '-1');
        ini_set('max_execution_time', 0);

        $table = 'tbl_budget_production';
        $table_prod = 'tbl_production_planning_' . $tahun;

        $arr = [
            'select' => 'a.cost_centre as kode, b.id, b.cost_centre, c.kapasitas,
                        WD_01,WD_02,WD_03,WD_04,WD_05,WD_06,WD_07,WD_08,WD_09,WD_10,WD_11,WD_12',
            'join' => [
                'tbl_fact_cost_centre b on a.cost_centre = b.kode type LEFT',
                'tbl_kapasitas_produksi c on a.cost_centre = c.cost_centre type LEFT'
            ],
            'where' => [
                'a.is_active' => 1,
                'a.id_cost_centre !=' => 0,
                'b.cost_centre !=' => ''
                // 'a.cost_centre' => '2135',
                // 'a.code' => 'CIU9N1PNDM'
            ],
            // 'group_by' => 'a.id_cost_centre',
            'group_by' => 'b.cost_centre',
            'sort_by' => 'b.id',
        ];

        if ($cost_centre && $cost_centre != 'ALL') $arr['where']['a.cost_centre'] = $cost_centre;


        $data['grup'][0] = get_data('tbl_fact_product a', $arr)->result();

        $data['kprod'] = [];
        $data['wday'] = [];
        $data['sprod'] = [];
        foreach ($data['grup'][0] as $m0) {
            $data['kprod'][$m0->id] = $m0->kapasitas;

            $field1 = '';
            $field2 = '';
            for ($i = 1; $i <= 12; $i++) {
                $field1 = 'WD_' . sprintf('%02d', $i);
                $data['wday'][$m0->id][$i] = $m0->$field1;
                $data['sprod'][$m0->id][$i] = ($m0->kapasitas * $m0->$field1);

                if ($i == 1) {
                    $field2 = 'sum(CASE WHEN ' . ' b.P_' . sprintf('%02d', $i)  . ' = 0 OR NULL' . ' THEN ' . ' a.P_' . sprintf('%02d', $i) . ' ELSE ' . 'b.P_' . sprintf('%02d', $i) . ' * c.batch_size' . ' END)' . ' AS ' . 'P_' . sprintf('%02d', $i);
                } else {
                    $field2 .=  ' , ' . ' sum(CASE WHEN ' . ' b.P_' . sprintf('%02d', $i)  . ' = 0 or NULL' . ' THEN ' . ' a.P_' . sprintf('%02d', $i) . ' ELSE ' . 'b.P_' . sprintf('%02d', $i) . ' * c.batch_size' . ' END)' . ' AS ' . 'P_' . sprintf('%02d', $i);
                }
            }


            $data['prod'][$m0->id] = get_data($table_prod . ' a', [
                'select' => 'a.id_cost_centre,a.cost_centre, ' . $field2,
                'join'   =>  [
                    $table_prod . ' b on a.product_code = b.product_code and b.posting_code ="EPR" type LEFT',
                    'tbl_beginning_stock c on a.product_code = c.budget_product_code and tahun ="' . $tahun . '" type LEFT',
                ],
                'where'  => [
                    'a.posting_code' => 'PRD',
                    'a.id_cost_centre' => $m0->id,
                ],
                'group_by' => 'id_cost_centre',
            ])->row_array();

            // debug($data['prod']) ;die;

            // $cproduk = get_data('tbl_fact_product a',[
            //     'where' => [
            //         'a.is_active' => 1,
            //         'a.id_cost_centre' => $m0->id,
            //     ],
            //     'sort_by' => 'a.id_cost_centre'
            // ])->result();

            // foreach($cproduk as $p) {   
            //     $cek = get_data($table . ' a',[
            //         'select' => 'a.*',
            //         'where' => [
            //             'a.tahun' => $tahun,
            //             'a.budget_product_code' => $p->code,
            //             'a.product_line' => $p->product_line,
            //         ]
            //     ])->row();
            //     if(!isset($cek->id)){
            //         insert_data($table,
            //         ['tahun' => $tahun, 'id_cost_centre' => $p->id_cost_centre ,'divisi' => $p->divisi, 'product_line' => $p->product_line, 'id_budget_product'=>$p->id, 'budget_product_code'=>$p->code, 
            //         'budget_product_name' => $p->product_name, 'category' => $p->sub_product]
            //     );
            //     }
            // }

            $data['produk'][$m0->id] = get_data($table_prod . ' a', [
                'select' => 'a.*,b.code,b.product_name,b.destination, c.abbreviation as initial, c.cost_centre, d.batch_size',
                'join' =>  [
                    'tbl_fact_product b on a.product_code = b.code',
                    'tbl_fact_cost_centre c on a.id_cost_centre = c.id type LEFT',
                    'tbl_beginning_stock d on a.product_code = d.budget_product_code and d.tahun="' . $tahun . '"'
                ],
                'where' => [
                    // 'a.tahun' => $tahun,
                    'd.tahun' => $tahun,
                    'd.is_active' => 1,
                    'a.id_cost_centre' => $m0->id,
                    'a.posting_code' => 'STA'
                    // 'a.budget_product_code' => 'CIU9N1PNDM'
                ],
                'sort_by' => 'a.id_cost_centre'
            ])->result();


            $data['sales'][$m0->id] = get_data($table_prod . ' a', [
                'select' => 'a.*',
                'join' =>  [
                    'tbl_fact_product b on a.product_code = b.code',
                    'tbl_fact_cost_centre c on a.cost_centre = c.kode type LEFT',
                ],
                'where' => [
                    'c.id' => $m0->id,
                    'a.posting_code' => 'SLS',
                ]
            ])->result();

            $data['sto_awal'][$m0->id] = get_data($table_prod . ' a', [
                'select' => 'a.*',
                'join' =>  [
                    'tbl_fact_product b on a.product_code = b.code',
                    'tbl_fact_cost_centre c on a.cost_centre = c.kode type LEFT',
                ],
                'where' => [
                    'c.id' => $m0->id,
                    'a.posting_code' => 'STA',
                ]
            ])->result();

            $data['sto_end'][$m0->id] = get_data($table_prod . ' a', [
                'select' => 'a.*',
                'join' =>  [
                    'tbl_fact_product b on a.product_code = b.code',
                    'tbl_fact_cost_centre c on a.cost_centre = c.kode type LEFT',
                ],
                'where' => [
                    'c.id' => $m0->id,
                    'a.posting_code' => 'STE',
                ]
            ])->result();

            $data['m_cov'][$m0->id] = get_data($table_prod . ' a', [
                'select' => 'a.*',
                'join' =>  [
                    'tbl_fact_product b on a.product_code = b.code',
                    'tbl_fact_cost_centre c on a.cost_centre = c.kode type LEFT',
                ],
                'where' => [
                    'c.id' => $m0->id,
                    'a.posting_code' => 'COV',
                ]
            ])->result();

            $data['xprod'][$m0->id] = get_data($table_prod . ' a', [
                'select' => 'a.*',
                'join' =>  [
                    'tbl_fact_product b on a.product_code = b.code',
                    'tbl_fact_cost_centre c on a.cost_centre = c.kode type LEFT',
                ],
                'where' => [
                    'c.id' => $m0->id,
                    'a.posting_code' => 'XPR',
                ]
            ])->result();

            // Produksi
            $data['prd'][$m0->id] = get_data($table_prod . ' a', [
                'select' => 'a.*',
                'join' =>  [
                    'tbl_fact_product b on a.product_code = b.code',
                    'tbl_fact_cost_centre c on a.cost_centre = c.kode type LEFT',
                ],
                'where' => [
                    'c.id' => $m0->id,
                    'a.posting_code' => 'PRD',
                ]
            ])->result();

            // Edited produksi
            $data['epd'][$m0->id] = get_data($table_prod . ' a', [
                'select' => 'a.*',
                'join' =>  [
                    'tbl_fact_product b on a.product_code = b.code',
                    'tbl_fact_cost_centre c on a.cost_centre = c.kode type LEFT',
                ],
                'where' => [
                    'c.id' => $m0->id,
                    'a.posting_code' => 'EPD',
                ]
            ])->result();

            $data['epr'][$m0->id] = get_data($table_prod . ' a', [
                'select' => 'a.*,a.P_01+a.P_02+a.P_03+a.P_04+a.P_05+a.P_06+a.P_07+a.P_08+a.P_09+a.P_10+a.P_11+a.P_12 as total_p',
                'join' =>  [
                    'tbl_fact_product b on a.product_code = b.code',
                    'tbl_fact_cost_centre c on a.cost_centre = c.kode type LEFT',
                ],
                'where' => [
                    'c.id' => $m0->id,
                    'a.posting_code' => 'EPR',
                ]
            ])->result();
        }

        //edit produksi//
        // $data['epr'] = get_data('tbl_budget_production a',[
        //     'select' => 'a.id, b.P_01, b.P_02,b.P_03,b.P_04,b.P_05,b.P_06,b.P_07,b.P_08,b.P_09,b.P_10,b.P_11,b.P_12',
        //     'join' => $table_prod . ' b on a.budget_product_code = b.product_code',
        //     'where' => [
        //         'a.tahun' => $tahun,
        //         'b.posting_code' => 'EPR',
        //         // 'a.budget_product_code' => 'CIU9N1PNDM',
        //     ],
        // ])->result_array();

        $response    = array(
            'table'        => $this->load->view('material_cost/production_planning/table', $data, true),
            'epr' => $data['epr'],
        );

        render($response, 'json');
    }

    function proses()
    {
        ini_set('memory_limit', '-1');
        ini_set('max_execution_time', 0);

        $tahun = post('tahun');
        $factory = post('factory');

        $table_sales = 'tbl_budget_qtysales_' . $tahun;
        $table_prod = 'tbl_production_planning_' . $tahun;

        // cari budget sales //
        $field = '';
        for ($i = 1; $i <= 12; $i++) {
            if ($field == '') {
                $field = 'sum(' . 'B_' . sprintf('%02d', $i) . ')' . ' as ' . 'B_' . sprintf('%02d', $i);
            } else {
                $field = $field . ' , ' . 'sum(' . 'B_' . sprintf('%02d', $i) . ')' . ' as ' . 'B_' . sprintf('%02d', $i);
            }
        }
        $arr = [
            'select' => 'a.budget_product_code, a.budget_product_name, b.product_line,b.destination,  
                        b.cost_centre,c.id as id_cost_centre, d.batch_size, ' . $field,
            'join'   => [
                'tbl_fact_product b on a.budget_product_code = b.code type LEFT',
                'tbl_fact_cost_centre c on b.cost_centre = c.kode type LEFT',
                'tbl_beginning_stock d on a.budget_product_code = d.budget_product_code and d.tahun ="' . $tahun . '" type LEFT'
            ],
            'where' => [
                'a.tahun' => $tahun,
                'd.is_active' => 1,
                // 'a.budget_product_code' => 'CIU9N1PNDM',
            ],
            'group_by' => 'a.budget_product_code'
        ];

        if (!empty($factory) && $factory != 'ALL') $arr['where']['b.cost_centre'] = $factory;

        $sales = get_data($table_sales . ' a', $arr)->result();

        foreach ($sales as $s) {
            $data_sls = [
                'revision' => 0,
                'posting_code' => 'SLS',
                'product_code' => $s->budget_product_code,
                'product_name' => $s->budget_product_name,
                'cost_centre' => $s->cost_centre,
                'dest' => $s->destination,
                'batch' => $s->batch_size,
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

            $cek = get_data($table_prod, [
                'where' => [
                    'revision' => 0,
                    'product_code' => $s->budget_product_code,
                    'posting_code' => 'SLS',
                ],
            ])->row();

            if (!isset($cek->id)) {
                insert_data($table_prod, $data_sls);
            } else {
                update_data($table_prod, $data_sls, ['id' => $cek->id]);
            }
        }

        // end cari budget sales //

        // cari stock awal
        $arrs = [
            'select' => 'a.budget_product_code, a.budget_product_name, b.product_line,b.destination,  
                        b.cost_centre,c.id as id_cost_centre, a.total_stock, a.batch_size',
            'join'   => [
                'tbl_fact_product b on a.budget_product_code = b.code type LEFT',
                'tbl_fact_cost_centre c on b.id_cost_centre = c.id type LEFT',
            ],
            'where' => [
                'a.tahun' => $tahun,
                'a.is_active' => 1,
                // 'a.budget_product_code' => 'CIU9N1PNDM'
            ],
            'group_by' => 'a.budget_product_code'
        ];

        if (!empty($factory) && $factory != 'ALL') $arr['where']['b.cost_centre'] = $factory;

        $stock = get_data('tbl_beginning_stock a', $arrs)->result();

        if ($stock) {
            foreach ($stock as $s) {
                $data_sta = [
                    'revision' => 0,
                    'posting_code' => 'STA',
                    'product_code' => $s->budget_product_code,
                    'product_name' => $s->budget_product_name,
                    'cost_centre' => $s->cost_centre,
                    'batch' => $s->batch_size,
                    'dest' => $s->destination,
                    'id_cost_centre' => ($s->id_cost_centre == null) ? 0 : $s->id_cost_centre,
                    'product_line' => $s->product_line,
                    'P_01' => $s->total_stock,
                ];

                $cek1 = get_data($table_prod, [
                    'where' => [
                        'revision' => 0,
                        'product_code' => $s->budget_product_code,
                        'posting_code' => 'STA',
                    ],
                ])->row();

                if (!isset($cek1->id)) {
                    insert_data($table_prod, $data_sta);
                } else {
                    update_data($table_prod, $data_sta, ['id' => $cek1->id]);
                }

                $data_ste = [
                    'revision' => 0,
                    'posting_code' => 'STE',
                    'product_code' => $s->budget_product_code,
                    'product_name' => $s->budget_product_name,
                    'cost_centre' => $s->cost_centre,
                    'batch' => $s->batch_size,
                    'dest' => $s->destination,
                    'id_cost_centre' => ($s->id_cost_centre == null) ? 0 : $s->id_cost_centre,
                    'product_line' => $s->product_line,
                    'P_01' => 0,
                ];

                $cek2 = get_data($table_prod, [
                    'where' => [
                        'revision' => 0,
                        'product_code' => $s->budget_product_code,
                        'posting_code' => 'STE',
                    ],
                ])->row();

                if (!isset($cek2->id)) {
                    insert_data($table_prod, $data_ste);
                } else {
                    update_data($table_prod, $data_ste, ['id' => $cek2->id]);
                }


                // $this->end_stock($s->budget_product_code,$tahun);
                // $this->month_coverage($s->budget_product_code,$tahun);
                // $this->production0($s->budget_product_code,$tahun);

                $this->produksi_awal($s->budget_product_code, $tahun);
            }

            /// end cari stock awal //

            // isi stock end //
            // foreach($stock as $s) {

            // }
        }

        render([
            'status'    => 'success',
            'message'    => 'MRP Process has benn succesfuly'
        ], 'json');
    }

    function save_perubahan()
    {

        $tahun = post('tahun');

        $table = 'tbl_production_planning_' . $tahun;

        $data   = json_decode(post('json'), true);

        $this->save_xproduction_planning();
        $this->save_production_planning(false);

        // set field edited
        foreach ($data as $id => $record) {
            $cek_produk = get_data($table, 'id', $id)->row();
            if (isset($cek_produk->product_code)) {
                $result = $record;
                foreach ($result as $r => $v) {
                    update_data($table, [
                        'is_active' => 1
                    ], ['product_code' => $cek_produk->product_code, 'posting_code' => 'EPR']);
                }
            }
        }
    }

    function xxend_stock($product_code = "", $tahun = "")
    {
        ini_set('memory_limit', '-1');
        ini_set('max_execution_time', 0);
        $table_prod = 'tbl_production_planning_' . $tahun;
        $p = get_data($table_prod . ' a', [
            'select' => 'a.product_code,a.product_name,a.cost_centre,a.id_cost_centre,a.product_line,a.dest,d.batch_size,
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
            'join'   => [
                'tbl_fact_product b on a.product_code = b.code type LEFT',
                'tbl_fact_cost_centre c on a.id_cost_centre = c.id type LEFT',
                'tbl_beginning_stock d on a.product_code = d.budget_product_code and d.tahun ="' . $tahun . '" type LEFT'
            ],
            'where' => [
                'a.product_code' => $product_code,
                'd.is_active' => 1
            ],
        ])->row();

        // debug($p);die;


        if ($p) {

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
                'batch' => $p->batch_size,
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
                'batch' => $p->batch_size,
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


                if ($i == 1) {
                    $$field4 = ($p->$field3 + $p->$field5) - $p->$field2;
                    $data_sls[$field0] = $$field4;
                } else {
                    $field01 = 'COV_' . sprintf('%02d', $i);
                    $field02 = 'SLS_' . sprintf('%02d', ($i - 1));
                    $field03 = 'STA_' . sprintf('%02d', ($i - 1));
                    $field04 = 'STE_' . sprintf('%02d', ($i - 1));
                    $field05 = 'PRD_' . sprintf('%02d', ($i - 1));
                    $field06 = 'XPR_' . sprintf('%02d', ($i - 1));


                    $$field04 = ($p->$field03 + $p->$field05) - $p->$field02;
                    $data_sla[$field0] = $$field04;
                    $data_sls[$field0] = ($$field04 + $p->$field5) - $p->$field2;
                }

                $cek = get_data($table_prod, [
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
                    if ($ix == 1) {
                        $data_sls['posting_code'] = 'STE';
                        $cek = get_data($table_prod, [
                            'where' => [
                                'product_code' => $product_code,
                                'posting_code' => 'STE'
                            ],
                        ])->row();

                        if (!isset($cek->id)) {
                            insert_data($table_prod, $data_sls);
                        } else {
                            update_data($table_prod, $data_sls, ['id' => $cek->id]);
                        }
                    } else {
                        $data_sla['posting_code'] = 'STA';
                        $ceka = get_data($table_prod, [
                            'where' => [
                                'product_code' => $product_code,
                                'posting_code' => 'STA'
                            ],
                        ])->row();

                        if (!isset($ceka->id)) {
                            insert_data($table_prod, $data_sla);
                        } else {
                            update_data($table_prod, $data_sla, ['id' => $ceka->id]);
                        }
                    }
                }
            }
        }
    }

    // end stodk end //

    // month coverage //
    function month_coverage($product_code = "", $tahun = "")
    {
        ini_set('memory_limit', '-1');
        ini_set('max_execution_time', 0);
        $table_prod = 'tbl_production_planning_' . $tahun;
        $select1 = '';
        for ($i = 1; $i <= 12; $i++) {
            if ($select1 == "") {
                $select1 = "MAX(CASE WHEN posting_code = 'SLS' THEN " . "P_" . sprintf('%02d', $i) . " END) AS " . "S_" . sprintf('%02d', $i);
            } else {
                $select1 .= " ," . "MAX(CASE WHEN posting_code = 'SLS' THEN " . "P_" . sprintf('%02d', $i) . " END) AS " . "S_" . sprintf('%02d', $i);
            }
        }

        $select2 = '';
        for ($i = 1; $i <= 12; $i++) {
            if ($select2 == "") {
                $select2 = "MAX(CASE WHEN a.posting_code = 'STE' THEN " . "P_" . sprintf('%02d', $i) . " END) AS " . "E_" . sprintf('%02d', $i);
            } else {
                $select2 .= " ," . "MAX(CASE WHEN a.posting_code = 'STE' THEN " . "P_" . sprintf('%02d', $i) . " END) AS " . "E_" . sprintf('%02d', $i);
            }
        }

        $select = $select1 . ' , ' . $select2;

        $prod = get_data($table_prod . ' a', [
            'select' => 'a.id,a.product_code,a.product_name,a.cost_centre,a.id_cost_centre,a.product_line,
                         b.destination, d.batch_size, ' . $select,
            'join'   => [
                'tbl_fact_product b on a.product_code = b.code type LEFT',
                'tbl_fact_cost_centre c on a.id_cost_centre = c.id type LEFT',
                'tbl_beginning_stock d on a.product_code = d.budget_product_code and d.tahun ="' . $tahun . '" type LEFT'
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
            'batch' => $prod->$batch_size,
            'id_cost_centre' => ($prod->id_cost_centre == null) ? 0 : $prod->id_cost_centre,
            'product_line' => $prod->product_line,
        ];

        for ($i = 1; $i <= 12; $i++) {
            $mCov = 'MC_' . sprintf('%02d', $i);
            $field = 'P_' . sprintf('%02d', $i);
            if ($i == 1) {
                $$mCov = (($prod->S_01 + $prod->S_02 + $prod->S_03 + $prod->S_04) / 4) != 0 ? $prod->E_01 / (($prod->S_01 + $prod->S_02 + $prod->S_03 + $prod->S_04) / 4) : 0;
            } elseif ($i == 2) {
                $$mCov = (($prod->S_02 + $prod->S_03 + $prod->S_04 + $prod->S_05) / 4) != 0 ? $prod->E_02 / (($prod->S_02 + $prod->S_03 + $prod->S_04 + $prod->S_05) / 4) : 0;
            } elseif ($i == 3) {
                $$mCov = (($prod->S_03 + $prod->S_04 + $prod->S_05 + $prod->S_06) / 4) != 0 ? $prod->E_03 / (($prod->S_03 + $prod->S_04 + $prod->S_05 + $prod->S_06) / 4) : 0;
            } elseif ($i == 4) {
                $$mCov = (($prod->S_04 + $prod->S_05 + $prod->S_06 + $prod->S_07) / 4) != 0 ? $prod->E_04 / (($prod->S_04 + $prod->S_05 + $prod->S_06 + $prod->S_07) / 4) : 0;
            } elseif ($i == 5) {
                $$mCov = (($prod->S_05 + $prod->S_06 + $prod->S_07 + $prod->S_08) / 4) != 0 ? $prod->E_05 / (($prod->S_05 + $prod->S_06 + $prod->S_07 + $prod->S_08) / 4) : 0;
            } elseif ($i == 6) {
                $$mCov = (($prod->S_06 + $prod->S_07 + $prod->S_08 + $prod->S_09) / 4) != 0 ? $prod->E_06 / (($prod->S_06 + $prod->S_07 + $prod->S_08 + $prod->S_09) / 4) : 0;
            } elseif ($i == 7) {
                $$mCov = (($prod->S_07 + $prod->S_08 + $prod->S_09 + $prod->S_10) / 4) != 0 ? $prod->E_07 / (($prod->S_07 + $prod->S_08 + $prod->S_09 + $prod->S_10) / 4) : 0;
            } elseif ($i == 8) {
                $$mCov = (($prod->S_08 + $prod->S_09 + $prod->S_10 + $prod->S_11) / 4) != 0 ? $prod->E_08 / (($prod->S_08 + $prod->S_09 + $prod->S_10 + $prod->S_11) / 4) : 0;
            } elseif ($i == 9) {
                $$mCov = (($prod->S_09 + $prod->S_10 + $prod->S_11 + $prod->S_12) / 4) != 0 ? $prod->E_09 / (($prod->S_09 + $prod->S_10 + $prod->S_11 + $prod->S_12) / 4) : 0;
            } elseif ($i == 10) {
                $$mCov = (($prod->S_10 + $prod->S_11 + $prod->S_12) / 3) != 0 ? $prod->E_10 / (($prod->S_10 + $prod->S_11 + $prod->S_12) / 3) : 0;
            } elseif ($i == 11) {
                $$mCov = (($prod->S_11 + $prod->S_12) / 2) != 0 ? $prod->E_11 / (($prod->S_11 + $prod->S_12) / 2) : 0;
            } else {
                $$mCov = $prod->S_12 != 0 ? $prod->E_12 / $prod->S_12 : 0;
            }
            $data_sls[$field] = $$mCov;
        }

        $cek = get_data($table_prod, [
            'where' => [
                'product_code' => $product_code,
                'posting_code' => 'COV',
            ],
        ])->row();

        if (!isset($cek->id)) {
            insert_data($table_prod, $data_sls);
        } else {
            update_data($table_prod, $data_sls, ['id' => $cek->id]);
        }
    }


    function produksi_awal($product_code, $tahun)
    {

        $table_prod = 'tbl_production_planning_' . $tahun;

        $c = get_data($table_prod . ' a', [
            'select' => 'a.*,b.destination, d.batch_size, d.total_stock, d.batch_size',
            'join'   => [
                'tbl_fact_product b on a.product_code = b.code type LEFT',
                'tbl_fact_cost_centre c on a.id_cost_centre = c.id type LEFT',
                'tbl_beginning_stock d on a.product_code = d.budget_product_code and d.tahun ="' . $tahun . '" type LEFT'
            ],
            'where' => [
                'a.posting_code' => 'STA',
                'a.product_code' => $product_code,
                'd.is_active' => 1
            ],
        ])->row();


        if ($c) {
            $list_posting_code = ['COV', 'XPR', 'EPR', 'EPD', 'PRD'];
            foreach ($list_posting_code as $pc) {
                $cek_prod = get_data($table_prod, [
                    'where' => [
                        'product_code' => $product_code,
                        'posting_code' => $pc,
                    ],
                ])->row();
                $data_prod = [
                    'revision' => 0,
                    'posting_code' => $pc,
                    'product_code' => $product_code,
                    'product_name' => $c->product_name,
                    'cost_centre' => $c->cost_centre,
                    'dest' => $c->destination,
                    'batch' => $c->batch_size,
                    'id_cost_centre' => ($c->id_cost_centre == null) ? 0 : $c->id_cost_centre,
                    'product_line' => $c->product_line,
                ];

                $field = '';
                for ($i = 1; $i <= 12; $i++) {
                    $field = 'P_' . sprintf('%02d', $i);
                    $data_prod[$field] = 0;
                }

                if (!isset($cek_prod->id)) {
                    insert_data($table_prod, $data_prod);
                }
            }


            $field = "";
            $next_data = [
                'sales' => 0,
                'beginning_stock' => 0,
                'end_stock' => 0,
                'coverage' => 0,
                'production' => 0,
                'x_production' => 0,
            ];

            $data_sales = get_data('tbl_production_planning_' . $tahun, [
                'where' => [
                    'product_code' => $product_code,
                    'posting_code' => 'SLS'
                ]
            ])->row_array();
            if ($c->batch_size > 0) {
                for ($i = 1; $i <= 12; $i++) {
                    $sales = @$data_sales['P_' . sprintf('%02d', $i)] ?? 0;
                    if ($i == 1) {
                        $tmp_data = $this->init_data($product_code, sprintf('%02d', $i), $tahun);
                        $tmp_data['beginning_stock'] = $c->total_stock;
                    } else {
                        $tmp_data = [
                            'sales' => $next_data['sales'],
                            'beginning_stock' => $next_data['beginning_stock'],
                            'end_stock' => $next_data['end_stock'],
                            'coverage' => $next_data['coverage'],
                            'production' => $next_data['production'],
                            'x_production' => $next_data['x_production'],
                        ];
                    }

                    // cari data yang di edit 
                    $cari_epr = get_data('tbl_production_planning_' . $tahun, [
                        'select' => 'P_' . sprintf('%02d', $i) . ' as jml',
                        'where' => [
                            'product_code' => $product_code,
                            'posting_code' => 'EPR'
                        ]
                    ])->row_array();

                    // debug($cari_epr);die;

                    // if(isset($cari_epr['jml']) && $cari_epr['jml'] > 0) {
                    //     $value_xproduction = $cari_epr['jml'];
                    // }else{
                    $value_xproduction = -1;
                    // }
                    $value_end_stock = 0;
                    $value_coverage = 0;
                    $value_production = 0;

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

                        // if(isset($cari_epr['jml']) && $cari_epr['jml'] > 0) {
                        //     $value_xproduction = $cari_epr['jml'];
                        // }else{
                        while ($value_coverage < setting('month_coverage')) {
                            $value_xproduction++;

                            $tmp_data['sales'] = $sales;
                            $value_production = $value_xproduction * $c->batch_size;
                            $value_end_stock = $tmp_data['beginning_stock'] + $value_production - $tmp_data['sales'];

                            if ($value_end_stock != 0 && $average_sales_per_4_month != 0) {
                                $value_coverage = $value_end_stock / $average_sales_per_4_month;
                            } else {
                                break;
                            }
                        }
                        //     }
                    } else {
                        $value_xproduction = 0;
                    }

                    if (!($i + 1 >= 13)) {
                        $next_data = [
                            'sales' => @$data_sales['P_' . sprintf('%02d', $i + 1)] ?? 0,
                            'beginning_stock' => $value_end_stock,
                            'end_stock' => 0,
                            'coverage' => 0,
                            'production' => 0,
                            'x_production' => 0
                        ];
                    }

                    # sales
                    update_data($table_prod, [
                        'P_' . sprintf('%02d', $i) => $tmp_data['beginning_stock'],
                    ], [
                        'product_code' => $product_code,
                        'posting_code' => 'STA'
                    ]);

                    # end stock
                    update_data($table_prod, [
                        'P_' . sprintf('%02d', $i) => $value_end_stock,
                    ], [
                        'product_code' => $product_code,
                        'posting_code' => 'STE'
                    ]);

                    # corverage
                    update_data($table_prod, [
                        'P_' . sprintf('%02d', $i) => $value_coverage,
                    ], [
                        'product_code' => $product_code,
                        'posting_code' => 'COV'
                    ]);

                    $data_epd = get_data($table_prod, [
                        'where' => [
                            'product_code' => $product_code,
                            'posting_code' => 'EPD'
                        ]
                    ])->row_array();

                    if(empty($data_epd) || @$data_epd['P_' . sprintf('%02d', $i)] <= 0){
                        # production
                        update_data($table_prod, [
                            'P_' . sprintf('%02d', $i) => $value_production,
                        ], [
                            'product_code' => $product_code,
                            'posting_code' => 'PRD'
                        ]);
                    }

                    # x production
                    update_data($table_prod, [
                        'P_' . sprintf('%02d', $i) => $value_xproduction,
                        'update_at' => date('Y-m-d H:i:s')
                    ], [
                        'product_code' => $product_code,
                        'posting_code' => 'XPR',
                    ]);
                }
            }

            # Save X Production
            // if (!isset($cek_xprod->id)) {
            //     insert_data($table_prod, $data_xprod);
            // } else {
            //     update_data($table_prod, $data_xprod, ['id' => $cek_xprod->id]);
            // }

            // $this->xxend_stock($product_code, $tahun);
            // $this->month_coverage($product_code, $tahun);
        }
    }

    # init data production planning
    private function init_data($product_code, $month, $year)
    {

        $data_prod = get_data('tbl_production_planning_' . $year . ' a', [
            'select' => 'P_' . sprintf('%02d', $month) . ' as value, posting_code',
            'where' => [
                'a.product_code' => $product_code
            ]
        ])->result_array();

        $data = [
            'sales' => 0,
            'beginning_stock' => 0,
            'end_stock' => 0,
            'coverage' => 0,
            'production' => 0,
            'x_production' => 0,
        ];

        if ($data_prod) {
            foreach ($data_prod as $v) {
                switch ($v['posting_code']) {
                    case 'SLS':
                        $data['sales'] = $v['value'];
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
                    case 'PRD':
                        $data['production'] = $v['value'];
                        break;
                    case 'XPR':
                        $data['x_production'] = $v['value'];
                        break;
                        // case 'EPR':
                        //     $data['x_production'] = $v['value'];
                        // break;
                }
            }
        }

        return $data;
    }

    public function submit_production()
    {
        // render([
        //     'status'    => 'success',
        //     'message'    => 'Submit Production has benn succesfuly'
        // ], 'json');

        $this->save_production_planning(true, true);
    }

    private function save_production_planning($save_budget = true, $is_production = true)
    {

        $tahun = post('tahun');
        $table = 'tbl_production_planning_' . $tahun;

        $value = $this->input->post('production_value') ?? [];
        $product = $this->input->post('production_product') ?? [];
        $month = $this->input->post('production_month') ?? [];
        $edit = $this->input->post('production_edit') ?? [];

        // debug(post());
        foreach ($value as $k => $v) {
            // if(isset($product[$k]) && isset($month[$k])){
            $detail_product = get_data('tbl_fact_product fp', [
                'select' => 'fp.*',
                'where' => [
                    'fp.code' => $product[$k]
                ],
                'join' => [
                    'tbl_beginning_stock bs ON bs.budget_product_code = fp.code and bs.tahun = ' . $tahun . ' type left'
                ]
            ])->row_array();
            // debug($detail_product);

            if ($detail_product) {

                // untuk menyimpan data kedalam budget production
                if ($save_budget) {
                    $table_budget = 'tbl_budget_production_dev';
                    $cek_prd = get_data($table_budget, [
                        'where' => [
                            'tahun' => $tahun,
                            'budget_product_code' => $detail_product['code']
                        ]
                    ])->row_array();

                    if ($cek_prd) {
                        $data_update = [
                            'B_' . sprintf('%02d', $month[$k]) => $value[$k],
                        ];
                        update_data($table_budget, $data_update, 'id', $cek_prd['id']);
                    } else {
                        $data_insert = [
                            'tahun' => $tahun,
                            'id_cost_centre' => $detail_product['id_cost_centre'],
                            'product_line' => $detail_product['product_line'],
                            'divisi' => $detail_product['divisi'],
                            'category' => $detail_product['sub_product'],
                            'id_budget_product' => $detail_product['id'],
                            'budget_product_code' => $detail_product['code'],
                            'budget_product_name' => $detail_product['product_name'],
                            'id_user' => 0, // default 0
                            'nip' => '', // default kosong
                            'B_' . sprintf('%02d', $month[$k]) => $value[$k],
                        ];
                        insert_data($table_budget, $data_insert);
                    }
                } else {
                    // untuk menyimpan data production kedalam production planning
                    $cek_prd = get_data($table, [
                        'where' => [
                            'posting_code' => 'PRD',
                            'product_code' => $detail_product['code'],
                        ]
                    ])->row_array();

                    if ($cek_prd) {
                        $data_update = [
                            'P_' . sprintf('%02d', $month[$k]) => $value[$k],
                        ];
                        update_data($table, $data_update, 'id', $cek_prd['id']);
                    } else {
                        $data_insert = [
                            'revision' => 0,
                            'product_code' => $detail_product['code'],
                            'product_name' => $detail_product['product_name'],
                            'cost_centre' => $detail_product['cost_centre'],
                            'id_cost_centre' => $detail_product['id_cost_centre'],
                            'product_line' => $detail_product['product_line'],
                            'dest' => $detail_product['destination'],
                            'batch' => 0,
                            'posting_code' => 'PRD',
                            'P_' . sprintf('%02d', $month[$k]) => $value[$k]
                        ];

                        insert_data($table, $data_insert);
                    }
                    
                    if (@$edit[$k] == '1') {
                        $cek_epd = get_data($table, [
                            'where' => [
                                'posting_code' => 'EPD',
                                'product_code' => $detail_product['code'],
                            ]
                        ])->row_array();
    
                        if ($cek_epd) {
                            $data_update = [
                                // 'P_'.sprintf('%02d', $month[$k]) => $value[$k]
                                'P_' . sprintf('%02d', $month[$k]) => 1
                            ];
                            update_data($table, $data_update, 'id', $cek_epd['id']);
                        } else {
                            $data_insert = [
                                'revision' => 0,
                                'product_code' => $detail_product['code'],
                                'product_name' => $detail_product['product_name'],
                                'cost_centre' => $detail_product['cost_centre'],
                                'id_cost_centre' => $detail_product['id_cost_centre'],
                                'product_line' => $detail_product['product_line'],
                                'dest' => $detail_product['destination'],
                                'batch' => 0,
                                'posting_code' => 'EPD',
                                // 'P_'.sprintf('%02d', $month[$k]) => $value[$k],
                                'P_' . sprintf('%02d', $month[$k]) => 1,
                            ];
    
                            insert_data($table, $data_insert);
                        }
                    }
                }

            }
            // }
        }

        // if ($is_production) {
        //     delete_data('tbl_scm_submit', ['code_submit' => 'PROD', 'tahun' => $tahun]);

        //     insert_data('tbl_scm_submit', [
        //         'tahun' => $tahun,
        //         'code_submit' => 'PROD',
        //         'is_submit' => 1,
        //         'is_active' => 1
        //     ]);
        // }
    }

    private function save_xproduction_planning(){

        $tahun = post('tahun');
        $table = 'tbl_production_planning_' . $tahun;

        $value = $this->input->post('xproduction_value') ?? [];
        $product = $this->input->post('xproduction_product') ?? [];
        $month = $this->input->post('xproduction_month') ?? [];

        foreach ($value as $k => $v) {
            $detail_product = get_data('tbl_fact_product fp', [
                'select' => 'fp.*',
                'where' => [
                    'fp.code' => $product[$k]
                ],
                'join' => [
                    'tbl_beginning_stock bs ON bs.budget_product_code = fp.code and bs.tahun = ' . $tahun . ' type left'
                ]
            ])->row_array();
            if ($detail_product) {
                $cek_prd = get_data($table, [
                    'where' => [
                        'posting_code' => 'EPR',
                        'product_code' => $detail_product['code'],
                    ]
                ])->row_array();

                if ($cek_prd) {
                    $data_update = [
                        'P_' . sprintf('%02d', $month[$k]) => $value[$k],
                    ];
                    update_data($table, $data_update, 'id', $cek_prd['id']);
                } else {
                    $data_insert = [
                        'revision' => 0,
                        'product_code' => $detail_product['code'],
                        'product_name' => $detail_product['product_name'],
                        'cost_centre' => $detail_product['cost_centre'],
                        'id_cost_centre' => $detail_product['id_cost_centre'],
                        'product_line' => $detail_product['product_line'],
                        'dest' => $detail_product['destination'],
                        'batch' => 0,
                        'posting_code' => 'EPR',
                        'P_' . sprintf('%02d', $month[$k]) => $value[$k]
                    ];

                    insert_data($table, $data_insert);
                }
            }
        }
    }

    function update_cost_center($tahun = "")
    {
        $table = 'tbl_budget_production';
        $table_prod = 'tbl_production_planning_' . $tahun;

        $cek = get_data($table_prod . ' a', [
            'select' => 'a.product_code, c.kode as cost_centre, c.id as id_cost_centre',
            'join' => [
                'tbl_fact_product b on a.product_code = b.code type LEFT',
                'tbl_fact_cost_centre c on b.id_cost_centre = c.id type LEFT',
            ],
            'where' => [
                'b.is_active' => 1,
                // 'a.product_code' => 'CIGSOL21DM'
            ],
        ])->result();

        // debug($cek);die;

        foreach ($cek as $c) {
            update_data(
                $table_prod,
                [
                    'cost_Centre' => $c->cost_centre,
                    'id_cost_centre' => $c->id_cost_centre
                ],
                ['product_code' => $c->product_code]
            );
        }

        $cek2 = get_data('tbl_beginning_stock' . ' a', [
            'select' => 'a.budget_product_code, b.cost_centre, c.id as id_cost_centre',
            'join' => [
                'tbl_fact_product b on a.budget_product_code = b.code type LEFT',
                'tbl_fact_cost_centre c on b.cost_centre = c.kode type LEFT',
            ],
            'where' => [
                'b.is_active' => 1,
                // 'a.product_code' => 'CINFEKMNDM'
            ],
        ])->result();

        foreach ($cek2 as $c2) {
            update_data(
                'tbl_beginning_stock',
                [
                    'id_cost_centre' => $c2->id_cost_centre
                ],
                [
                    'budget_product_code' => $c2->budget_product_code,
                    'tahun' => $tahun
                ]
            );
        }

        echo 'success';
        die;
    }


    Function save_volume_production()
    {
        $id_save_volume = post('id_save_volume');
        $tahun_volume = post('tahun_volume');
        $factory_volume = post('factory_volume');

           
        if(!$id_save_volume || !$tahun_volume) {
            render(['status' => 'failed', 'message' => 'Parameter tidak lengkap'], 'json');
            return;
        }

        $table = 'tbl_budget_production_dev2';
        $table_prod = 'tbl_production_planning_' . $tahun_volume;

        // Get all cost centres to process
        $arr = [
            'select' => 'a.cost_centre as kode, b.id, b.cost_centre, c.kapasitas,
                        WD_01,WD_02,WD_03,WD_04,WD_05,WD_06,WD_07,WD_08,WD_09,WD_10,WD_11,WD_12',
            'join' => [
                'tbl_fact_cost_centre b on a.cost_centre = b.kode type LEFT',
                'tbl_kapasitas_produksi c on a.cost_centre = c.cost_centre type LEFT'
            ],
            'where' => [
                'a.is_active' => 1,
                'a.id_cost_centre !=' => 0,
                'b.cost_centre !=' => ''
            ],
            'group_by' => 'b.cost_centre',
            'sort_by' => 'b.id',
        ];

        if ($factory_volume && $factory_volume != 'ALL') {
            $arr['where']['a.cost_centre'] = $factory_volume;
        }

        $cost_centres = get_data('tbl_fact_product a', $arr)->result();

        foreach($cost_centres as $cost_centre) {
            // Get products for this cost centre with batch_size from tbl_beginning_stock
            $products = get_data('tbl_fact_product a', [
                'select' => 'a.*, b.batch_size',
                'join' => [
                    'tbl_beginning_stock b on a.code = b.budget_product_code and b.tahun = "' . $tahun_volume . '"'
                ],
                'where' => [
                    'a.is_active' => 1,
                    'a.id_cost_centre' => $cost_centre->id,
                    'b.is_active' => 1
                ],
                'sort_by' => 'a.id_cost_centre'
            ])->result();

            foreach($products as $product) {
                // Check if record already exists in tbl_budget_production
                $existing = get_data($table, [
                    'where' => [
                        'tahun' => $tahun_volume,
                        'budget_product_code' => $product->code,
                        'id_cost_centre' => $cost_centre->id
                    ]
                ])->row();

                // Calculate production values based on the view logic
                $production_data = [];
                $total_budget = 0;
                
                for ($i = 1; $i <= 12; $i++) {
                    $field_month = 'B_' . sprintf('%02d', $i);
                    $field_prod = 'P_' . sprintf('%02d', $i);
                    
                    // Get X Produksi value from production planning table
                    $xprod_data = get_data($table_prod, [
                        'where' => [
                            'product_code' => $product->code,
                            'posting_code' => 'EPR',
                            'id_cost_centre' => $cost_centre->id
                        ]
                    ])->row();
                    
                    // Calculate production value (X Produksi * batch size)
                    $xproduksi_value = 0;
                    if($xprod_data) {
                        $xproduksi_value = isset($xprod_data->$field_prod) ? $xprod_data->$field_prod : 0;
                    }
                    
                    // Production calculation: if X Produksi > 0, use X Produksi * batch_size, otherwise 0
                    $prod_value = 0;
                    if($xproduksi_value > 0) {
                        $batch_size = isset($product->batch_size) ? $product->batch_size : 0;
                        $prod_value = $xproduksi_value * $batch_size;
                    }
                    
                    $production_data[$field_month] = $prod_value;
                    $total_budget += $prod_value;
                }

                // Prepare data for insert/update
                $data_to_save = [
                    'tahun' => $tahun_volume,
                    'id_cost_centre' => $cost_centre->id,
                    'divisi' => $product->divisi,
                    'product_line' => $product->product_line,
                    'id_budget_product' => $product->id,
                    'budget_product_code' => $product->code,
                    'budget_product_name' => $product->product_name,
                    'category' => $product->sub_product,
                    'total_budget' => $total_budget
                ];

                // Add monthly production data
                $data_to_save = array_merge($data_to_save, $production_data);

                if($existing) {
                    // Update existing record
                    update_data($table, $data_to_save, 'id', $existing->id);
                } else {
                    // Insert new record
                    insert_data($table, $data_to_save); 
                    //
                }

                // // Update related tables
                // update_data('tbl_fact_allocation_qc', 
                //     ['product_qty' => $total_budget], 
                //     ['tahun' => $tahun_volume, 'product_code' => $product->code]
                // );
                
                // update_data('tbl_fact_product_ovh', 
                //     ['qty_production' => $total_budget], 
                //     ['tahun' => $tahun_volume, 'product_code' => $product->code]
                // );
            }
        }

        render(['status' => 'success', 'message' => 'Data volume produksi berhasil disimpan'], 'json');
    }
}
