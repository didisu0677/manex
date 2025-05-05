<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');

class Material_price extends BE_Controller {

	function __construct() {
		parent::__construct();
	}

	function index() {
		$data['tahun'] = get_data('tbl_fact_tahun_budget', 'is_active',1)->result();   
		if(user('id_group') == 1) {
			$data['user_id'] = get_data('tbl_user','is_active',1)->result_array();
		}else{
			$data['user_id'] = get_data('tbl_user',[
				'where' => [
					'is_active' => 1,
					'id'	=> user('id'),
				],
			])->result_array();
		}
		render($data);
	}

	function data() {
		$data = data_serverside();
		render($data,'json');
	}

	function get_data() {
		$data = get_data('tbl_material_price','id',post('id'))->row_array();
		render($data,'json');
	}

	function save() {
		$response = save_data('tbl_material_price',post(),post(':validation'));
		render($response,'json');
	}

	function delete() {
		$response = destroy_data('tbl_material_price','id',post('id'));
		render($response,'json');
	}

	function template() {
		ini_set('memory_limit', '-1');
		$arr = ['year' => 'year','material_code' => 'material_code','kode_budget' => 'kode_budget','nama' => 'nama','vcode' => 'vcode','loc' => 'loc','bm' => 'bm','curr' => 'curr','price_us' => 'price_us','user_id' => 'user_id','is_active' => 'is_active'];
		$config[] = [
			'title' => 'template_import_material_price',
			'header' => $arr,
		];
		$this->load->library('simpleexcel',$config);
		$this->simpleexcel->export();
	}

	function import() {
		ini_set('memory_limit', '-1');
		$file = post('fileimport');
		$col = ['year','material_code','kode_budget','nama','vcode','loc','bm','curr','price_us','user_id','is_active'];
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
					$save = insert_data('tbl_material_price',$data);
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
		$arr = ['year' => 'Year','material_code' => 'Material Code','kode_budget' => 'Kode Budget','nama' => 'Nama','vcode' => 'Vcode','loc' => 'Loc','bm' => 'Bm','curr' => 'Curr','price_us' => 'Price Us','user_id' => 'User Id','is_active' => 'Aktif'];
		$data = get_data('tbl_material_price')->result_array();
		$config = [
			'title' => 'data_material_price',
			'data' => $data,
			'header' => $arr,
		];
		$this->load->library('simpleexcel',$config);
		$this->simpleexcel->export();
	}

}