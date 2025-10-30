<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');

class R_allocation_actual extends BE_Controller {
    var $controller = 'r_allocation';
    function __construct() {
        parent::__construct();
    }
    
    function index() {

        $data['tahun'] = get_data('tbl_fact_tahun_budget', [
            'where' => [
                'is_active'=>1,
                'tahun' => user('tahun_budget') - 1
            ],
            ])->result();   
        $data['cc'] = get_data('tbl_fact_cost_centre', 'is_active',1)->result(); 

        $list_ccallocation = [];
        $dtalocation = get_data('tbl_fact_ccallocation a',[
            'select' => 'a.source_allocation',
            'where' => [
                'a.is_active' => 1,
            ],
            ])->result();

        foreach($dtalocation as $d) {
            $soure_cc = json_decode($d->source_allocation);
            foreach($soure_cc as $c) {
                if(!in_array($c, $list_ccallocation)) $list_ccallocation[] = $c;
            }
        }

        $data['production'] = get_data('tbl_fact_cost_centre', [
            'where' => [
                'is_active'=> 1,
                'kode not' => $list_ccallocation,
                '__m' => 'kode != "3100" AND kode != "0000"'
                // 'id_group_department' => 2
            ]
        ])->result(); 
        $access         = get_access($this->controller);
        $data['access_additional']  = $access['access_additional'];
        render($data);
    }
    
    function sortable() {
        render();
    }

    function data($tahun="", $bulan="",$tipe = 'table') {

        $total_type = get('type');
        $field = 'B_' . sprintf('%02d', $bulan);
        $table = 'act_tbl_fact_lstbudget_' . $tahun ;
        $list_ccallocation = [];
        // if($status=="1"){
            $dtalocation = get_data('tbl_fact_ccallocation a',[
                'select' => 'a.source_allocation',
                'where' => [
                    'a.is_active' => 1,
                ],
                ])->result();

            foreach($dtalocation as $d) {
                $soure_cc = json_decode($d->source_allocation);
                foreach($soure_cc as $c) {
                    if(!in_array($c, $list_ccallocation)) $list_ccallocation[] = $c;
                }
            }
        // }


        $data['mst_account'][0] = get_data('tbl_fact_manex_account a',[
            'select' => 'distinct grup',
            'where'=> [
                'is_active'=>1
            ],
            'sort_by'=>'urutan',
            ])->result();

        foreach($data['mst_account'][0] as $m0) {
            $data['mst_account'][$m0->grup] = get_data('tbl_fact_manex_account a',[
                'select' => 'a.*',
                'where'=>[
                    'a.grup'=>$m0->grup
                ],
                'sort_by'=>'a.urutan'
                ])->result();
            }
        

            $manex = get_data('tbl_fact_manex_account',[
                'where' => [
                    'is_active' => 1,
                    // 'account_code' => '731'
                ],
                
                ])->result();



        if(table_exists($table)) {
            $data['total_budget'] = [];

 
            $arr = [
                'select' => '*',
                'where'	=> [
                    'is_active'			=> 1,
                ],
            ];

            $arr['where']['kode not'] = $list_ccallocation;
            $arr['where']['__m'] = '(kode != "0000" AND kode != "3100")';

            //  if($status == 1) $arr['where']['kode not'] = $list_ccallocation;

            $data['production'] = get_data('tbl_fact_cost_centre', $arr)->result(); 
           
            foreach($data['production'] as $p) {
                foreach($manex as $m) {

                    $dataFilter = get_data(' tbl_fact_filter_account',[
                        'select' => 'account_manex as acc_manex, account_code,tail_subaccount',
                        'where' => [
                            'is_active' => 1,
                            'account_manex' => $m->account_code
                        ]
                    ])->result_array();

                    
                    $customWhere1 = array();
                    if(count($dataFilter)) {
                        foreach($dataFilter as $dk => $dv){
                            if($dv['acc_manex'] == $m->account_code){
                                $tailSubAccount = $dv['tail_subaccount'];
                                $customWhere1[] = '(account_code = "'.$dv['account_code'].'" AND sub_account LIKE "%'.$tailSubAccount.'")';
                            }
                        }
                    }

                    $customWhere = '';
                    foreach($customWhere1 as $c) {
                        if($customWhere == '') {
                            $customWhere = $c;
                        }else{
                            $customWhere = $customWhere . ' OR ' . $c;
                        }
                    }
    
    
                    $accountNumber = json_decode($m->account_member);
    
                    // debug($accountNumber);die;
                    $realAccountNumber = array();
                    foreach($accountNumber as $k => $v){
                        $isExistInDataFilter = false;
                        $tailSubAccount = '';
    
                        foreach($dataFilter as $dk => $dv){
                            if($dv['acc_manex'] == $m->account_code){
                                if($v == $dv['account_code']) $isExistInDataFilter = true;
                            }
                        }
    
                        if(!$isExistInDataFilter) $realAccountNumber[] = $v;
                    }
    
    
                    $realAccountNumber = implode(',', array_map(function($value) {
                        return "'" . $value . "'";
                    }, $realAccountNumber));

                    $arr = [
                        'select' => 'a.cost_centre, sum('.$field.') as total_budget',
                        //  sum(total_budget_idle) as total_budget_idle, sum(budget_after_idle) as budget_after_idle',
                        'where' => [
                            '__m' => '(account_code IN ('.$realAccountNumber.') '.(!empty($customWhere)  ? ' OR ' .$customWhere : '').')',

                        ],
                        'group_by' => 'cost_centre'
                    ];

                    // if($status == 0) $arr['where']['a.id_ccallocation'] = 0; 

                    // if($status == 1) $arr['where']['a.cost_centre not'] = $list_ccallocation;
                    
                    $sum = get_data($table . ' a',$arr)->result();
                    foreach($sum as $s) {
                        if($s->cost_centre == $p->kode) {
                            $data['total_budget'][$m->account_code][$p->kode] = $s->total_budget;
                            $data['total_budget_idle'][$m->account_code][$p->kode] = 0;
                            $data['budget_after_idle'][$m->account_code][$p->kode] = 0;
                        }
                    }
                }
            }
        }

        // debug($data['total_budget_idle']);die;
        delete_data('tbl_fact_manex_allocation_actual', ['tahun'=>$tahun,'bulan' => $bulan,'cost_centre !=' => '3100']);
        //simpan report ke database
        foreach($data['total_budget'] as $d => $v) {
            foreach($v as $vc => $t1) {
   
                $data_insert = [
                    'tahun' => $tahun,
                    'bulan' => $bulan,
                    'manex_account' => $d,
                    'cost_centre' => $vc,
                    'total' => $t1
                ];
                insert_data('tbl_fact_manex_allocation_actual',$data_insert);
            }
        }


        $lst = get_data('tbl_fact_manex_allocation_actual a',[
			'select' => 'a.*,b.kode as cost_centre,c.prsn_allocation',
			'join'   => ['tbl_fact_cost_centre b on a.cost_centre = b.kode type LEFT',
                       'tbl_idle_allocation_actual c on b.id = c.id_cost_centre and a.bulan = c.bulan and b.is_active = 1 type left',
                        ],
            'where'  => [
                        'a.tahun' => $tahun,
                        'a.bulan' => $bulan,
                        ]
  		    ])->result();

        foreach($lst as $l) {
            if(in_array($l->manex_account,['7212','735','736','738','759'])) {
                $total_idle = ($l->total * ($l->prsn_allocation / 100));
                $after_idle = $l->total - $total_idle;
            } else {
                $total_idle = 0;
                $after_idle = $l->total;
            }

                update_data('tbl_fact_manex_allocation_actual',
                    ['total_idle' => $total_idle, 'after_idle' => $after_idle ],
                    ['tahun' => $l->tahun,'bulan' => $l->bulan,'manex_account'=>$l->manex_account,'cost_centre' => $l->cost_centre],
                );

            $data['total_budget_idle'][$l->manex_account][$l->cost_centre] = 0;//$total_idle;
            $data['budget_after_idle'][$l->manex_account][$l->cost_centre] = 0;//$after_idle;

		}


        // foreach($data['total_budget_idle'] as $d1 => $v1) {
        //     foreach($v1 as $vc1 => $t11) {


        //         $data_insert1 = [
        //             'total_idle' => $t11
        //         ];
        //         update_data('tbl_fact_manex_allocation',$data_insert1,['tahun'=>$tahun,'manex_account'=>$d1,'cost_centre'=>$vc1]);
        //     }
        // }

        // foreach($data['budget_after_idle'] as $d2 => $v2) {
        //     foreach($v2 as $vc2 => $t12) {

        //         $data_insert2 = [
        //             'after_idle' => $t12
        //         ];
        //         update_data('tbl_fact_manex_allocation',$data_insert2,['tahun'=>$tahun,'manex_account'=>$d2,'cost_centre'=>$vc2]);
        //     }
        // }

        $data['total_type'] = $total_type;
        $response	= array(
            'table'		=> $this->load->view('reporting/r_allocation_actual/table',$data,true),
            // 'table2'		=> $this->load->view('reporting/r_allocation_actual/table2',$data,true),
        );
	   
	    render($response,'json');
	}
}

