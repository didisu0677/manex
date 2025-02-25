<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');

class Kapasitas_produksi extends BE_Controller {

	function __construct() {
		parent::__construct();
	}

	function index() {
		render();
	}

	function data() {
		$data = data_serverside();
		render($data,'json');
	}

	function get_data() {
		$data = get_data('tbl_kapasitas_produksi','id',post('id'))->row_array();
		render($data,'json');
	}

	function save() {
		$response = save_data('tbl_kapasitas_produksi',post(),post(':validation'));
		render($response,'json');
	}

	function delete() {
		$response = destroy_data('tbl_kapasitas_produksi','id',post('id'));
		render($response,'json');
	}

	function template() {
		ini_set('memory_limit', '-1');
		$arr = ['id_factory' => 'id_factory','cost_centre' => 'cost_centre','kapasitas' => 'kapasitas','factory' => 'factory','WD_01' => 'WD_01','WD_02' => 'WD_02','WD_03' => 'WD_03','WD_04' => 'WD_04','WD_05' => 'WD_05','WD_06' => 'WD_06','WD_07' => 'WD_07','WD_08' => 'WD_08','WD_09' => 'WD_09','WD_10' => 'WD_10','WD_11' => 'WD_11','WD_12' => 'WD_12','is_active' => 'is_active'];
		$config[] = [
			'title' => 'template_import_kapasitas_produksi',
			'header' => $arr,
		];
		$this->load->library('simpleexcel',$config);
		$this->simpleexcel->export();
	}

	function import() {
		ini_set('memory_limit', '-1');
		$file = post('fileimport');
		$col = ['id_factory','cost_centre','kapasitas','factory','WD_01','WD_02','WD_03','WD_04','WD_05','WD_06','WD_07','WD_08','WD_09','WD_10','WD_11','WD_12','is_active'];
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
					$save = insert_data('tbl_kapasitas_produksi',$data);
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
		$arr = ['id_factory' => 'Id Factory','cost_centre' => 'Cost Centre','kapasitas' => 'Kapasitas','factory' => 'Factory','WD_01' => 'WD 01','WD_02' => 'WD 02','WD_03' => 'WD 03','WD_04' => 'WD 04','WD_05' => 'WD 05','WD_06' => 'WD 06','WD_07' => 'WD 07','WD_08' => 'WD 08','WD_09' => 'WD 09','WD_10' => 'WD 10','WD_11' => 'WD 11','WD_12' => 'WD 12','is_active' => 'Aktif'];
		$data = get_data('tbl_kapasitas_produksi')->result_array();
		$config = [
			'title' => 'data_kapasitas_produksi',
			'data' => $data,
			'header' => $arr,
		];
		$this->load->library('simpleexcel',$config);
		$this->simpleexcel->export();
	}

}