<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');

class Actual_manex extends BE_Controller {
    var $controller = 'actual_manex';
    function __construct() {
        parent::__construct();
    }
    
    function index() {

        $data['tahun'] = get_data('tbl_fact_tahun_budget', [
            'where' => [
                'is_active' => 1,
                // 'tahun' => user('tahun_budget')
            ],
            ])->result();   
        $data['cc'] = get_data('tbl_fact_cost_centre', 'is_active',1)->result(); 

        $access         = get_access($this->controller);
        $data['access_additional']  = $access['access_additional'];
        render($data);
    }
    
    function sortable() {
        render();
    }

    function data($tahun="",$cost_centre="",$tipe = 'table') {


        $where = [];
        $where1 = [];
        // if($tahun) $where['b.tahun'] = $tahun;
        if($cost_centre) $where['a.cost_centre'] = $cost_centre;
        
   
        if(!empty($tahun) && !empty($cost_centre)) {
            $table = 'tbl_fact_actual_budget_' . $tahun ;
            $cek1 = get_data('tbl_fact_account_cc a',[
                'select' => 'a.id_account, a.cost_centre,a.sub_account,a.account_code,a.account_name,b.id as id_trx, c.id as id_cost_centre',
                'join'   => [$table.' b on a.account_code = b.account_code and a.cost_centre = b.cost_centre  type LEFT', 
                            'tbl_fact_cost_centre c on a.cost_centre = c.kode type LEFT',    
                            'tbl_fact_account d on a.id_account = d.id type LEFT'  
                            ],
                'where' => $where + $where1,
                'sort_by' => 'd.urutan',
            ])->result();

  
            $cek2 = get_data('tbl_fact_account_cc a',[
                'select' => 'a.id_account,a.cost_centre,a.sub_account,a.account_code,a.account_name,b.id as id_trx, c.id as id_cost_centre',
                'join'   => [$table.' b on a.account_code = b.account_code and a.cost_centre = b.cost_centre  type LEFT', 
                            'tbl_fact_cost_centre c on a.cost_centre = c.kode type LEFT',      
                            'tbl_fact_account d on a.id_account = d.id type LEFT'
                            ],
                'where' => $where,
                'sort_by' => 'd.urutan', 
            ])->result();

            
            if(count($cek1)) {
                $id_trx = 0;
                foreach($cek1 as $c) {
                    if(!empty($c->id_trx)) $id_trx = $c->id_trx ;
                    $data['id'] = $data['id'] = $id_trx;
                    $data['tahun'] = $tahun;
                    $data['id_cost_centre'] = $c->id_cost_centre;
                    $data['cost_centre'] = $c->cost_centre;
                    $data['account_code'] = $c->account_code;
                    $data['account_name'] = $c->account_name;
                    save_data($table,$data);
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

                    save_data($table,$data2);
                }
            }
        }
        


        $data['mst_account'][0] = get_data('tbl_fact_account a',[
            'select' => 'a.*,b.id as id_trx, b.total_actual',
            'join' => $table . ' b on a.account_code = b.account_code and b.cost_centre ="'.$cost_centre.'"  type LEFT',
            'where'=> [
                'a.parent_id'=>0
            ],
            'sort_by'=>'a.urutan',
            ])->result();
        foreach($data['mst_account'][0] as $m0) {
            $data['mst_account'][$m0->id] = get_data('tbl_fact_account a',[
                'select' => 'a.*, b.id as id_trx, b.total_actual',
                'join' => $table . ' b on a.account_code = b.account_code and b.cost_centre ="'.$cost_centre.'"  type LEFT',    
                'where'=>[
                    'a.parent_id'=>$m0->id
                ],
                'sort_by'=>'a.urutan'
                ])->result();
            foreach($data['mst_account'][$m0->id] as $m1) {
                $data['mst_account'][$m1->id] = get_data('tbl_fact_account a',[
                    'select' => 'a.*, b.id as id_trx, b.total_actual',
                    'join' => $table . ' b on a.account_code = b.account_code and b.cost_centre ="'.$cost_centre.'"  type LEFT',        
                    'where'=>[
                        'a.parent_id'=>$m1->id
                    ],
                    'sort_by'=>'a.urutan'
                    ])->result();
                foreach($data['mst_account'][$m1->id] as $m2) {
                    $data['mst_account'][$m2->id] = get_data('tbl_fact_account a',[
                        'select' => 'a.*, b.id as id_trx, b.total_actual',
                        'join' => $table . ' b on a.account_code = b.account_code and b.cost_centre ="'.$cost_centre.'" type LEFT',            
                        'where'=>[
                            'a.parent_id'=>$m2->id
                        ],'sort_by'=>'a.urutan'
                    ])->result();
                }
            }
        }

        $response	= array(
            'table'		=> $this->load->view('transaction/actual_manex/table',$data,true),
        );
	   
	    render($response,'json');
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

        $tahun = post('tahun');
        $table = 'tbl_fact_actual_budget_' . $tahun;
        $data   = json_decode(post('json'),true);

        foreach($data as $id => $record) {
            $result = $record;
            foreach ($result as $r => $v) {               
                update_data($table, $result,'id',$id);
            }        
        }
    }

    function import() {
		ini_set('memory_limit', '-1');

        $table = 'tbl_fact_actual_budget_' .post('tahun');
        $cost_centre = post('cost_centre');
        $sub_account = post('sub_account');
        $id_user = post('username');
        $product_code = post('product');

        // debug(post());die;

        $acc = get_data('tbl_fact_account_cc a',[
            'select' => 'b.id as id_account, a.account_code',
            'join' => 'tbl_fact_account b on a.account_code = b.account_code',
            'where' => [
                'a.cost_centre' => $cost_centre,
            ]
        ])->result();

         
        $acc1 = [];
         foreach($acc as $a) {
            $cek = get_data('tbl_fact_account','parent_id',$a->id_account)->row();
            if(!isset($cek->id)) $acc1[] = $a->account_code;

        }

   
		$file = post('fileimport');
		$col = ['Account','Code', 'actual'];

		$this->load->library('simpleexcel');
		$this->simpleexcel->define_column($col);
		$jml = $this->simpleexcel->read($file);

		$c = 0;
        $save = 0;
		foreach($jml as $i => $k) {
			if($i==0) {
				for($j = 8; $j <= $k; $j++) {
					$data = $this->simpleexcel->parsing($i,$j);
                    if($data['Code'] !='' && $data['actual'] != 0) {
                        $data2['total_actual'] = (isset($data['actual']) ? str_replace(['.',','],'',$data['actual']) : 0);
                        $data2['create_at'] = date('Y-m-d H:i:s');
                        $data2['create_by'] = user('nama');
                        if(in_array($data['Code'],$acc1))
                        $save = update_data($table,$data2,['account_code'=>$data['Code'],'cost_centre'=>$cost_centre]);					
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
}

