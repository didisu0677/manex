<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');

class Material_supplier extends BE_Controller {

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
		$data = get_data('tbl_material_supplier','id',post('id'))->row_array();
		render($data,'json');
	}

	function save() {
		$response = save_data('tbl_material_supplier',post(),post(':validation'));
		if($response['status'] == 'success') {
			$data = post();
			$data['create_at'] = date('Y-m-d H:i:s');
			$data['create_by'] = user('nama');

			$cek = get_data('tbl_m_supplier','code',$data['kode_supplier'])->row_array();
			$master = [
				'code' => $data['kode_supplier'],
				'nama' => $data['nama_supplier'],
				'is_active' => 1,
				'create_at' => date('Y-m-d H:i:s'),
				'create_by' => user('nama'),
			];

			if(!isset($cek['code'])) {
				insert_data('tbl_m_supplier',$master);
			}else{
				update_data('tbl_m_supplier',$master,[
					'code' => $data['kode_supplier']
				]);
			}
		}
		render($response,'json');
	}

	function delete() {
		$response = destroy_data('tbl_material_supplier','id',post('id'));
		render($response,'json');
	}

	function template() {
		ini_set('memory_limit', '-1');
		$arr = ['kode_supplier' => 'kode_supplier','nama_supplier' => 'nama_supplier','material_code' => 'material_code','material_name' => 'material_name','um' => 'um','moq' => 'moq','order_multiple' => 'order_multiple','m_cov' => 'm_cov','total_stock' => 'total_stock','is_active' => 'is_active'];
		$config[] = [
			'title' => 'template_import_material_supplier',
			'header' => $arr,
		];
		$this->load->library('simpleexcel',$config);
		$this->simpleexcel->export();
	}

	function import() {
		ini_set('memory_limit', '-1');
		$file = post('fileimport');
		$col = ['kode_supplier','nama_supplier','material_code','material_name','um','moq','order_multiple','m_cov','total_stock','is_active'];
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

					$cek = get_data('tbl_material_supplier',[
						'where' => [
							'kode_supplier' => $data['kode_supplier'],
							'material_code' => $data['material_code']
						],
					])->row_array();
					if(!isset($cek['kode_supplier'])) {
						$save = insert_data('tbl_material_supplier',$data);
					}else{
						$save = update_data('tbl_material_supplier',$data,[
							'kode_supplier' => $data['kode_supplier'],
							'material_code' => $data['material_code']
						]);
					}
					if($save) $c++;

					$cek_master = get_data('tbl_m_supplier','code',$data['kode_supplier'])->row_array();
					$master = [
						'code' => $data['kode_supplier'],
						'nama' => $data['nama_supplier'],
						'is_active' => 1,
						'create_at' => date('Y-m-d H:i:s'),
						'create_by' => user('nama'),
					];

					if(!isset($cek_master['code'])) {
						insert_data('tbl_m_supplier',$master);
					}else{
						update_data('tbl_m_supplier',$master,[
							'code' => $data['kode_supplier']
						]);
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

	function export() {
		ini_set('memory_limit', '-1');
		$arr = ['kode_supplier' => 'Kode Supplier','nama_supplier' => 'Nama Supplier','material_code' => 'Material Code','material_name' => 'Material Name','um' => 'Um','moq' => 'Moq','order_multiple' => 'Order Multiple','m_cov' => 'M Cov','total_stock' => 'Total Stock','is_active' => 'Aktif'];
		$data = get_data('tbl_material_supplier')->result_array();
		$config = [
			'title' => 'data_material_supplier',
			'data' => $data,
			'header' => $arr,
		];
		$this->load->library('simpleexcel',$config);
		$this->simpleexcel->export();
	}

}