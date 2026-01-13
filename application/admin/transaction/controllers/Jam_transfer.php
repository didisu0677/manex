<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');

class Jam_transfer extends BE_Controller {

	function __construct() {
		parent::__construct();
	}

	function index() {
		$data['tahun'] = get_data('tbl_fact_tahun_budget', [
            'where' => [
                'is_active' => 1,
                'tahun' => user('tahun_budget')
            ]
        ])->result();   
		render($data);
	}

	function data() {
		$data = data_serverside();
		render($data,'json');
	}

	function get_data() {
		$data = get_data('tbl_jam_transfer_allocation','id',post('id'))->row_array();
		render($data,'json');
	}

	function save() {
		$response = save_data('tbl_jam_transfer_allocation',post(),post(':validation'));
		render($response,'json');
	}

	function delete() {
		$response = destroy_data('tbl_jam_transfer_allocation','id',post('id'));
		render($response,'json');
	}

	function template() {
		ini_set('memory_limit', '-1');
		$arr = ['account_code' => 'account_code','allocation_amount' => 'allocation_amount','cost_centre_asal' => 'cost_centre_asal','cost_centre_tujuan' => 'cost_centre_tujuan','is_active' => 'is_active'];
		$config[] = [
			'title' => 'template_import_jam_transfer',
			'header' => $arr,
		];
		$this->load->library('simpleexcel',$config);
		$this->simpleexcel->export();
	}

	function import() {
		ini_set('memory_limit', '-1');
		$file = post('fileimport');
		$col = ['account_code','allocation_amount','cost_centre_asal','cost_centre_tujuan','is_active'];
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
					$save = insert_data('tbl_jam_transfer_allocation',$data);
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
		$arr = ['account_code' => 'Account Code','allocation_amount' => 'Allocation Amount','cost_centre_asal' => 'Cost Centre Asal','cost_centre_tujuan' => 'Cost Centre Tujuan','is_active' => 'Aktif'];
		$data = get_data('tbl_jam_transfer_allocation')->result_array();
		$config = [
			'title' => 'data_jam_transfer',
			'data' => $data,
			'header' => $arr,
		];
		$this->load->library('simpleexcel',$config);
		$this->simpleexcel->export();
	}

}