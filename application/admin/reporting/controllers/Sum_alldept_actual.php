<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');

class Sum_alldept_actual extends BE_Controller {
    var $controller = 'sum_alldept';
    function __construct() {
        parent::__construct();
    }
    
    function index() {

        $data['tahun'] = get_data('tbl_fact_tahun_budget', [
            'where' => [
                'is_active'=>1,
                'tahun' => user('tahun_budget') - 1
            ]
        ])->result();   
        // $data['cc'] = get_data('tbl_fact_cost_centre', 'is_active',1)->result(); 


        $data['production'] = get_data('tbl_fact_cost_centre', [
            'where' => [
                'is_active'=> 1,
                'kode !='              => '0000'
            ]
        ])->result(); 

        $access         = get_access($this->controller);
        $data['access_additional']  = $access['access_additional'];
        render($data);
    }
    
    function sortable() {
        render();
    }

    function data($tahun="", $bulan="",$status="",$tipe = 'table') {
		ini_set('memory_limit', '-1');
        
        $tahun = user('tahun_budget') ;
        $field0 = 'EST_' . sprintf('%02d', $bulan);
        $field1 = 'B_' . sprintf('%02d', $bulan);
        

        $arr = [
            'select' => '*',
            'where'	=> [
                'is_active'			=> 1,
                'kode !='              => '0000'
            ],
        ];


        $data['production'] = get_data('tbl_fact_cost_centre', $arr)->result(); 

        $status = 0;
        $table = 'tbl_fact_lstbudget_' . $tahun ;

        $arr = [
            'select' => 'a.id,a.account_code,a.account_name,a.urutan',
            'where'=> [
                'a.parent_id'=>0,
            ],
            'group_by' => 'a.id,a.account_code,a.account_name,a.urutan',
            'sort_by'=>'a.urutan',
        ];

        $data['mst_account'][0] = get_data('tbl_fact_template_report a',$arr)->result();
        
        // OPTIMIZED: Fetch all account levels in ONE query instead of nested loops
        $all_accounts = get_data('tbl_fact_template_report', [
            'select' => 'id, parent_id, account_code, account_name, urutan',
            'sort_by' => 'urutan'
        ])->result();
        
        // Index accounts by parent_id for O(1) lookup
        $accounts_by_parent = [];
        foreach($all_accounts as $acc) {
            if(!isset($accounts_by_parent[$acc->parent_id])) {
                $accounts_by_parent[$acc->parent_id] = [];
            }
            $accounts_by_parent[$acc->parent_id][] = $acc;
        }
        
        // Build hierarchy from indexed data (no more database queries)
        foreach($data['mst_account'][0] as $m0) {
            $data['mst_account'][$m0->id] = isset($accounts_by_parent[$m0->id]) ? $accounts_by_parent[$m0->id] : [];
            
            foreach($data['mst_account'][$m0->id] as $m1) {
                $data['mst_account'][$m1->id] = isset($accounts_by_parent[$m1->id]) ? $accounts_by_parent[$m1->id] : [];
                
                foreach($data['mst_account'][$m1->id] as $m2) {
                    $data['mst_account'][$m2->id] = isset($accounts_by_parent[$m2->id]) ? $accounts_by_parent[$m2->id] : [];
                }
            }
        }


        $sum_budget = get_data($table, [
            'select' => 'cost_centre,account_code,sum('.$field0.') as total_budget, sum('.$field1.') as total_le',
            'where'  => [
                'tahun' => $tahun,
                'id_ccallocation' => 0
            ],
            'group_by' => 'account_code,cost_centre',
        ])->result();

        $data['tbudget'] = $sum_budget;
        
        // OPTIMIZED: Index budget data by cost_centre+account_code for O(1) lookup
        $data['budget_indexed'] = [];
        foreach($sum_budget as $budget) {
            $key = $budget->cost_centre . '_' . $budget->account_code;
            $data['budget_indexed'][$key] = $budget;
        }



        $arrl = [
            'select' => '*',
            'where' => [
                'is_active' => 1,
                'sum_of !=' => "",
                // 'account_code' => '721111'
            ],
            'sort_by' => 'urutan',
        ];


        $total_labour = get_data('tbl_fact_template_report',$arrl)->result();

        $data['labour'] = [];
        $data['id_labour'] = [];
        foreach($total_labour as $m) {

            $arr = [
                'select' => 'cost_centre, sum('.$field0.') as total_budget, sum('.$field1.') as total_le',
                'where' => [
                    'a.account_code' => json_decode($m->sum_of),
                    'a.id_ccallocation' => $status
                ],
                'group_by' => 'cost_centre',
            ];

            $sum = get_data($table . ' a',$arr)->result();

            // Initialize all cost centres with 0 values first
            foreach($data['production'] as $prod) {
                $data['total_labour'][$m->id][$prod->kode] = [
                    'total_budget' => 0,
                    'total_le' => 0,
                ];
            }

            // Then update with actual data if exists
            foreach($sum as $s) {
                $data['total_labour'][$m->id][$s->cost_centre] =
                [
                    'total_budget' => $s->total_budget,
                    'total_le' => $s->total_le,
                ];
            }

            $data['id_labour'][] = $m->id;
        }

        // debug($data['total_labour']) ;die;

        // foreach($data['total_labour'] as $t=>$v) {
        //     debug($t);
        //     debug($v);die;
        // }

        $response	= array(
            'table2'		=> $this->load->view('reporting/sum_alldept_actual/table2',$data,true),
        );
	   
	    render($response,'json');
	}


    function isi_listbudget($tahun="",$cc=""){
        isi_budget_acaount($tahun,$cc);
    }
}

