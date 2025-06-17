<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');

class Breakdown_budget extends BE_Controller {

    var $controller = 'Breakdown_budget';
	function __construct() {
		parent::__construct();
	}

	function index() {
		$data['tahun'] = get_data('tbl_fact_tahun_budget', [
            'where' => [
                'is_active' => 1,
                'tahun' => user('tahun_budget')
            ],
        ])->result();   
        
        $arr = [
            'select' => 'a.cost_centre as kode, b.cost_centre',
                'join' => 'tbl_fact_cost_centre b on a.cost_centre = b.kode',
                'where' => [
                    'b.is_active'=>1,
                    'a.tahun' => user('tahun_budget'),
                ],
                'group_by' => 'a.cost_centre',
        ];

        if(in_array(user('id_group'), [BUDGET_PIC_FACTORY,SCM,OPR])) {
            $xid = "%".user('id')."%";
            $arr['where']['__m'] = 'user_id like "'.$xid.'"' ;
        }


        $data['cc'] = get_data('tbl_fact_pic_budget a',$arr)->result_array(); 

        $aVc =[];
        foreach($data['cc'] as $a => $vc) {
            $aVc[] = $vc['kode'];
        }

   
        // $data['cc'] = get_data('tbl_fact_cost_centre',$arr)->result_array(); 

        $arr_acc = [
                'select' => 'account_code, CONCAT(account_code, " - ", account_name) as account_name',
                'where' => [
                    'is_active' => 1
                ]
            ];


        if(in_array(user('id_group'), [BUDGET_PIC_FACTORY,SCM,OPR,QC,IT,ENG,SCM])) {
            $arr_acc['where']['__m'] = 'substr(account_code,1,2) != "72"' ;
            $arr_acc['where']['__m1'] = 'account_code in (select account_code from tbl_fact_account_cc where cost_centre in ("'.implode(",",$aVc).'"))';
        }

        $data['account'] = get_data('tbl_fact_account', $arr_acc)->result_array(); 

        $access         = get_access($this->controller);
        $data['access'] = $access ;

 
	    render($data);
	}
	 
	function data($page = 1) {
        ini_set('memory_limit', '-1');
	    $limit = 50;
	    if($page) {
	        $page = ($page - 1) * $limit;
	    }
	 
        if(post('tahun')) {
			$table = 'tbl_fact_breakdown_budget_' .post('tahun');
		}

        if(table_exists($table)) {
            
            $arr_cc = [
                'select' => 'a.cost_centre as kode, b.cost_centre',
                    'join' => 'tbl_fact_cost_centre b on a.cost_centre = b.kode',
                    'where' => [
                        'b.is_active'=>1,
                        'a.tahun' => user('tahun_budget'),
                    ],
                    'group_by' => 'a.cost_centre',
            ];
    
            if(in_array(user('id_group'), [BUDGET_PIC_FACTORY,SCM,OPR,QC,IT,MPD,ENG,HRD])) {
                $xid = "%".user('id')."%";
                $arr_cc['where']['__m'] = 'user_id like "'.$xid.'"' ;
            }

            $cc_akses = get_data('tbl_fact_pic_budget a',$arr_cc)->result(); 

            $cca = [''];
            foreach($cc_akses as $c) {
                $cca[] = $c->kode;
            }


            $arr            = [
                'select'    => 'a.cost_centre, b.cost_centre as cost_centre_name',
                'join'      => 'tbl_fact_cost_centre b on a.cost_centre = b.kode',
                'where'     => [
                    'b.is_active' => 1,
                    'a.cost_centre' => $cca
                ],
                'group_by' => 'a.cost_centre',
                'sort_by' => 'a.cost_centre',
            ];

            if(post('filter_cost_centre')) {
                $arr['where']['a.cost_centre']  = post('filter_cost_centre');
            }


            $data_view['grup']    = get_data($table . ' a',$arr)->result_array();

            $attr = [
                'select' => "a.*,b.account_name, c.sub_account as sub_account_name, d.cost_centre as cost_centre_name",
                'join'   => ['tbl_fact_account b on a.account_code = b.account_code type LEFT',
                             'tbl_fact_sub_account c on a.sub_account = c.kode type LEFT',
                             'tbl_fact_cost_centre d on a.cost_centre = d.kode type LEFT'
                            ],
                'where'  => [
                    'a.is_active' => 0,                   
                    'a.cost_centre' => $cca

                ],
                'group_by' => 'a.id',
                'sort_by' => 'a.cost_centre',
                'limit' => $limit,
                'offset' => $page
            ];
            
            if(post('filter_cost_centre')) {
                $attr['like']['a.cost_centre'] = post('filter_cost_centre');
            }

            if(post('filter_account')) {
                $attr['like']['a.account_code']  = post('filter_account');
            }

            if(post('filter_username') && !empty(post('filter_username')) && post('filter_username') !="") {
                $attr['like']['a.user_id'] = post('filter_username');
            }

            if(in_array(user('id_group'), [BUDGET_PIC_FACTORY,SCM,OPR,QC])) {
                $attr['where']['__m'] = 'substr(a.account_code,1,2) != "72"' ;
            }elseif(user('id_group') == HRD) {
                $attr['where']['__m'] = '(substr(a.account_code,1,3) != "721" and a.cost_centre !="1200")' ;
            }

            $result = data_pagination($table. ' a',$attr,base_url('transaction/breakdown_budget/data/'),4);
        
            $data_view['record']    = $result['record'];

            // $data_view['grup']   = [];
            $data_view['grup']   = [];
            foreach($result['record'] as $r => $v) {
                $data_view['grup'][$v['cost_centre']]=[
                    'cost_centre' => $v['cost_centre'],
                    'cost_centre_name' => $v['cost_centre_name']
                ];
            
            }


            // debug($data_view['grup'] );die;

  
            $view   = $this->load->view('transaction/breakdown_budget/data',$data_view,true);
        
            $data = [
                'data'          => $view,
                'pagination'    => $result['pagination']
            ];
        }else{
            $attr = [
                'select' => "a.*,",
                'where'  => [
                    'a.is_active' => 9,
                ],
                'limit' => $limit,
                'offset' => $page
            ];
            

            $result = data_pagination('tbl_fact_cost_centre'. ' a',$attr,base_url('transaction/breakdown_budget/data/'),4);
        
            $data_view['record']    = $result['record'];


            $view   = $this->load->view('transaction/breakdown_budget/data',$data_view,true);
        
            $data = [
                'data'          => $view,
                'pagination'    => $result['pagination']
            ];
        }

        render($data,'json');
	}


	function template() {
		ini_set('memory_limit', '-1');
		$arr = ['description' => 'description','ref' => 'ref','account_code' => 'account_code','account_name' => 'account_name','cost_centre' => 'cost_centre','sub_account' => 'sub_account','account_cost' => 'account_cost','initial1' => 'initial1','inniial2' => 'inniial2','user_id' => 'user_id','B_01' => 'B_01','B_02' => 'B_02','B_03' => 'B_03','B_04' => 'B_04','B_05' => 'B_05','B_06' => 'B_06','B_07' => 'B_07','B_08' => 'B_08','B_09' => 'B_09','B_10' => 'B_10','B_11' => 'B_11','B_12' => 'B_12','total' => 'total','is_active' => 'is_active'];
		$config[] = [
			'title' => 'template_import_breakdown_budget',
			'header' => $arr,
		];
		$this->load->library('simpleexcel',$config);
		$this->simpleexcel->export();
	}

	function import() {
		ini_set('memory_limit', '-1');

        // cek akses cost centre //
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

            $cca = [''];
            foreach($cc_akses as $c) {
                $cca[] = $c->kode;
            }

            $arr_acc = [
                'select' => 'account_code, sub_account',
                'where' => [
                    'is_active' => 1
                ]
            ];


        if(in_array(user('id_group'), [BUDGET_PIC_FACTORY,SCM,OPR,QC])) {
            $arr_acc['where']['cost_centre'] = $cca ;
        }

        $access_acc = get_data('tbl_fact_account_cc', $arr_acc)->result(); 
        $acc_a = [];
        $acc_s = [];
        foreach($access_acc as $a) {
            $acc_a[] = $a->account_code;
            $acc_s[] = $a->sub_account;

        } 

        //
        $table = 'tbl_fact_breakdown_budget_' .post('tahun_budget');
        $table2 = 'tbl_fact_lstbudget_' . post('tahun_budget');
        $user_id = (!empty(post('username')) ? post('username') : 0);

        if(empty($user_id) || $user_id == 0 )  {
            $response = [
                'status' => 'failed',
                'message' => 'Untuk Import Data anda harus memilih filter user name',
            ];
            render($response,'json');
        }
        

        $cost_centre = (!empty(post('cost_centre')) ? post('çost_centre') : '');
        $tahun = post('tahun_budget');
  
		$file = post('fileimport');
		$col = ['no','sub_no','description','account_code','account_description','sub_acc_code','sub_account_description',
                'cc_code','cc_initital', 'currency_code','cur','amount','rate','in_idr','jan','feb','mar','apr','may','jun','jul','aug','sep','oct','nov','dec','total_spend'];
		$this->load->library('simpleexcel');
		$this->simpleexcel->define_column($col);
		$jml = $this->simpleexcel->read($file);
		$c = 0;
        $cc = [];
        $sc = [];
		foreach($jml as $i => $k) {
			if($i==0) {
				for($j = 10; $j <= $k; $j++) {
					$data = $this->simpleexcel->parsing($i,$j);
  
                    if($data['account_code'] != "") {

                        $data2['description'] = $data['description'];
                        $data2['account_code'] = $data['account_code'];

                        // if(($user_id != 0 && $data['cc_code'] != $cost_centre) && in_array(user('id_group'), [BUDGET_PIC_FACTORY,SCM,OPR,QC])) {
                        //     $response = [
                        //         'status' => 'failed',
                        //         'message' => 'Data cost centre tidak sesuai denga pic budget'
                        //     ];
                        //     render($response,'json');
                        //     die;
                        // }   

                        if(in_array(user('id_group'), [BUDGET_PIC_FACTORY,SCM,OPR,QC]) && substr($data['account_code'],1,2) == '72'){
                            $response = [
                                'status' => 'failed',
                                'message' => 'Access di tolak untuk account ' . $data['account_code'],
                            ];
                            render($response,'json');
                            die;
                        }

                        if(in_array(user('id_group'), [BUDGET_PIC_FACTORY,SCM,OPR,QC]) && !in_array($data['account_code'],$acc_a)){
                            $response = [
                                'status' => 'failed',
                                'message' => 'Access di tolak untuk account ' . $data['account_code'],
                            ];
                            render($response,'json');
                            die;
                        }

                        // if(in_array(user('id_group'), [BUDGET_PIC_FACTORY,SCM,OPR,QC]) && !in_array($data['cc_code'],$cca)){
                        //     $response = [
                        //         'status' => 'failed',
                        //         'message' => 'Access di tolak untuk cost_centre ' . $data['cc_code'],
                        //     ];
                        //     render($response,'json');
                        //     die;
                        // }


                        // if(in_array(user('id_group'), [BUDGET_PIC_FACTORY,SCM,OPR,QC]) && !in_array($data['sub_acc_code'],$acc_s)){
                        //     $response = [
                        //         'status' => 'failed',
                        //         'message' => 'Access di tolak untuk sub_account ' . $data['sub_acc_code'],
                        //     ];
                        //     render($response,'json');
                        //     die;
                        // }


                        $data2['cost_centre'] = $data['cc_code'];
                        $data2['sub_account'] = $data['sub_acc_code'];
                        $data2['user_id'] = $user_id;
                        $data2['B_01'] = (@$data['jan'] ? str_replace(['.',','],'', $data['jan']) : 0);
                        $data2['B_02'] = (@$data['feb'] ? str_replace(['.',','],'', $data['feb']) : 0);
                        $data2['B_03'] = (@$data['mar'] ? str_replace(['.',','],'', $data['mar']) : 0);
                        $data2['B_04'] = (@$data['apr'] ? str_replace(['.',','],'', $data['apr']) : 0);
                        $data2['B_05'] = (@$data['may'] ? str_replace(['.',','],'', $data['may']) : 0);
                        $data2['B_06'] = (@$data['jun'] ? str_replace(['.',','],'', $data['jun']) : 0);
                        $data2['B_07'] = (@$data['jul'] ? str_replace(['.',','],'', $data['jul']) : 0);
                        $data2['B_08'] = (@$data['aug'] ? str_replace(['.',','],'', $data['aug']) : 0);
                        $data2['B_09'] = (@$data['sep'] ? str_replace(['.',','],'', $data['sep']) : 0);
                        $data2['B_10'] = (@$data['oct'] ? str_replace(['.',','],'', $data['oct']) : 0);
                        $data2['B_11'] = (@$data['nov'] ? str_replace(['.',','],'', $data['nov']) : 0);
                        $data2['B_12'] = (@$data['dec'] ? str_replace(['.',','],'', $data['dec']) : 0);
                        $data2['total'] = 
                            (@$data['jan'] ? str_replace(['.',','],'', $data['jan']) : 0) +
                            (@$data['feb'] ? str_replace(['.',','],'', $data['feb']) : 0) +
                            (@$data['mar'] ? str_replace(['.',','],'', $data['mar']) : 0) +
                            (@$data['apr'] ? str_replace(['.',','],'', $data['apr']) : 0) +
                            (@$data['may'] ? str_replace(['.',','],'', $data['may']) : 0) +
                            (@$data['jun'] ? str_replace(['.',','],'', $data['jun']) : 0) +
                            (@$data['jul'] ? str_replace(['.',','],'', $data['jul']) : 0) +
                            (@$data['aug'] ? str_replace(['.',','],'', $data['aug']) : 0) +
                            (@$data['sep'] ? str_replace(['.',','],'', $data['sep']) : 0) +
                            (@$data['oct'] ? str_replace(['.',','],'', $data['oct']) : 0) +
                            (@$data['nov'] ? str_replace(['.',','],'', $data['nov']) : 0) +
                            (@$data['dec'] ? str_replace(['.',','],'', $data['dec']) : 0);
                        $data2['create_at'] = date('Y-m-d H:i:s');
                        $data2['create_by'] = user('nama');

                        if(!in_array($data['cc_code'],$cc)) $cc[] = $data['cc_code'];
                        if(!in_array($data['sub_acc_code'],$sc)) $sc[] = $data['sub_acc_code'];
                        $save = insert_data($table,$data2);
                        if($save) $c++;
                    }
				}
			}
		}
		$response = [
			'status' => 'success',
			'message' => $c.' '.lang('data_berhasil_disimpan').'.'
		];


        if($response['status']== 'success') {

            foreach($cc as $c => $vc) {
                $where = [];
                $where1 = [];
                $where['a.cost_centre'] = $vc;
                
                foreach($sc as $c2 => $vs) {
                    $where['a.sub_account'] = $vs;

                    $cek1 = get_data('tbl_fact_account_cc a',[
                        'select' => 'a.cost_centre,a.sub_account,a.account_code,a.account_name,b.id as id_trx, c.id as id_cost_centre',
                        'join'   => [$table2.' b on a.account_code = b.account_code and a.cost_centre = b.cost_centre and a.sub_account = b.sub_account type LEFT', 
                                    'tbl_fact_cost_centre c on a.cost_centre = c.kode type LEFT',      
                                    ],
                        'where' => $where + $where1,
                    ])->result();

        
                    $cek2 = get_data('tbl_fact_account_cc a',[
                        'select' => 'a.cost_centre,a.sub_account,a.account_code,a.account_name,b.id as id_trx, c.id as id_cost_centre',
                        'join'   => [$table2.' b on a.account_code = b.account_code and a.cost_centre = b.cost_centre and a.sub_account = b.sub_account type LEFT', 
                                    'tbl_fact_cost_centre c on a.cost_centre = c.kode',      
                                    ],
                        'where' => $where 
                    ])->result();

                    
                    if(count($cek1)) {
                        $id_trx = 0;
                        foreach($cek1 as $c) {
                            if(!empty($c->id_trx)) $id_trx = $c->id_trx ;
                            $data['id'] = $data['id'] = $id_trx ;
                            $data['tahun'] = $tahun;
                            $data['id_cost_centre'] = $c->id_cost_centre;
                            $data['cost_centre'] = $c->cost_centre;
                            $data['account_code'] = $c->account_code;
                            $data['account_name'] = $c->account_name;
                            $data['sub_account'] = $c->sub_account;
                            save_data($table2,$data);
                        }
                    }else{
                        $id_trx = 0;
                        foreach($cek2 as $c2) {
                            if(!empty($c2->id_trx)) $id_trx = $c2->id_trx ;
                            $data2['id'] = $data2['id'] = $id_trx ;
                            $data2['tahun'] = $tahun;
                            $data2['id_cost_centre'] = $c2->id_cost_centre;
                            $data2['cost_centre'] = $c2->cost_centre;
                            $data2['account_code'] = $c2->account_code;
                            $data2['account_name'] = $c2->account_name;
                            $data2['sub_account'] = $c2->sub_account;

                            save_data($table2,$data2);
                        }
                    }
                }
            }
            
            $sum = get_data($table . ' a',[
                'select' => 'a.cost_centre,a.sub_account, a.account_code, sum(B_01) as B_01, sum(B_02) as B_02, sum(B_03) as B_03, sum(B_04) as B_04, sum(B_05) as B_05, sum(B_06) as B_06, 
                    sum(B_07) as B_07, sum(B_08) as B_08, sum(B_09) as B_09, sum(B_10) as B_10, sum(B_11) as B_11, sum(B_12) as B_12',
                'where' => [
                    'a.cost_centre' => $cc,
                ],
                'group_by' => 'a.cost_centre,a.sub_account,a.account_code'
            ])->result();

            // debug($sum);die;
            foreach($sum as $s) {
                $data_update = [
                    'B_01' => $s->B_01,
                    'B_02' => $s->B_02,
                    'B_03' => $s->B_03,
                    'B_04' => $s->B_04,
                    'B_05' => $s->B_05,
                    'B_06' => $s->B_06,
                    'B_07' => $s->B_07,
                    'B_08' => $s->B_08,
                    'B_09' => $s->B_09,
                    'B_10' => $s->B_10,
                    'B_11' => $s->B_11,
                    'B_12' => $s->B_12,
                    'total_budget' => $s->B_01+$s->B_02+$s->B_03+$s->B_04+$s->B_05+$s->B_06+$s->B_07+$s->B_08+$s->B_09+$s->B_10+$s->B_11+$s->B_12,
                ];
                update_data($table2, $data_update,['cost_centre'=>$s->cost_centre,'sub_account'=>$s->sub_account,'account_code'=>$s->account_code]);
            };
        }

		@unlink($file);
		render($response,'json');
	}


    function sum_budget_acaount($tahun="",$cc="") {
        ini_set('memory_limit', '-1');
		ini_set('max_execution_time', 0);

        $table = 'tbl_fact_breakdown_budget_' .$tahun;
        $table2 = 'tbl_fact_lstbudget_' . $tahun;

        // $cc1 = $cc;
        if(empty($cc) || $cc=="") {
            $lstcc = get_data($table,[
                'select' => 'cost_centre',
                'where' => [
                    'cost_centre !=' => "",
                ],
                'group_by' => 'cost_centre',
            ])->result();
        }else{
            $lstcc = get_data($table,[
                'select' => 'cost_centre',
                'where' => [
                    'cost_centre' => $cc,
                ],
                'group_by' => 'cost_centre',
            ])->result();
        }

        $jum = 0;
        foreach($lstcc as $c) {
            $cc = $c->cost_centre;
            $cost_centre = get_data('tbl_fact_cost_centre','kode',$cc)->row();
            $sub_account = json_decode($cost_centre->id_sub_account);

            $data_update0 = [
                'B_01' => 0,
                'B_02' => 0,
                'B_03' => 0,
                'B_04' => 0,
                'B_05' => 0,
                'B_06' => 0,
                'B_07' => 0,
                'B_08' => 0,
                'B_09' => 0,
                'B_10' => 0,
                'B_11' => 0,
                'B_12' => 0,
                'total_budget' => 0,
            ];

            update_data($table2, $data_update0,['cost_centre'=>$cc]);

            foreach($sub_account as $s) {
                $sa = get_data('tbl_fact_sub_account',[
                    'where' => [
                        'id' => $s
                    ],
                    'sort_by' => 'kode',
                    'sort' => 'DESC',
                ])->row();
                $acc = get_data($table .' a',[
                    'select' => 'a.*,b.id as id_cost_centre, c.id as id_account, c.account_name',
                    'join' => ['tbl_fact_cost_centre b on a.cost_centre = b.kode type LEFT',
                            'tbl_fact_account c on a.account_code = c.account_code'
                            ],
                    'where' => [
                        'a.cost_centre' => $cc,
                        'a.sub_account' => $sa->kode,
                        // 'c.is_active' => 1,
                        // 'a.account_code' => '725141-1',
                    ],
                    ])->result();
                
                $acc_akses = [];
                foreach($acc as $a) {
                    $acc_akses[] = $a->account_code;
                    $cek = get_data($table2,[
                        'where' => [
                            'cost_centre' => $cc,
                            'sub_account' => $sa->kode,
                            'account_code' => $a->account_code
                        ]
        
                    ])->row();
                    
                    $data2 = [];
                    if(!isset($cek->id)) {
                        $data2['tahun'] = $tahun;
                        $data2['id_cost_centre'] = $a->id_cost_centre;
                        $data2['cost_centre'] = $a->cost_centre;
                        $data2['id_account'] = $a->id_account;
                        $data2['account_code'] = $a->account_code;
                        $data2['account_name'] = $a->account_name;
                        $data2['sub_account'] = $a->sub_account;

                        insert_data($table2,$data2);
                    }


                }

                // debug($acc_akses);die;

     
                if(count($acc_akses)) {
      
                // delete_data($table2,['account_code not'=> $acc_akses, 'cost_centre'=>$cc,'sub_account'=>$sa->kode]); 

                    $sum = get_data($table . ' a',[
                        'select' => 'a.cost_centre,a.sub_account, a.account_code, sum(B_01) as B_01, sum(B_02) as B_02, sum(B_03) as B_03, sum(B_04) as B_04, sum(B_05) as B_05, sum(B_06) as B_06, 
                            sum(B_07) as B_07, sum(B_08) as B_08, sum(B_09) as B_09, sum(B_10) as B_10, sum(B_11) as B_11, sum(B_12) as B_12',
                        'where' => [
                            'a.cost_centre' => $cc,
                            'a.sub_account' => $sa->kode,
                            'a.account_code' => $acc_akses
                        ],
                        'group_by' => 'a.cost_centre,a.sub_account,a.account_code'
                    ])->result();
                
                           
                    foreach($sum as $s) {
                        // debug($sum) ;die;
                        $jum++;
                        $data_update = [
                            'B_01' => $s->B_01,
                            'B_02' => $s->B_02,
                            'B_03' => $s->B_03,
                            'B_04' => $s->B_04,
                            'B_05' => $s->B_05,
                            'B_06' => $s->B_06,
                            'B_07' => $s->B_07,
                            'B_08' => $s->B_08,
                            'B_09' => $s->B_09,
                            'B_10' => $s->B_10,
                            'B_11' => $s->B_11,
                            'B_12' => $s->B_12,
                            'total_budget' => $s->B_01+$s->B_02+$s->B_03+$s->B_04+$s->B_05+$s->B_06+$s->B_07+$s->B_08+$s->B_09+$s->B_10+$s->B_11+$s->B_12,
                        ];
                        update_data($table2, $data_update,['cost_centre'=>$s->cost_centre,'sub_account'=>$s->sub_account,'account_code'=>$s->account_code]);
                    };
                }
            }
        }

        
		render([
			'status'	=> 'success',
			'message'	=> 'Recalculate Sukses'
		],'json');	

        // echo 'success update ' .$jum . ' data' ;

    }

    function save_perubahan() {       
        $tahun = post('tahun');
        $table = 'tbl_fact_breakdown_budget_' . $tahun;
        $table2 = 'tbl_fact_lstbudget_' . user('tahun_budget');
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
                update_data($table,['total' => $total],'id',$upd->id);

                $sum = get_data($table . ' a',[
                    'select' => 'a.cost_centre,a.sub_account, a.account_code, sum(B_01) as B_01, sum(B_02) as B_02, sum(B_03) as B_03, sum(B_04) as B_04, sum(B_05) as B_05, sum(B_06) as B_06, 
                        sum(B_07) as B_07, sum(B_08) as B_08, sum(B_09) as B_09, sum(B_10) as B_10, sum(B_11) as B_11, sum(B_12) as B_12',
                    'where' => [
                        'a.cost_centre' => $upd->cost_centre,
                        'a.sub_account' => $upd->sub_account,
                        'a.account_code' => $upd->account_code
                    ],
                    'group_by' => 'a.cost_centre,a.sub_account,a.account_code'
                ])->result();
    
                // debug($sum);die;
                foreach($sum as $s) {
                    $data_update = [
                        'B_01' => $s->B_01,
                        'B_02' => $s->B_02,
                        'B_03' => $s->B_03,
                        'B_04' => $s->B_04,
                        'B_05' => $s->B_05,
                        'B_06' => $s->B_06,
                        'B_07' => $s->B_07,
                        'B_08' => $s->B_08,
                        'B_09' => $s->B_09,
                        'B_10' => $s->B_10,
                        'B_11' => $s->B_11,
                        'B_12' => $s->B_12,
                        'total_budget' => $s->B_01+$s->B_02+$s->B_03+$s->B_04+$s->B_05+$s->B_06+$s->B_07+$s->B_08+$s->B_09+$s->B_10+$s->B_11+$s->B_12,
                    ];
                    update_data($table2, $data_update,['cost_centre'=>$s->cost_centre,'sub_account'=>$s->sub_account,'account_code'=>$s->account_code]);
                };
            }        
        }
    }

	function export() {
		ini_set('memory_limit', '-1');
		$arr = ['description' => 'Description','ref' => 'Ref','account_code' => 'Account Code','account_name' => 'Account Name','cost_centre' => 'Cost Centre','sub_account' => 'Sub Account','account_cost' => 'Account Cost','initial1' => 'Initial1','inniial2' => 'Inniial2','user_id' => 'User Id','B_01' => 'B 01','B_02' => 'B 02','B_03' => 'B 03','B_04' => 'B 04','B_05' => 'B 05','B_06' => 'B 06','B_07' => 'B 07','B_08' => 'B 08','B_09' => 'B 09','B_10' => 'B 10','B_11' => 'B 11','B_12' => 'B 12','total' => 'Total','is_active' => 'Aktif'];
		$data = get_data('tbl_fact_breakdown_budget')->result_array();
		$config = [
			'title' => 'data_breakdown_budget',
			'data' => $data,
			'header' => $arr,
		];
		$this->load->library('simpleexcel',$config);
		$this->simpleexcel->export();
	}

    function clear_data($tahun="",$cost_centre="") {
		ini_set('memory_limit', '-1');
		ini_set('max_execution_time', 0);

		$tahun = post('tahun') ;
		$cost_centre = post('cost_centre');

        $username = post('username');

		// $user_id   = '06041977';
		$date = date('Y-m-d H:i:s');

        $table = 'tbl_fact_breakdown_budget_' . $tahun;
        $table2 = 'tbl_fact_lstbudget_' . $tahun;
        
        $arr            = [
            'select'    => 'a.cost_centre, b.cost_centre as cost_centre_name',
            'join'      => 'tbl_fact_cost_centre b on a.cost_centre = b.kode',
            'where'     => [
                'a.is_active' => 0,
            ],
            'group_by' => 'a.cost_centre',
            'sort_by' => 'a.cost_centre',
        ];


        $x = "";
        if(!empty($cost_centre) && !empty($username)) {
            $x = "1";
            $arr['where']['a.cost_centre'] = $cost_centre;
            $arr['where']['a.user_id'] = $username;
            $cc1 = get_data($table. ' a',$arr)->result_array();
            delete_data($table,['cost_centre'=>$cost_centre,'user_id'=>$username]);
        }elseif(!empty($cost_centre) && empty($username)) {
            $x = "2";
            $arr['where']['a.cost_centre'] = $cost_centre;
            $cc1 = get_data($table. ' a',$arr)->result_array();
            delete_data($table,'cost_centre=',$cost_centre);
        }elseif(!empty($username) && empty($cost_centre)){
            $x = "3";
            $arr['where']['a.user_id'] = $username;
            $cc1 = get_data($table. ' a',$arr)->result_array();
            delete_data($table,'user_id',$username);
        }else{
            $x = "4";
            $cc1 = get_data($table. ' a',$arr)->result_array();
            delete_data($table,'cost_centre !=',"");
        }



        $cc = [];
        foreach($cc1 as $c => $vc) {
            if(!in_array($vc['cost_centre'],$cc)) $cc[] = $vc['cost_centre'];
        }
                

        if(count($cc)) {
            $sum = get_data($table . ' a',[
                'select' => 'a.cost_centre,a.sub_account, a.account_code, sum(B_01) as B_01, sum(B_02) as B_02, sum(B_03) as B_03, sum(B_04) as B_04, sum(B_05) as B_05, sum(B_06) as B_06, 
                    sum(B_07) as B_07, sum(B_08) as B_08, sum(B_09) as B_09, sum(B_10) as B_10, sum(B_11) as B_11, sum(B_12) as B_12',
                'where' => [
                    'a.cost_centre' => $cc,
                ],
                'group_by' => 'a.cost_centre,a.sub_account,a.account_code'
            ])->result();

            // debug($sum);die;
            foreach($sum as $s) {
                $data_update = [
                    'B_01' => $s->B_01,
                    'B_02' => $s->B_02,
                    'B_03' => $s->B_03,
                    'B_04' => $s->B_04,
                    'B_05' => $s->B_05,
                    'B_06' => $s->B_06,
                    'B_07' => $s->B_07,
                    'B_08' => $s->B_08,
                    'B_09' => $s->B_09,
                    'B_10' => $s->B_10,
                    'B_11' => $s->B_11,
                    'B_12' => $s->B_12,
                    'total_budget' => $s->B_01+$s->B_02+$s->B_03+$s->B_04+$s->B_05+$s->B_06+$s->B_07+$s->B_08+$s->B_09+$s->B_10+$s->B_11+$s->B_12,
                ];
                update_data($table2, $data_update,['cost_centre'=>$s->cost_centre,'sub_account'=>$s->sub_account,'account_code'=>$s->account_code]);
            };
        }

		render([
			'status'	=> 'success',
			'message'	=> 'Data Berhasil di Hapus'
		],'json');	
	}

    function get_user(){
        $cost_centre = post('cost_centre');
        $tahun = post('tahun');
        $table = 'tbl_fact_breakdown_budget_' . $tahun;

        $pic = get_data('tbl_fact_pic_budget','cost_centre',$cost_centre)->row();
        $id_pic = '';
        if(isset($pic->user_id)) {
           $id_pic = json_decode($pic->user_id,true) ;
        } 

        $pic_in = get_data($table,[
            'select' => 'distinct user_id',
            'where'  => [
                'cost_centre' => $cost_centre,
                'user_id' => $id_pic
            ]
        ])->result();
        $id_pic2 = [];
        foreach($pic_in as $p) {
            $id_pic2[] = $p->user_id;
        }

        if(in_array(user('id_group'), [BUDGET_PIC_FACTORY,SCM,OPR,QC,IT,MPD,ENG,SCM,ADMIN]) or user('id_group') == HRD) {
            $where = '';
            if(!empty($id_pic2)) $where = 'id IN (' . implode(',',$id_pic2) . ') OR';

            $res['user'] = get_data('tbl_user', [
                'where' => [
                    '__m' => $where . ' id="' . user('id') . '"',
                ]
            ])->result_array(); 

        }else{
            $res['user'] = get_data('tbl_user', [
                'where' => [
                    'is_active' => 1,
                    'id_group' => [BUDGET_PIC_FACTORY,HRD,ADMIN_UTAMA,SCM,OPR,QC,IT,MPD,ENG,ADMIN]
                ]
            ])->result_array(); 
            // $r2 = [];
            // foreach($r1 as $r) {
            //     $r2[] = $r->id;
            // }
        }

        // debug($res['user']);die;

     
        // if($r){
        //     if(user('id_group') != 1){
        //         $res['user'] = get_data('tbl_user','id',json_decode($r->id,true))->result_array();
        //     }else{
        //         $res['user'] = get_data('tbl_user','id',$r2)->result_array();
        //     }
        // }else{
        //     $res['user'] = [];
        // }
    
        render($res['user'], 'json');
    }
}