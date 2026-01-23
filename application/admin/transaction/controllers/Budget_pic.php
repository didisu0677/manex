<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');

class Budget_pic extends BE_Controller {
    var $controller = 'budget_pic';
    function __construct() {
        parent::__construct();
    }
    
    function index() {

            
        $data['factory'] = get_data('tbl_fact_cost_centre',[
            'where' => [
                'is_active' => 1,
                // 'id_fact_department' => 2,
            ],
        ])->result();
        $data['tahun'] = get_data('tbl_fact_tahun_budget', 'is_active',1)->result();   
        
        // debug($data['tahun']) ;die;


        $access         = get_access($this->controller);
        $data['access'] = $access ;
        $data['access_additional']  = $access['access_additional'];
        render($data);
    }
    
    function sortable() {
        render();
    }

    function data($tahun = "", $tipe = 'table') {
        $arr            = [
	        'select'	=> '*',
	        'where'     => [
	            'a.is_active' => 1,
                // 'a.id' => 4
	        ],
	    ];


        // $tahun = get('tahun');


	    $data['grup'][0]= get_data('tbl_fact_group_department a',$arr)->result();
        $data['user'] = get_data('tbl_user','is_active',1)->result_array(); 

        // debug($data['grup'][0]);die;


        foreach($data['grup'][0] as $m0) {	
            $data['cc1'][$m0->id]= get_data('tbl_fact_cost_centre a',[
                'where' => [
                    'a.is_active' => 1,
                    'a.id_group_department' => $m0->id,
                ],
                'sort_by' => 'a.id_group_department'
            ])->result();

            foreach($data['cc1'][$m0->id] as $c) {
                $cek = get_data('tbl_fact_pic_budget',[
                    'where' => [
                        'id_cost_centre' => $c->id,
                        'cost_centre' => $c->kode,
                        'tahun' => $tahun,
                    ],
                ])->row();
                
                if(!isset($cek->id)) {
                    $data_insert = [
                        'tahun' => $tahun,
                        'id_cost_centre' => $c->id,
                        'cost_centre' => $c->kode,
                        'is_active' => 1
                    ];
                    insert_data('tbl_fact_pic_budget',$data_insert); 
                }else{
                    update_data('tbl_fact_pic_budget',['id_cost_centre'=>$c->id,'cost_centre' => $c->kode,'is_active'=>1],['id'=>$cek->id]);
                }
            }

            $data['cc'][$m0->id]= get_data('tbl_fact_pic_budget a',[
                'select' => 'a.id,a.user_level,a.user_id,b.kode,b.cost_centre,b.id_group_department,b.id_fact_department,b.abbreviation',
                'join' => 'tbl_fact_cost_centre b on a.id_cost_centre = b.id type LEFT',
                'where' => [
                    'a.is_active' => 1,
                    'b.id_group_department' => $m0->id,
                    'a.tahun' => $tahun
                ],
                'sort_by' => 'b.id_group_department, b.cost_centre'
            ])->result();

        }
       
    //    debug($data['cc']);die;
        $response	= array(
            'table'		=> $this->load->view('transaction/budget_pic/table',$data,true),
        );



	   
	    render($response,'json');
	}



    function save_perubahan() {       

        $data = post();

        // debug($data);die;
        $div = post('div');
        $dep = post('dep');
        $sec = post('sec');
        
        $user_pic = post('user_pic');

        if(is_array($user_pic)) {
            foreach($user_pic as $p => $v){
                // debug(json_encode($v,true));die;
                update_data('tbl_fact_pic_budget',
                    ['user_id' => json_encode($v,true)],
                    ['id'=>$p]
                );
            }
        }

        if(is_array($sec)) {
            foreach($sec as $p => $s) {
                update_data('tbl_fact_pic_budget',
                    ['user_level' => 3],
                    ['id'=>$p]
                );
            }
        }

        if(is_array($dep)) {
            foreach($dep as $p => $s) {
                update_data('tbl_fact_pic_budget',
                    ['user_level' => 2],
                    ['id'=>$p]
                );
            }
        }

        if(is_array($div)) {
            foreach($div as $p => $s) {
                update_data('tbl_fact_pic_budget',
                    ['user_level' => 1],
                    ['id'=>$p]
                );
            }
         }


        render([
            'status'	=> 'success',
            'message'	=> lang('data_berhasil_disimpan')
        ],'json');
    }

}

