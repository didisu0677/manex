<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');

class Resume_costing_actual extends BE_Controller {
    var $controller = 'resume_costing';
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

        $data['cc'] = get_data('tbl_fact_cost_centre', 'is_active',1)->result(); 
        $data['production'] = get_data('tbl_fact_cost_centre', [
            'where' => [
                'is_active'=> 1,
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

    function data($tahun="",$bulan="",$status ="", $tipe = 'table') {

        // Field untuk mengambil data actual sesuai bulan yang dipilih
        $tahun = user('tahun_budget') ;
        $field_actual = 'EST_' . sprintf('%02d', $bulan);
        
        $table = 'tbl_fact_lstbudget_' . $tahun ;
        $list_ccallocation = [];
        if($status=="1"){
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
        }


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
                        'select' => 'a.cost_centre, COALESCE(sum('.$field_actual.'), 0) as total_budget',
                        'where' => [
                            '__m' => '(account_code IN ('.$realAccountNumber.') '.(!empty($customWhere)  ? ' OR ' .$customWhere : '').')',

                        ],
                        'group_by' => 'cost_centre'
                    ];

                    if($status == 0) $arr['where']['a.id_ccallocation'] = 0; 

                    // if($status == 1) $arr['where']['a.cost_centre not'] = $list_ccallocation;
                    
                    $sum = get_data($table . ' a',$arr)->result();
            
                    // Initialize with 0 for this cost centre and manex account
                    $data['total_budget'][$m->account_code][$p->kode] = 0;
                    
                    // Update with actual data if exists
                    foreach($sum as $s) {
                        if($s->cost_centre == $p->kode) {
                            $data['total_budget'][$m->account_code][$p->kode] = $s->total_budget ?? 0;
                        }
                    }
                }
            }
        }

        // debug($data['total_budget']);die;

        delete_data('tbl_fact_manex_allocation_actual', ['tahun'=>$tahun-1, 'bulan' => $bulan, 'cost_centre' => "3100"]);
        //simpan report ke database
        foreach($data['total_budget'] as $d => $v) {
            foreach($v as $vc => $t1) {
                if($vc == "3100"){
                    $data_insert = [
                        'tahun' => $tahun-1,
                        'bulan' => $bulan,
                        'manex_account' => $d,
                        'cost_centre' => $vc,
                        'total' => $t1
                    ];
                    insert_data('tbl_fact_manex_allocation_actual',$data_insert);
                }
            }
        }
 
        // Add bulan data to view
        $data['bulan'] = $bulan;
        $data['field_actual'] = $field_actual;
        
        $response	= array(
            'table'		=> $this->load->view('reporting/resume_costing_actual/table',$data,true),
            // 'table2'		=> $this->load->view('reporting/resume_costing/table2',$data,true),
        );
	   
	    render($response,'json');
	}
}

