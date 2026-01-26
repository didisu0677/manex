<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');

class Budget_by_deptnew extends BE_Controller {
    var $controller = 'budget_by_deptnew';
    function __construct() {
        parent::__construct();
    }
    
    function index() {

        $data['tahun'] = get_data('tbl_fact_tahun_budget', 'is_active',1)->result();   
        // $data['cc'] = get_data('tbl_fact_cost_centre', 'is_active',1)->result(); 

        $arr = [
            'select' => 'a.cost_centre as kode, b.cost_centre',
                'join' => 'tbl_fact_cost_centre b on a.cost_centre = b.kode',
                'where' => [
                    'b.is_active'=>1,
                    'a.tahun' => user('tahun_budget'),
                ],
                'group_by' => 'a.cost_centre',
        ];

        if(in_array(user('id_group'), [BUDGET_PIC_FACTORY,SCM,OPR,QC,IT])) {
            $xid = "%".user('id')."%";
            $arr['where']['__m'] = 'user_id like"'.$xid.'"' ;
        }


        $data['cc'] = get_data('tbl_fact_pic_budget a',$arr)->result(); 


        $access         = get_access($this->controller);
        $data['access'] = $access ;
        $data['access_additional']  = $access['access_additional'];
        render($data);
    }
    
    function sortable() {
        render();
    }

    function data($tahun="",$cost_centre="",$status="",$tipe = 'table') {
		ini_set('memory_limit', '-1');

        $status = 0;
        $table = 'tbl_fact_lstbudget_' . $tahun ;
        $costCentreUser = getCostCenterByUser(user('id'), $tahun);

        $group_cc = false ;
        $cc_group = get_data('tbl_fact_cost_centre','member_of',$cost_centre)->result();
        $cc_member = [];
        if(count($cc_group)){
            $group_cc = true ;
            foreach($cc_group as $c) $cc_member[] = $c->kode;
        }

        // akses cost centre //
            $arr_cc = [
                'select' => 'a.cost_centre as kode, b.cost_centre',
                    'join' => 'tbl_fact_cost_centre b on a.cost_centre = b.kode',
                    'where' => [
                        'b.is_active'=>1,
                        'a.tahun' => user('tahun_budget'),
                    ],
                    'group_by' => 'a.cost_centre',
            ];

            if(in_array(user('id_group'), [BUDGET_PIC_FACTORY,SCM,OPR,QC])) {
                $xid = "%".user('id')."%";
                $arr_cc['where']['__m'] = 'user_id like "'.$xid.'"' ;
            }

            $cc_akses = get_data('tbl_fact_pic_budget a',$arr_cc)->result(); 

            $cca = [];
            foreach($cc_akses as $c) {
                $cca[] = $c->kode;
            }
        //

                //
        ///////////////////////// akses akun user ////////////////////////////////
        $arr_ccXX = [
            'select' => 'a.cost_centre as kode, b.cost_centre',
                'join' => 'tbl_fact_cost_centre b on a.cost_centre = b.kode',
                'where' => [
                    'b.is_active'=>1,
                    'a.tahun' => user('tahun_budget'),
                ],
                'group_by' => 'a.cost_centre',
        ];

        
        if(!in_array(user('id_group'), [ADMIN_UTAMA,ADMIN])) {
            $xidU = "%".user('id')."%";
            $arr_ccXX['where']['__mz'] = 'user_id like "'.$xidU.'"' ;
        }

        $cc_USER = get_data('tbl_fact_pic_budget a',$arr_ccXX)->result(); 

        $ccU = [];
        foreach($cc_USER as $cu) {
            $ccU[] = $cu->kode;
        }

        ////////////////////////////////////////////////

        $acc_akses = get_data('tbl_fact_account_cc a',[
            'select' => 'b.id as id_account, a.account_code',
            'join' => 'tbl_fact_account b on a.account_code = b.account_code',
            'where' => [
                'a.cost_centre' => $ccU,
            ],
            'group_by' => 'account_code'
        ])->result();

        $right_acc = [];
        foreach($acc_akses as $a) $right_acc[] = $a->account_code;

        $data['user_akses_account'] = $right_acc;

        // debug($acc_akses);die;

        $arr = [
            'select' => 'a.id,a.account_code,a.account_name,b.cost_centre, b.cost_centre,a.urutan, 
                         sum(b.EST_01) as B_01, sum(b.B_02) as B_02, sum(b.B_03) as B_03, 
                         sum(b.B_04) as B_04, sum(b.B_05) as B_05, sum(b.B_06) as B_06, 
                         sum(b.B_07) as B_07, sum(b.B_08) as B_08, sum(b.B_09) as B_09,
                         sum(b.B_10) as B_10, sum(b.B_11) as B_11, sum(b.B_12) as B_12, 

                         sum(b.EST_01) as EST_01, sum(b.EST_02) as EST_02, sum(b.EST_03) as EST_03, 
                         sum(b.EST_04) as EST_04, sum(b.EST_05) as EST_05, sum(b.EST_06) as EST_06, 
                         sum(b.EST_07) as EST_07, sum(b.EST_08) as EST_08, sum(b.EST_09) as EST_09,
                         sum(b.EST_10) as EST_10, sum(b.EST_11) as EST_11, sum(b.EST_12) as EST_12, 

                         sum(b.total_budget) as total_budget, sum(total_le) as total_le, ',
            // 'join' => $table . ' b on a.account_code = b.account_code and b.cost_centre ="'.$cost_centre.'" and b.id_ccallocation = "'.$status.'" type LEFT',
            'where'=> [
                'a.parent_id'=>0,
            ],
            'group_by' => 'a.id,a.account_code,a.account_name,b.cost_centre,a.urutan',
            'sort_by'=>'a.urutan',
        ];

        if($cost_centre == 'ALL') {
            $arr['group_by'] = 'a.id,a.account_code,a.account_name,a.urutan'; 
            $arr['join'] =   $table . ' b on a.account_code = b.account_code and b.id_ccallocation = "'.$status.'" type LEFT';            
        }elseif($group_cc == true){
            $arr['group_by'] = 'a.id,a.account_code,a.account_name,a.urutan';
            $arr['join'] =   $table . ' b on a.account_code = b.account_code and b.cost_centre IN ('.implode(',', $cc_member).') and b.id_ccallocation = "'.$status.'" type LEFT';            
        }else{
            $arr['group_by'] = 'a.id,a.account_code,a.account_name,b.cost_centre,a.urutan';
            $arr['join'] =   $table . ' b on a.account_code = b.account_code and b.cost_centre ="'.$cost_centre.'" and b.id_ccallocation = "'.$status.'" type LEFT';            
        }

        if(in_array(user('id_group'), [BUDGET_PIC_FACTORY,SCM,OPR,QC])) {
            if($costCentreUser) $arr['where']['__m2'] = '(b.cost_centre IN ('.implode(',', $costCentreUser).') OR b.cost_centre IS NULL)';
             $arr['where']['__m'] = 'substr(a.account_code,1,2) != "72"' ;
        }elseif(user('id_group') == HRD && $cost_centre == '1200'){
            $arr['where']['__m'] = 'substr(a.account_code,1,3) != "721"' ;
        }elseif(user('id_group') == HRD && $cost_centre == 'ALL'){
            $arr['where_or_field'] = '(a.parent_id ="0" and substr(a.account_code,1,3) = "721" and b.cost_centre != "1200")' ;
        }

        if($group_cc == true ) {
            $arr['where']['__m31'] = '(b.cost_centre IN ('.implode(',', $cc_member).') OR b.cost_centre IS NULL)';
        }

        $data['mst_account'][0] = get_data('tbl_fact_template_report a',$arr)->result();
        $isAllHrd = user('id_group') == HRD && $cost_centre == 'ALL';
        $customSelect = 'sum(b.B_01) as B_01, sum(b.B_02) as B_02, sum(b.B_03) as B_03, 
            sum(b.B_04) as B_04, sum(b.B_05) as B_05, sum(b.B_06) as B_06, 
            sum(b.B_07) as B_07, sum(b.B_08) as B_08, sum(b.B_09) as B_09,
            sum(b.B_10) as B_10, sum(b.B_11) as B_11, sum(b.B_12) as B_12,

            sum(b.EST_01) as EST_01, sum(b.EST_02) as EST_02, sum(b.EST_03) as EST_03, 
            sum(b.EST_04) as EST_04, sum(b.EST_05) as EST_05, sum(b.EST_06) as EST_06, 
            sum(b.EST_07) as EST_07, sum(b.EST_08) as EST_08, sum(b.EST_09) as EST_09,
            sum(b.EST_10) as EST_10, sum(b.EST_11) as EST_11, sum(b.EST_12) as EST_12,
            
            sum(b.total_budget) as total_budget, sum(total_le) as total_le';

        $customSelect2 = 'a.account_code,
        sum(B_01) as B_01,sum(B_02) as B_02,sum(B_03) as B_03,sum(B_04) as B_04,
        sum(B_05) as B_05,sum(B_06) as B_06,sum(B_07) as B_07,sum(B_08) as B_08,
        sum(B_09) as B_09,sum(B_10) as B_10,sum(B_11) as B_11,sum(B_12) as B_12, 
        
        sum(EST_01) as EST_01,sum(EST_02) as EST_02,sum(EST_03) as EST_03,sum(EST_04) as EST_04,
        sum(EST_05) as EST_05,sum(EST_06) as EST_06,sum(EST_07) as EST_07,sum(EST_08) as EST_08,
        sum(EST_09) as EST_09,sum(EST_10) as EST_10,sum(EST_11) as EST_11,sum(EST_12) as EST_12, 
        
        sum(total_budget) as total_budget, sum(total_le) as total_le';

        if($isAllHrd){
            $customSelect = '
            SUM(IF(b.cost_centre = "1200", (IF(substr(a.account_code,1,3) != "721", b.B_01, 0)), b.B_01)) as B_01,
            SUM(IF(b.cost_centre = "1200", (IF(substr(a.account_code,1,3) != "721", b.B_02, 0)), b.B_02)) as B_02,
            SUM(IF(b.cost_centre = "1200", (IF(substr(a.account_code,1,3) != "721", b.B_03, 0)), b.B_03)) as B_03,
            SUM(IF(b.cost_centre = "1200", (IF(substr(a.account_code,1,3) != "721", b.B_04, 0)), b.B_04)) as B_04,
            SUM(IF(b.cost_centre = "1200", (IF(substr(a.account_code,1,3) != "721", b.B_05, 0)), b.B_05)) as B_05,
            SUM(IF(b.cost_centre = "1200", (IF(substr(a.account_code,1,3) != "721", b.B_06, 0)), b.B_06)) as B_06,
            SUM(IF(b.cost_centre = "1200", (IF(substr(a.account_code,1,3) != "721", b.B_07, 0)), b.B_07)) as B_07,
            SUM(IF(b.cost_centre = "1200", (IF(substr(a.account_code,1,3) != "721", b.B_08, 0)), b.B_08)) as B_08,
            SUM(IF(b.cost_centre = "1200", (IF(substr(a.account_code,1,3) != "721", b.B_09, 0)), b.B_09)) as B_09,
            SUM(IF(b.cost_centre = "1200", (IF(substr(a.account_code,1,3) != "721", b.B_10, 0)), b.B_10)) as B_10,
            SUM(IF(b.cost_centre = "1200", (IF(substr(a.account_code,1,3) != "721", b.B_11, 0)), b.B_11)) as B_11,
            SUM(IF(b.cost_centre = "1200", (IF(substr(a.account_code,1,3) != "721", b.B_12, 0)), b.B_12)) as B_12,
            
            SUM(IF(b.cost_centre = "1200", (IF(substr(a.account_code,1,3) != "721", b.EST_01, 0)), b.EST_01)) as EST_01,
            SUM(IF(b.cost_centre = "1200", (IF(substr(a.account_code,1,3) != "721", b.EST_02, 0)), b.EST_02)) as EST_02,
            SUM(IF(b.cost_centre = "1200", (IF(substr(a.account_code,1,3) != "721", b.EST_03, 0)), b.EST_03)) as EST_03,
            SUM(IF(b.cost_centre = "1200", (IF(substr(a.account_code,1,3) != "721", b.EST_04, 0)), b.EST_04)) as EST_04,
            SUM(IF(b.cost_centre = "1200", (IF(substr(a.account_code,1,3) != "721", b.EST_05, 0)), b.EST_05)) as EST_05,
            SUM(IF(b.cost_centre = "1200", (IF(substr(a.account_code,1,3) != "721", b.EST_06, 0)), b.EST_06)) as EST_06,
            SUM(IF(b.cost_centre = "1200", (IF(substr(a.account_code,1,3) != "721", b.EST_07, 0)), b.EST_07)) as EST_07,
            SUM(IF(b.cost_centre = "1200", (IF(substr(a.account_code,1,3) != "721", b.EST_08, 0)), b.EST_08)) as EST_08,
            SUM(IF(b.cost_centre = "1200", (IF(substr(a.account_code,1,3) != "721", b.EST_09, 0)), b.EST_09)) as EST_09,
            SUM(IF(b.cost_centre = "1200", (IF(substr(a.account_code,1,3) != "721", b.EST_10, 0)), b.EST_10)) as EST_10,
            SUM(IF(b.cost_centre = "1200", (IF(substr(a.account_code,1,3) != "721", b.EST_11, 0)), b.EST_11)) as EST_11,
            SUM(IF(b.cost_centre = "1200", (IF(substr(a.account_code,1,3) != "721", b.EST_12, 0)), b.EST_12)) as EST_12,
            
            SUM(IF(b.cost_centre = "1200", (IF(substr(a.account_code,1,3) != "721", b.total_budget, 0)), b.total_budget)) as total_budget, 
            SUM(IF(b.cost_centre = "1200", (IF(substr(a.account_code,1,3) != "721", b.total_le, 0)), b.total_le)) as total_le';

            $customSelect2 = 'a.account_code,
            SUM(IF(a.cost_centre = "1200", (IF(substr(a.account_code,1,3) != "721", a.B_01, 0)), a.B_01)) as B_01,
            SUM(IF(a.cost_centre = "1200", (IF(substr(a.account_code,1,3) != "721", a.B_02, 0)), a.B_02)) as B_02,
            SUM(IF(a.cost_centre = "1200", (IF(substr(a.account_code,1,3) != "721", a.B_03, 0)), a.B_03)) as B_03,
            SUM(IF(a.cost_centre = "1200", (IF(substr(a.account_code,1,3) != "721", a.B_04, 0)), a.B_04)) as B_04,
            SUM(IF(a.cost_centre = "1200", (IF(substr(a.account_code,1,3) != "721", a.B_05, 0)), a.B_05)) as B_05,
            SUM(IF(a.cost_centre = "1200", (IF(substr(a.account_code,1,3) != "721", a.B_06, 0)), a.B_06)) as B_06,
            SUM(IF(a.cost_centre = "1200", (IF(substr(a.account_code,1,3) != "721", a.B_07, 0)), a.B_07)) as B_07,
            SUM(IF(a.cost_centre = "1200", (IF(substr(a.account_code,1,3) != "721", a.B_08, 0)), a.B_08)) as B_08,
            SUM(IF(a.cost_centre = "1200", (IF(substr(a.account_code,1,3) != "721", a.B_09, 0)), a.B_09)) as B_09,
            SUM(IF(a.cost_centre = "1200", (IF(substr(a.account_code,1,3) != "721", a.B_10, 0)), a.B_10)) as B_10,
            SUM(IF(a.cost_centre = "1200", (IF(substr(a.account_code,1,3) != "721", a.B_11, 0)), a.B_11)) as B_11,
            SUM(IF(a.cost_centre = "1200", (IF(substr(a.account_code,1,3) != "721", a.B_12, 0)), a.B_12)) as B_12,
            
            SUM(IF(a.cost_centre = "1200", (IF(substr(a.account_code,1,3) != "721", a.EST_01, 0)), a.EST_01)) as EST_01,
            SUM(IF(a.cost_centre = "1200", (IF(substr(a.account_code,1,3) != "721", a.EST_02, 0)), a.EST_02)) as EST_02,
            SUM(IF(a.cost_centre = "1200", (IF(substr(a.account_code,1,3) != "721", a.EST_03, 0)), a.EST_03)) as EST_03,
            SUM(IF(a.cost_centre = "1200", (IF(substr(a.account_code,1,3) != "721", a.EST_04, 0)), a.EST_04)) as EST_04,
            SUM(IF(a.cost_centre = "1200", (IF(substr(a.account_code,1,3) != "721", a.EST_05, 0)), a.EST_05)) as EST_05,
            SUM(IF(a.cost_centre = "1200", (IF(substr(a.account_code,1,3) != "721", a.EST_06, 0)), a.EST_06)) as EST_06,
            SUM(IF(a.cost_centre = "1200", (IF(substr(a.account_code,1,3) != "721", a.EST_07, 0)), a.EST_07)) as EST_07,
            SUM(IF(a.cost_centre = "1200", (IF(substr(a.account_code,1,3) != "721", a.EST_08, 0)), a.EST_08)) as EST_08,
            SUM(IF(a.cost_centre = "1200", (IF(substr(a.account_code,1,3) != "721", a.EST_09, 0)), a.EST_09)) as EST_09,
            SUM(IF(a.cost_centre = "1200", (IF(substr(a.account_code,1,3) != "721", a.EST_10, 0)), a.EST_10)) as EST_10,
            SUM(IF(a.cost_centre = "1200", (IF(substr(a.account_code,1,3) != "721", a.EST_11, 0)), a.EST_11)) as EST_11,
            SUM(IF(a.cost_centre = "1200", (IF(substr(a.account_code,1,3) != "721", a.EST_12, 0)), a.EST_12)) as EST_12,
            
            SUM(IF(a.cost_centre = "1200", (IF(substr(a.account_code,1,3) != "721", a.total_budget, 0)), a.total_budget)) as total_budget, 
            SUM(IF(a.cost_centre = "1200", (IF(substr(a.account_code,1,3) != "721", a.total_le, 0)), a.total_le)) as total_le';
        }
        foreach($data['mst_account'][0] as $m0) {

            $customWhere = [
                '__m0'=>'(a.parent_id = "'.$m0->id.'")',
            ];
            

            // if($isAllHrd){
            //     $customWhere = array();
            // }

            $arr = [
                'select' => 'a.id,a.account_code,a.account_name, b.cost_centre, a.urutan,  
                                '.$customSelect,
                // 'join' => $table . ' b on a.account_code = b.account_code and b.cost_centre ="'.$cost_centre.'" and b.id_ccallocation = "'.$status.'" type LEFT',    
                // 'where'=>[
                //     'a.parent_id'=>$m0->id
                // ],
                'where' => $customWhere,
                // 'group_by' => 'a.id,a.account_code,a.account_name,b.cost_centre,a.urutan',
                'sort_by'=>'a.urutan'
            ];

            if($cost_centre == 'ALL') {
                $arr['group_by'] = 'a.id,a.account_code,a.account_name,a.urutan'; 
                $arr['join'] =   $table . ' b on a.account_code = b.account_code and b.id_ccallocation = "'.$status.'" type LEFT';            
            }elseif($group_cc == true){
                $arr['group_by'] = 'a.id,a.account_code,a.account_name,a.urutan';
                $arr['join'] =   $table . ' b on a.account_code = b.account_code and b.cost_centre IN ('.implode(',', $cc_member).') and b.id_ccallocation = "'.$status.'" type LEFT';            
            }else{
                $arr['group_by'] = 'a.id,a.account_code,a.account_name,b.cost_centre,a.urutan';
                $arr['join'] =   $table . ' b on a.account_code = b.account_code and b.cost_centre ="'.$cost_centre.'" and b.id_ccallocation = "'.$status.'" type LEFT';            

            }

            if(in_array(user('id_group'), [BUDGET_PIC_FACTORY,SCM,OPR,QC])) {
                if($costCentreUser) $arr['where']['__m2'] = '(b.cost_centre IN ('.implode(',', $costCentreUser).') OR b.cost_centre IS NULL)';
                $arr['where']['__m'] = 'substr(a.account_code,1,2) != "72"' ;
            }elseif(user('id_group') == HRD && $cost_centre == '1200'){
                $arr['where']['__m'] = 'substr(a.account_code,1,3) != "721"' ;
            }elseif($isAllHrd){
                // $arr['where']['__m'] = '(a.parent_id ="'.$m0->id.'" and substr(a.account_code,1,3) = "721")' ;
            }
            
            if($group_cc == true ) {
                $arr['where']['__m42'] = '(b.cost_centre IN ('.implode(',', $cc_member).') OR b.cost_centre IS NULL)';
            }

            $data['mst_account'][$m0->id] = get_data('tbl_fact_template_report a',$arr)->result();
            foreach($data['mst_account'][$m0->id] as $m1) {
                $customWhere = [
                    '__m0'=>'(a.parent_id = "'.$m1->id.'")',
                ];
                // if($isAllHrd){
                //     $customWhere = array();
                // }
                $arr = [
                    'select' => 'a.id,a.account_code,a.account_name, b.cost_centre, a.urutan,  
                                '.$customSelect,
                    // 'join' => $table . ' b on a.account_code = b.account_code and b.cost_centre ="'.$cost_centre.'" and b.id_ccallocation = "'.$status.'" type LEFT',        
                    // 'where'=>[
                    //     '__m0'=>'(a.parent_id = "'.$m1->id.'")',
                    // ],
                    // 'group_by' => 'a.id,a.account_code,a.account_name,b.cost_centre,a.urutan',
                    'where' => $customWhere,
                    'sort_by'=>'a.urutan'
                ];

                if($cost_centre == 'ALL') {
                    $arr['group_by'] = 'a.id,a.account_code,a.account_name,a.urutan'; 
                    $arr['join'] =   $table . ' b on a.account_code = b.account_code and b.id_ccallocation = "'.$status.'" type LEFT';            
                }elseif($group_cc == true){
                    $arr['group_by'] = 'a.id,a.account_code,a.account_name,a.urutan';
                    $arr['join'] =   $table . ' b on a.account_code = b.account_code and b.cost_centre IN ('.implode(',', $cc_member).') and b.id_ccallocation = "'.$status.'" type LEFT';            
                }else{
                    $arr['group_by'] = 'a.id,a.account_code,a.account_name,b.cost_centre,a.urutan';
                    $arr['join'] =   $table . ' b on a.account_code = b.account_code and b.cost_centre ="'.$cost_centre.'" and b.id_ccallocation = "'.$status.'" type LEFT';            
                }

                if(in_array(user('id_group'), [BUDGET_PIC_FACTORY,SCM,OPR,QC])) {
                    if($costCentreUser) $arr['where']['__m2'] = '(b.cost_centre IN ('.implode(',', $costCentreUser).') OR b.cost_centre IS NULL)';
                    $arr['where']['__m1'] = 'substr(a.account_code,1,2) != "72"' ;
                }elseif(user('id_group') == HRD && $cost_centre == '1200'){
                    $arr['where']['__m1'] = 'substr(a.account_code,1,3) != "721"' ;
                }elseif($isAllHrd){
                    // $arr['where']['__m'] = '(a.parent_id ="'.$m1->id.'" and substr(a.account_code,1,3) = "721" and b.cost_centre != "1200")' ;
                }

                // if($group_cc == true ) {
                //     $arr['where']['__m2'] = '(b.cost_centre IN ('.implode(',', $costCentreUser).') OR b.cost_centre IS NULL)';
                //     $arr['where']['__m2'] = '(b.cost_centre IN ('.implode(',', $cc_member).') OR b.cost_centre IS NULL)';
                // }

                $data['mst_account'][$m1->id] = get_data('tbl_fact_template_report a',$arr)->result();

                foreach($data['mst_account'][$m1->id] as $m2) {
                    $customWhere = [
                        '__m0'=>'(a.parent_id = "'.$m2->id.'")',
                    ];
        
                    // if($isAllHrd){
                    //     $customWhere = array();
                    // }
                    $arr = [
                        'select' => 'a.id,a.account_code,a.account_name, b.cost_centre, a.urutan,  
                                '.$customSelect,
                        // 'join' => $table . ' b on a.account_code = b.account_code and b.cost_centre ="'.$cost_centre.'" and b.id_ccallocation = "'.$status.'" type LEFT',            
                        // 'where'=>[
                        //     '__m0'=>'(a.parent_id = "'.$m2->id.'")',
                        // ],
                        // 'group_by' => 'a.id,a.account_code,a.account_name,b.cost_centre,a.urutan',
                        'where' => $customWhere,
                        'sort_by'=>'a.urutan'
                    ];

                    if($cost_centre == 'ALL') {
                        $arr['group_by'] = 'a.id,a.account_code,a.account_name,a.urutan'; 
                        $arr['join'] =   $table . ' b on a.account_code = b.account_code and b.id_ccallocation = "'.$status.'" type LEFT';            
                    }elseif($group_cc == true){
                        $arr['group_by'] = 'a.id,a.account_code,a.account_name,a.urutan';
                        $arr['join'] =   $table . ' b on a.account_code = b.account_code and b.cost_centre IN ('.implode(',', $cc_member).') and b.id_ccallocation = "'.$status.'" type LEFT';            
                    }else{
                        $arr['group_by'] = 'a.id,a.account_code,a.account_name,b.cost_centre,a.urutan';
                        $arr['join'] =   $table . ' b on a.account_code = b.account_code and b.cost_centre ="'.$cost_centre.'" and b.id_ccallocation = "'.$status.'" type LEFT';            

                    }

                    if(in_array(user('id_group'), [BUDGET_PIC_FACTORY,SCM,OPR,QC])) {
                        if($costCentreUser) $arr['where']['__m2'] = '(b.cost_centre IN ('.implode(',', $costCentreUser).') OR b.cost_centre IS NULL)';
                        $arr['where']['__m'] = 'substr(a.account_code,1,2) != "72"' ;
                    }elseif(user('id_group') == HRD && $cost_centre == '1200'){
                        $arr['where']['__m'] = 'substr(a.account_code,1,3) != "721"' ;
                    }elseif($isAllHrd){
                        // $arr['where']['__m'] = '(a.parent_id ="'.$m2->id.'" and substr(a.account_code,1,3) = "721" and b.cost_centre != "1200")' ;
                    }

                    if($group_cc == true ) {
                        $arr['where']['__m53'] = '(b.cost_centre IN ('.implode(',', $cc_member).') OR b.cost_centre IS NULL)';
                    }

                    $data['mst_account'][$m2->id] = get_data('tbl_fact_template_report a',$arr)->result();
                }
            }
        }

        // debug($cost_centre);die;
        $arrh = [
            'select' => '*',
            'where' => [
                'is_active' => 1,
                'sum_of' => "",
                'parent_id' => 0,
            ],
            'sort_by' => 'urutan',
        ];

        if(in_array(user('id_group'), [BUDGET_PIC_FACTORY,SCM,OPR,QC])) {
            if($costCentreUser) $arr['where']['__m2'] = '(a.cost_centre IN ('.implode(',', $costCentreUser).') OR a.cost_centre IS NULL)';
            $arrh['where']['__m'] = 'substr(account_code,1,2) != "72"' ;
        }elseif(user('id_group') == HRD && $cost_centre == '1200'){
            $arrh['where']['__m'] = 'substr(a.account_code,1,2) != "721"' ;
        }

        $total_header = get_data('tbl_fact_template_report',$arrh)->result();



        $data['total_header'] = [];
        $acc= [];
        foreach($total_header as $th) {
            $child1= [''];
            $childx = get_data('tbl_fact_template_report',[
                'where' => [
                    'is_active' => 1,
                    'parent_id' => $th->id,
                ],
            ])->result();
            $child1=[''];
            foreach($childx as $c) {
                $child1[] = $c->id;
            }

            $child2= [''];
            $childxy = get_data('tbl_fact_template_report',[
                'where' => [
                    'is_active' => 1,
                    'parent_id' => $child1,
                ],
            ])->result();

            foreach($childxy as $c2) {
                $child2[] = $c2->id;
            }

            $child3= [''];
            if(count($child2)) {
            $childxyx = get_data('tbl_fact_template_report',[
                'where' => [
                    'is_active' => 1,
                    'parent_id' => $child2,
                ],
            ])->result();
            }
            
            if(count($child3)){
                foreach($childxyx as $c3) {
                    $child3[] = $c3->id;
                }
            }
            

            $child = array_unique(array_merge($child1,$child2,$child3));

  
            $childxyz = get_data('tbl_fact_template_report',[
                'where' => [
                    'is_active' => 1,
                    'id' => $child,
                ],
                'sort_by' => 'account_code',
            ])->result();

            $acc= [];
            foreach($childxyz as $c) {
                $acc[] = $c->account_code;
            }

            // debug($acc);die;

            if(count($acc)){
                $arr = [
                    'select' => $customSelect2,
                    'where' => [
                        'account_code' => $acc,
                        // 'cost_centre' => $cca,
                        'id_ccallocation' => $status
                    ],
                ];

                if($status == 0 ) {
                    $arr['where']['a.id_ccallocation'] = 0; 
                }

                if($cost_centre !='ALL') {
                   $arr['where']['cost_centre'] = $cost_centre;
                }

                // $arr['where']['cost_centre'] = $cca;
                if(in_array(user('id_group'), [BUDGET_PIC_FACTORY,SCM,OPR,QC])) {
                    $arr['where']['__m'] = 'substr(a.account_code,1,2) != "72"' ;
                }//elseif(user('id_group') == HRD){
                //     $arr['where']['__m'] = '(substr(a.account_code,1,3) != "721" and a.cost_centre ="1200")' ;
                // }

                $sum = get_data($table . ' a',$arr)->row();
            }else{
                $sum = get_data($table . ' a',[
                    'select' => $customSelect2,
                    'where' => [
                        'account_code' => 0,
                        'cost_centre' => '0',
                    ],
                ])->row();
            }


            $data['total_header'][$th->id] =
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
                'total' => $sum->total_budget,

                'EST_01' => $sum->EST_01,
                'EST_02' => $sum->EST_02,
                'EST_03' => $sum->EST_03,
                'EST_04' => $sum->EST_04,
                'EST_05' => $sum->EST_05,
                'EST_06' => $sum->EST_06,
                'EST_07' => $sum->EST_07,
                'EST_08' => $sum->EST_08,
                'EST_09' => $sum->EST_09,
                'EST_10' => $sum->EST_10,
                'EST_11' => $sum->EST_11,
                'EST_12' => $sum->EST_12,
                'total_est' => $sum->EST_01+$sum->EST_02+$sum->EST_03+$sum->EST_04+$sum->EST_05+$sum->EST_06+$sum->EST_07+$sum->EST_08+$sum->EST_09+$sum->EST_10+$sum->EST_11+$sum->EST_12,
            ];
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

        if(in_array(user('id_group'), [BUDGET_PIC_FACTORY,SCM,OPR,QC])) {
            $arrl['where']['__m'] = 'substr(account_code,1,2) != "72"' ;
        }

        $total_labour = get_data('tbl_fact_template_report',$arrl)->result();

        $data['labour'] = [];
        $data['id_labour'] = [];
        foreach($total_labour as $m) {

            $arr = [
                'select' => $customSelect2,
                'where' => [
                    'a.account_code' => json_decode($m->sum_of),
                    // 'a.cost_centre' => $cca,
                    'a.id_ccallocation' => $status
                ],
            ];

            if($status == 0 ) {
                $arr['where']['a.id_ccallocation'] = 0; 
            }

            if($cost_centre != "ALL") {
                $arr['where']['a.cost_centre'] = $cost_centre;
            }
            // $arr['where']['cost_centre'] = $cca;

            // $arr['where']['cost_centre'] = $cca;
            if(in_array(user('id_group'), [BUDGET_PIC_FACTORY,SCM,OPR,QC])) {
                if($costCentreUser) $arr['where']['__m'] = '(a.cost_centre IN ('.implode(',', $costCentreUser).') OR a.cost_centre IS NULL)';
                $arr['where']['__m'] = 'substr(account_code,1,2) != "72"' ;
            }elseif(user('id_group') == HRD){
                // $arr['where']['__m'] = '(substr(a.account_code,1,3) != "721" and a.cost_centre ="1200")' ;
            }

            $sum = get_data($table . ' a',$arr)->row();

            $data['total_labour'][$m->id] =
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
                'total' => $sum->total_budget,

                'EST_01' => $sum->EST_01,
                'EST_02' => $sum->EST_02,
                'EST_03' => $sum->EST_03,
                'EST_04' => $sum->EST_04,
                'EST_05' => $sum->EST_05,
                'EST_06' => $sum->EST_06,
                'EST_07' => $sum->EST_07,
                'EST_08' => $sum->EST_08,
                'EST_09' => $sum->EST_09,
                'EST_10' => $sum->EST_10,
                'EST_11' => $sum->EST_11,
                'EST_12' => $sum->EST_12,
                'total_le' => $sum->total_le,
            ];

            $data['id_labour'][] = $m->id;
        }

        // Debug: Check if total_header has data
        // Uncomment these lines to debug:
        // error_log('Total Header Count: ' . count($data['total_header']));
        // error_log('Total Header Data: ' . print_r(array_keys($data['total_header']), true));

        $response	= array(
            'table'		=> $this->load->view('reporting/budget_by_deptnew/table',$data,true),
            'table2'		=> $this->load->view('reporting/budget_by_deptnew/table2',$data,true),
        );
	   
	    render($response,'json');
	}


    function get_subaccount(){
		$cost_centre = post('cost_centre');
		$r = get_data('tbl_fact_cost_centre', [
			'where' => [
				'kode' => $cost_centre,
				'is_active' => 1
			]
		])->row(); 


        $res['sub_acc'] = get_data('tbl_fact_sub_account','id',json_decode($r->id_sub_account,true))->result_array();

		render($res['sub_acc'], 'json');
	}

    function get_produk(){
		$cost_centre = post('cost_centre');

        if($cost_centre != '3100') {
            $is_active = '9';
        }else{
            $is_active = '1';
        } 

		$r = get_data('tbl_fact_product', [
			'where' => [
				'is_active' => $is_active
			]
		])->result_array(); 

        $res['product'] = $r;

		render($res['product'], 'json');
	}


    function get_user(){
		$cost_centre = post('cost_centre');
        $tahun = post('tahun');
		$r = get_data('tbl_fact_pic_budget', [
			'where' => [
				'tahun' => $tahun,
                'cost_centre' => $cost_centre,
			]
		])->row(); 


        $res['user'] = get_data('tbl_user','id',json_decode($r->user_id,true))->result_array();

		render($res['user'], 'json');
	}

	function get_data() {
		$data = get_data('tbl_m_bottomup_besaran','id',post('id'))->row_array();
		render($data,'json');
	}	

    function save_perubahan() {       

        $table = 'tbl_fact_lstbudget_' . user('tahun_budget');
        $data   = json_decode(post('json'),true);

        foreach($data as $id => $record) {
            $result = $record;
            foreach ($result as $r => $v) {               
                update_data($table, $result,'id',$id);

                $upd = get_data($table, 'id',$id)->row();
                $field = '';
                $total = 0;
                for ($i = 1; $i <= 12; $i++) { 
                    $field = 'B_' . sprintf('%02d', $i);
                    $total += $upd->$field ;
                }
                update_data($table,['total_budget' => $total],'id',$upd->id);
            }        
        }
    }

    function import() {
		ini_set('memory_limit', '-1');

        $table = 'tbl_fact_lstbudget_' .post('tahun');
        $cost_centre = post('cost_centre');
        $filter = post();
        
        if($filter['tab'] == '#result2'){
            $col = ['ACCOUNT_CODE', 'SUB_ACCOUNT', 'JANUARY','FEBRUARY', 'MARCH', 'APRIL', 'MAY', 'JUNE', 'JULY', 'AUGUST', 'SEPTEMBER', 'OCTOBER', 'NOVEMBER', 'DECEMBER'];
        } elseif($filter['tab'] == '#result') {
            $col = ['ACCOUNT_CODE', 'SUB_ACCOUNT', 'EST_01', 'EST_02', 'EST_03', 'EST_04', 'EST_05', 'EST_06', 'EST_07', 'EST_08', 'EST_09', 'EST_10', 'EST_11', 'EST_12'];
        } elseif($filter['tab'] == '#result3'){
            $col = ['ACCOUNT_CODE', 'SUB_ACCOUNT', 'THN_01', 'THN_02', 'THN_03', 'THN_04', 'THN_05', 'THN_06', 'THN_07', 'THN_08', 'THN_09', 'THN_10'];
        }

    
		$file = post('fileimport');


		$this->load->library('simpleexcel');
		$this->simpleexcel->define_column($col);
		$jml = $this->simpleexcel->read($file);

		$c = 0;
        $save = 0;
		foreach($jml as $i => $k) {
			if($i==0) {
				for($j = 11; $j <= $k; $j++) {
					$data = $this->simpleexcel->parsing($i,$j);

                    if($cost_centre == 'ALL') {
                        $response = [
                            'status' => 'failed',
                            'message' => 'Silakan pilih cost centre untuk import data'
                        ];
                        render($response,'json');
                        return ;
                    }

                    $sub_acc = get_data('tbl_fact_cost_centre',[
                        'select' => 'id_sub_account',
                        'where' => [
                            'kode' => $cost_centre,
                        ],
                    ])->row();

                    if(isset($sub_acc->id_sub_account)) {

                        $id_sub_acc1 = json_decode($sub_acc->id_sub_account);
                         $id_sub_acc = get_data('tbl_fact_sub_account','id', $id_sub_acc1[0])->row();

 
                    // debug($data['januari']);die;
                        // debug($data['januari']);die;
                        if($filter['tab'] == "#result"){
                            $data2['EST_01'] = (isset($data['EST_01']) ? str_replace(['.',','],'',$data['EST_01']) : 0);
                            $data2['EST_02'] = (isset($data['EST_02']) ? str_replace(['.',','],'',$data['EST_02']) : 0);
                            $data2['EST_03'] = (isset($data['EST_03']) ? str_replace(['.',','],'',$data['EST_03']) : 0);
                            $data2['EST_04'] = (isset($data['EST_04']) ? str_replace(['.',','],'',$data['EST_04']) : 0);
                            $data2['EST_05'] = (isset($data['EST_05']) ? str_replace(['.',','],'',$data['EST_05']) : 0);
                            $data2['EST_06'] = (isset($data['EST_06']) ? str_replace(['.',','],'',$data['EST_06']) : 0);
                            $data2['EST_07'] = (isset($data['EST_07']) ? str_replace(['.',','],'',$data['EST_07']) : 0);
                            $data2['EST_08'] = (isset($data['EST_08']) ? str_replace(['.',','],'',$data['EST_08']) : 0);
                            $data2['EST_09'] = (isset($data['EST_09']) ? str_replace(['.',','],'',$data['EST_09']) : 0);
                            $data2['EST_10'] = (isset($data['EST_10']) ? str_replace(['.',','],'',$data['EST_10']) : 0);
                            $data2['EST_11'] = (isset($data['EST_11']) ? str_replace(['.',','],'',$data['EST_11']) : 0);
                            $data2['EST_12'] = (isset($data['EST_12']) ? str_replace(['.',','],'',$data['EST_12']) : 0);
        
                            $field = "" ;
                            for ($j = 1; $j <= setting('actual_budget'); $j++) {
                                $field = 'EST_' . sprintf('%02d', $j);
                                unset($data2[$field]) ;                    
                            }
                            
                        } else if($filter['tab'] == '#result2'){
                            $data2['B_01'] = (isset($data['JANUARY']) ? str_replace(['.',','],'', $data['JANUARY']) : 0);
                            $data2['B_02'] = (isset($data['FEBRUARY']) ? str_replace(['.',','],'',$data['FEBRUARY']) : 0);
                            $data2['B_03'] = (isset($data['MARCH']) ? str_replace(['.',','],'',$data['MARCH']) : 0);
                            $data2['B_04'] = (isset($data['APRIL']) ? str_replace(['.',','],'',$data['APRIL']) : 0);
                            $data2['B_05'] = (isset($data['MAY']) ? str_replace(['.',','],'',$data['MAY']) : 0);
                            $data2['B_06'] = (isset($data['JUNE']) ? str_replace(['.',','],'',$data['JUNE']) : 0);
                            $data2['B_07'] = (isset($data['JULY']) ? str_replace(['.',','],'',$data['JULY']) : 0);
                            $data2['B_08'] = (isset($data['AUGUST']) ? str_replace(['.',','],'',$data['AUGUST']) : 0);
                            $data2['B_09'] = (isset($data['SEPTEMBER']) ? str_replace(['.',','],'',$data['SEPTEMBER']) : 0);
                            $data2['B_10'] = (isset($data['OCTOBER']) ? str_replace(['.',','],'',$data['OCTOBER']) : 0);
                            $data2['B_11'] = (isset($data['NOVEMBER']) ? str_replace(['.',','],'',$data['NOVEMBER']) : 0);
                            $data2['B_12'] = (isset($data['DECEMBER']) ? str_replace(['.',','],'',$data['DECEMBER']) : 0);
                        } else if($filter['tab'] == '#result3'){
                            $data2['THN_01'] = (isset($data['THN_01']) ? str_replace(['.',','],'',$data['THN_01']) : 0);
                            $data2['THN_02'] = (isset($data['THN_02']) ? str_replace(['.',','],'',$data['THN_02']) : 0);
                            $data2['THN_03'] = (isset($data['THN_03']) ? str_replace(['.',','],'',$data['THN_03']) : 0);
                            $data2['THN_04'] = (isset($data['THN_04']) ? str_replace(['.',','],'',$data['THN_04']) : 0);
                            $data2['THN_05'] = (isset($data['THN_05']) ? str_replace(['.',','],'',$data['THN_05']) : 0);
                            $data2['THN_06'] = (isset($data['THN_06']) ? str_replace(['.',','],'',$data['THN_06']) : 0);
                            $data2['THN_07'] = (isset($data['THN_07']) ? str_replace(['.',','],'',$data['THN_07']) : 0);
                            $data2['THN_08'] = (isset($data['THN_08']) ? str_replace(['.',','],'',$data['THN_08']) : 0);
                            $data2['THN_09'] = (isset($data['THN_09']) ? str_replace(['.',','],'',$data['THN_09']) : 0);
                            $data2['THN_10'] = (isset($data['THN_10']) ? str_replace(['.',','],'',$data['THN_10']) : 0);
                        }

                        
                        $data2['create_at'] = date('Y-m-d H:i:s');
                        $data2['create_by'] = user('nama');
                        // if(in_array($data['Code'],$acc1))
                        $save = update_data($table,$data2,['account_code'=>$data['ACCOUNT_CODE'],'cost_centre'=>$cost_centre,'sub_account'=>$id_sub_acc->sub_account]);					
                        if($save) $c++;
                    }
				}
			}
		}


		

		
		$response = [
			'status' => 'success',
			'message' => $c.' '.lang('data_berhasil_disimpan').'.'
		];
		@unlink($file);
		render($response,'json');
	}

    function isi_listbudget($tahun="",$cc=""){
        isi_budget_acaount($tahun,$cc);
    }
}

