<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');

class Manex_budget extends BE_Controller {
    var $controller = 'manex_budget';
    function __construct() {
        parent::__construct();
    }
    
    function index() {

        $data['tahun'] = get_data('tbl_fact_tahun_budget', 'is_active',1)->result();   
        $data['cc'] = get_data('tbl_fact_cost_centre', 'is_active',1)->result(); 
        $access         = get_access($this->controller);
        $data['access_additional']  = $access['access_additional'];
        render($data);
    }
    
    function sortable() {
        render();
    }

    function data($tahun="",$cost_centre="",$status="",$tipe = 'table') {

        $where = [];
        $where1 = [];

        if($cost_centre) $where['a.cost_centre'] = $cost_centre;
        

        if(!empty($tahun) && !empty($cost_centre)) {

        $table = 'tbl_fact_lstbudget_' . $tahun;

        // if($status=="0"){
        //     $table = 'tbl_fact_lstbudget_' . $tahun ;
        // }else{
        //     $table = 'tbl_fact_lstbudget_allocation_' . $tahun ;
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
        }

        $manex = get_data('tbl_fact_manex_account',[
            'where' => [
                'is_active' => 1,
                // 'account_code' => '731'
            ],
            
            ])->result();


        if(table_exists($table)) {
            $dataFilter = [];
            $data['total_budget'] = [];
            foreach($manex as $m) {

                $dataFilter = get_data(' tbl_fact_filter_account',[
                    'select' => 'account_manex as acc_manex, account_code,tail_subaccount',
                    'where' => [
                        'is_active' => 1,
                        'account_manex' => $m->account_code
                    ]
                ])->result_array();

                // $dataFilter = [
                //     [
                //         'acc_manex' => '759',
                //         'account_code' => '731111',
                //         'tail_sub_account' => '02'
                //     ],
                //     [
                //         'acc_manex' => '759',
                //         'account_code' => '731112',
                //         'tail_subaccount' => '02'
                //     ],
                // ];

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
                    'select' => 'sum(B_01) as B_01,sum(B_02) as B_02,sum(B_03) as B_03,sum(B_04) as B_04,sum(B_05) as B_05,sum(B_06) as B_06,
                                sum(B_07) as B_07,sum(B_08) as B_08,sum(B_09) as B_09,sum(B_10) as B_10,sum(B_11) as B_11,sum(B_12) as B_12, sum(total_budget) as total_budget',
                        'where' => [
                            '__m' => '(account_code IN ('.$realAccountNumber.') '.(!empty($customWhere)  ? ' OR ' .$customWhere : '').')',
                            // 'account_code' => json_decode($m->account_member),

                        ]
                    ];

                if($status == 0 ) {
                    $arr['where']['id_ccallocation'] = 0; 
                }

                if($cost_centre && $cost_centre != 'ALL') {
                    $arr['where']['cost_centre'] = $cost_centre;
                }

                $sum = get_data($table . ' a',$arr)->row();


                $data['total_budget'][$m->account_code] =
                [
                    'B_01' => $sum->B_01,
                    'B_02' => $sum->B_02,
                    'B_03' => $sum->B_03,
                    'B_04' => $sum->B_04,
                    'B_05' => $sum->B_05,
                    'B_06' => $sum->B_06,
                    'B_07' => $sum->B_07,
                    'B_08' => $sum->B_08,
                    'B_09' => $sum->B_09,
                    'B_10' => $sum->B_10,
                    'B_11' => $sum->B_11,
                    'B_12' => $sum->B_12,
                    'total' => $sum->total_budget
                ];
            }
        }

        // debug($data['total_budget']);die;

 
        $response	= array(
            'table'		=> $this->load->view('reporting/manex_budget/table',$data,true),
        );
	   
	    render($response,'json');
	}
}

