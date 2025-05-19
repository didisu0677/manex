<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');

class Group_material extends BE_Controller {

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
		$data = get_data('tbl_group_material','id',post('id'))->row_array();
		render($data,'json');
	}

	function save() {
		$response = save_data('tbl_group_material',post(),post(':validation'));
		render($response,'json');
	}

	function delete() {
		$response = destroy_data('tbl_group_material','id',post('id'));
		render($response,'json');
	}

	function template() {
		ini_set('memory_limit', '-1');
		$arr = ['pc' => 'pc','pc_um' => 'pc_um','mc' => 'mc','mc_um' => 'mc_um','qty' => 'qty','scr' => 'scr','group' => 'group','is_active' => 'is_active'];
		$config[] = [
			'title' => 'template_import_group_material',
			'header' => $arr,
		];
		$this->load->library('simpleexcel',$config);
		$this->simpleexcel->export();
	}

	function import() {
		ini_set('memory_limit', '-1');
		$file = post('fileimport');
		$col = ['pc','pc_um','mc','mc_um','qty','scr','group','is_active'];
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
					$save = insert_data('tbl_group_material',$data);
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
		$arr = ['pc' => 'Pc','pc_um' => 'Pc Um','mc' => 'Mc','mc_um' => 'Mc Um','qty' => 'Qty','scr' => 'Scr','group' => 'Group','is_active' => 'Aktif'];
		$data = get_data('tbl_group_material')->result_array();
		$config = [
			'title' => 'data_group_material',
			'data' => $data,
			'header' => $arr,
		];
		$this->load->library('simpleexcel',$config);
		$this->simpleexcel->export();
	}

}