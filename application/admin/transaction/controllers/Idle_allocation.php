<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');

class Idle_allocation extends BE_Controller {

	function __construct() {
		parent::__construct();
	}

	function index() {
		$data['tahun'] = get_data('tbl_fact_tahun_budget', 'is_active',1)->result();   
		$data['opt_cc'] = get_data('tbl_fact_cost_centre',[
			'where' => [
				'is_active' => 1,
				'id_fact_department' => 2
			],
			])->result_array();

		render($data);
	}

	function data($tahun="") {
		$config =[];

		if($tahun) {
	    	$config['where']['tahun']	= $tahun;	
	    }

		$data = data_serverside($config);
		render($data,'json');
	}

	function get_data() {
		$data = get_data('tbl_idle_allocation','id',post('id'))->row_array();
		render($data,'json');
	}

	function save() {
		$response = save_data('tbl_idle_allocation',post(),post(':validation'));
		render($response,'json');
	}

	function delete() {
		$response = destroy_data('tbl_idle_allocation','id',post('id'));
		render($response,'json');
	}

	function template() {
		ini_set('memory_limit', '-1');
		$arr = ['tahun' => 'tahun','id_cost_centre' => 'id_cost_centre','cost_center' => 'cost_center','prsn_allocation' => 'prsn_allocation','is_active' => 'is_active'];
		$config[] = [
			'title' => 'template_import_idle_allocation',
			'header' => $arr,
		];
		$this->load->library('simpleexcel',$config);
		$this->simpleexcel->export();
	}

	function import() {
		ini_set('memory_limit', '-1');
		$file = post('fileimport');
		$col = ['tahun','id_cost_centre','cost_center','prsn_allocation','is_active'];
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
					$save = insert_data('tbl_idle_allocation',$data);
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
		$arr = ['tahun' => 'Tahun', 'id_cost_centre' => 'Id Cost Centre','cost_center' => 'Cost Center','prsn_allocation' => '-dPrsn Allocation','is_active' => 'Aktif'];
		$data = get_data('tbl_idle_allocation')->result_array();
		$config = [
			'title' => 'data_idle_allocation',
			'data' => $data,
			'header' => $arr,
		];
		$this->load->library('simpleexcel',$config);
		$this->simpleexcel->export();
	}

}