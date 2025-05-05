<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');

class Tahun_budget extends BE_Controller {

	function __construct() {
		parent::__construct();
	}

	function index() {
		// $data['menu'][0] = get_data('tbl_menu',array('where_array'=>array('parent_id'=>0,'id'=>[49,288]),'sort_by'=>'urutan'))->result();
		// foreach($data['menu'][0] as $m0) {
		// 	$data['menu'][$m0->id] = get_data('tbl_menu',array('where_array'=>array('parent_id'=>$m0->id),'sort_by'=>'urutan'))->result();
		// 	foreach($data['menu'][$m0->id] as $m1) {
		// 		$data['menu'][$m1->id] = get_data('tbl_menu',array('where_array'=>array('parent_id'=>$m1->id),'sort_by'=>'urutan'))->result();
		// 		foreach($data['menu'][$m1->id] as $m2) {
		// 			$data['menu'][$m2->id] = get_data('tbl_menu',array('where_array'=>array('parent_id'=>$m2->id),'sort_by'=>'urutan'))->result();
		// 		}
		// 	}
		// }

		render();
	}

	function data() {
		$config['access_view'] = false;

		$config['button'][]	= button_serverside('btn-success','btn-lock',['fa-unlock',lang('manex_lock'),true],'act-lock',['is_lock' => 0]);
		$config['button'][]	= button_serverside('btn-danger','btn-unlock',['fa-lock',lang('manex_unlock'),true],'act-lock',['is_lock' => 1]);

		$config['button'][]	= button_serverside('btn-success','btn-sales-lock',['fa fa-key',lang('sales_lock'),true],'act-lock',['sales_lock' => 0]);
		$config['button'][]	= button_serverside('btn-danger','btn-sales-unlock',['fab fa-keycdn',lang('sales_unlock'),true],'act-lock',['sales_lock' => 1]);


		$data = data_serverside($config);
		
		render($data,'json');
	}

	function get_data() {
		$data = get_data('tbl_fact_tahun_budget','id',post('id'))->row_array();
		render($data,'json');
	}

	function save() {

		$data = post();
		$data['is_active'] = 1;

		$response = save_data('tbl_fact_tahun_budget',$data,post(':validation'));

		// if($response['status'] = 'success') {
		// 	$this->copy_budget(post('tahun'));
		// }

		render($response,'json');
	}

	function copy_budget($tahun){
		
		$current_tahun = date('Y');
		die;

		$current_user_acc = get_data($this->tbl_user_act_account, [
			'where_array' => ['tahun_budget' => $current_tahun]
		])->result_array();
		$current_user_pra = get_data($this->tbl_user_prnalokasi, [
			'where_array' => ['tahun_budget' => $current_tahun]
		])->result_array();
		$current_user_atr = get_data($this->tbl_user_atribute, [
			'where_array' => ['tahun_budget' => $current_tahun]
		])->result_array();
		
		delete_data($this->tbl_user_act_account, 'tahun_budget', $tahun);
		delete_data($this->tbl_user_prnalokasi, 'tahun_budget', $tahun);
		delete_data($this->tbl_user_atribute, 'tahun_budget', $tahun);

		foreach($current_user_acc as $v){
			$v['tahun_budget'] = $tahun;
			insert_data($this->tbl_user_act_account,$v);
		}

		foreach($current_user_pra as $v){
			$v['tahun_budget'] = $tahun;
			insert_data($this->tbl_user_prnalokasi,$v);
		}
		
		foreach($current_user_atr as $v){
			unset($v['id']);
			$v['tahun_budget'] = $tahun;
			insert_data($this->tbl_user_atribute,$v);
		}



	}

	function create_tbl_trx_budget($tahun){

		if(!table_exists('tbl_budget_byprod_'.$tahun))
		$this->db->query('CREATE TABLE IF NOT EXISTS tbl_budget_byprod_'.$tahun.' (
			id int(9) NOT NULL primary key auto_increment,
			gl_account varchar(6) NOT NULL,
			sub_account varchar(4) DEFAULT NULL,
			kode_proses varchar(25) DEFAULT NULL,
			prsn_alokasi double(9,2) NOT NULL,
			bisunit varchar(10) NOT NULL,
			B_01 double(14,2) NOT NULL,
			B_02 double(14,2) NOT NULL,
			B_03 double(14,2) NOT NULL,
			B_04 double(14,2) NOT NULL,
			B_05 double(14,2) NOT NULL,
			B_06 double(14,2) NOT NULL,
			B_07 double(14,2) NOT NULL,
			B_08 double(14,2) NOT NULL,
			B_09 double(14,2) NOT NULL,
			B_10 double(14,2) NOT NULL,
			B_11 double(14,2) NOT NULL,
			B_12 double(14,2) NOT NULL,
			total_budget double(14,2) NOT NULL,
			last_update datetime DEFAULT NULL,
			imported_id int(11) DEFAULT NULL,
			imported_name varchar(35) DEFAULT NULL
		  ) ENGINE=MyISAM;');

		  if(!table_exists('tbl_lstbudget_'.$tahun))
		  $this->db->query('CREATE TABLE IF NOT EXISTS tbl_lstbudget_'.$tahun.' (
			id int(11) NOT NULL primary key auto_increment,
			username varchar(100) NOT NULL,
			gl_account varchar(6) DEFAULT NULL,
			gl_description varchar(50) DEFAULT NULL,
			entity varchar(10) DEFAULT NULL,
			cost_center varchar(4) DEFAULT NULL,
			id_alokasi int(4) NOT NULL,
			sub_account varchar(4) NOT NULL,
			bisunit varchar(30) NOT NULL,
			parent_id int(14) NOT NULL,
			prsn_alokasi double(9,2) NOT NULL,
			ytd_cur bigint(20) NOT NULL,
			estimate bigint(20) NOT NULL,
			B_01 bigint(20) NOT NULL,
			B_02 bigint(20) NOT NULL,
			B_03 bigint(20) NOT NULL,
			B_04 bigint(20) NOT NULL,
			B_05 bigint(20) NOT NULL,
			B_06 bigint(20) NOT NULL,
			B_07 bigint(20) NOT NULL,
			B_08 bigint(20) NOT NULL,
			B_09 bigint(20) NOT NULL,
			B_10 bigint(20) NOT NULL,
			B_11 bigint(20) NOT NULL,
			B_12 bigint(20) NOT NULL,
			total_budget bigint(20) NOT NULL,
			urutan int(10) NOT NULL,
			status int(10) NOT NULL,
			status_alokasi int(1) NOT NULL DEFAULT 0
		  ) ENGINE=MyISAM;');

		  if(!table_exists('tbl_trial_balance_'.($tahun-1)))
		  $this->db->query('CREATE TABLE IF NOT EXISTS tbl_trial_balance_'.($tahun-1).' (
			id int(11) NOT NULL primary key auto_increment,
			`gl_account` varchar(6) NOT NULL,
			`cost_center` varchar(4) NOT NULL,
			`cost_center_name` varchar(100) NOT NULL,
			`sub_account` varchar(4) NOT NULL,
			`sub_account_name` varchar(100) NOT NULL,
			`bisunit` varchar(15) NOT NULL,
			`end_balance` double(14,2) NOT NULL,
			`imported_id` int(4) NOT NULL,
			`import_name` varchar(50) NOT NULL,
			`periode` varchar(50) NOT NULL
		  ) ENGINE=MyISAM;');

		  $budget_by_prod = get_data('tbl_trx_lock', [
			'where_array' => [
				'tabel' => 'tbl_budget_byprod_'.$tahun,
				'tahun' => $tahun
			]
		  ])->row_array();
		  if(!$budget_by_prod) insert_data('tbl_trx_lock', [
			'tahun' => $tahun,
			'tabel' => 'tbl_budget_byprod_'.$tahun,
			'description' => 'Summary Budget',
			'lock' => 0,
			'status' => 1
		  ]);

		  $lstbudget = get_data('tbl_trx_lock', [
			'where_array' => [
				'tabel' => 'tbl_lstbudget_'.$tahun,
				'tahun' => $tahun
			]
		  ])->row_array();
		  if(!$lstbudget) insert_data('tbl_trx_lock', [
			'tahun' => $tahun,
			'tabel' => 'tbl_lstbudget_'.$tahun,
			'description' => 'List Budget',
			'lock' => 0,
			'status' => 1
		  ]);
	}

	function delete() {
		$response = destroy_data('tbl_fact_tahun_budget','id',post('id'));
		render($response,'json');
	}

	function template() {
		ini_set('memory_limit', '-1');
		$arr = ['tahun' => 'tahun','description' => 'description','is_active' => 'is_active'];
		$config[] = [
			'title' => 'template_import_tahun_budget',
			'header' => $arr,
		];
		$this->load->library('simpleexcel',$config);
		$this->simpleexcel->export();
	}

	function import() {
		ini_set('memory_limit', '-1');
		$file = post('fileimport');
		$col = ['tahun','description','is_active'];
		$this->load->library('simpleexcel');
		$this->simpleexcel->define_column($col);
		$jml = $this->simpleexcel->read($file);
		$c = 0;
		foreach($jml as $i => $k) {
			if($i==0) {
				for($j = 2; $j <= $k; $j++) {
					$data = $this->simpleexcel->parsing($i,$j);
					$data['create_at'] = date('Y-m-d H:i:s');
					$data['create_by'] = user('nama');
					$save = insert_data('tbl_fact_tahun_budget',$data);
					if($save) $c++;
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

	function export() {
		ini_set('memory_limit', '-1');
		$arr = ['tahun' => 'Tahun','description' => 'Description','is_active' => 'Aktif'];
		$data = get_data('tbl_fact_tahun_budget')->result_array();
		$config = [
			'title' => 'data_tahun_budget',
			'data' => $data,
			'header' => $arr,
		];
		$this->load->library('simpleexcel',$config);
		$this->simpleexcel->export();
	}

	function unlock() {

		if(post('id_lock')) {
			$data = [
				'id' => post('id_lock'),
				'is_lock'	=> 1
			];
		}

		if(post('id_sales_lock')) {
			$data = [
				'id' => post('id_sales_lock'),
				'sales_lock'	=> 1
			];
		}

		$res = save_data('tbl_fact_tahun_budget',$data);
		render($res,'json');
	}

	function lock() {
		
		if(post('id_unlock')) {
			$data = [
				'id' => post('id_unlock'),
				'is_lock'	=> 0
			];
		}

		if(post('id_sales_unlock')) {
			$data = [
				'id' => post('id_sales_unlock'),
				'sales_lock'	=> 0
			];
		}

		$res = save_data('tbl_fact_tahun_budget',$data);
		render($res,'json');
	}

}