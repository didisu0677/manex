<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');

class Kapasitas_produksi extends BE_Controller {

	function __construct() {
		parent::__construct();
	}

	function index() {
		$data['opt_cc'] = get_data('tbl_fact_cost_centre',[
			'where' => [
				'is_active' => 1,
				'id_fact_department' => 2
			],
			])->result_array();
		render($data);
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
		$data = post();
		$cc = get_data('tbl_fact_cost_centre','id',$data['id_factory'])->row();
		if(!empty($cc)) {
			$data['factory'] = $cc->cost_centre ;
			$data['cost_centre'] = $cc->kode ;
		}
		$response = save_data('tbl_kapasitas_produksi',$data,post(':validation'));
		render($response,'json');
	}

	function delete() {
		$response = destroy_data('tbl_kapasitas_produksi','id',post('id'));
		render($response,'json');
	}

	function template() {
		ini_set('memory_limit', '-1');
		$arr = ['cost_centre' => 'cost_centre','kapasitas' => 'kapasitas','factory' => 'factory','WD_01' => 'WD_01','WD_02' => 'WD_02','WD_03' => 'WD_03','WD_04' => 'WD_04','WD_05' => 'WD_05','WD_06' => 'WD_06','WD_07' => 'WD_07','WD_08' => 'WD_08','WD_09' => 'WD_09','WD_10' => 'WD_10','WD_11' => 'WD_11','WD_12' => 'WD_12','is_active' => 'is_active'];
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
		$col = ['cost_centre','kapasitas','factory','WD_01','WD_02','WD_03','WD_04','WD_05','WD_06','WD_07','WD_08','WD_09','WD_10','WD_11','WD_12','is_active'];
		$this->load->library('simpleexcel');
		$this->simpleexcel->define_column($col);
		$jml = $this->simpleexcel->read($file);
		$c = 0;
		foreach($jml as $i => $k) {
			if($i==0) {
				for($j = 2; $j <= $k; $j++) {
					$data = $this->simpleexcel->parsing($i,$j);

					$id_fact = get_data('tbl_fact_cost_centre','kode',$data['cost_centre'])->row();
					if(!empty($id_fact)) $data['id_factory'] = $id_fact->id ;
					
					$data['kapasitas'] = str_replace(['.',','],'',$data['kapasitas']) ;
					$data['WD_01'] = str_replace(['.',','],'',$data['WD_01']) ;
					$data['WD_02'] = str_replace(['.',','],'',$data['WD_02']) ;
					$data['WD_03'] = str_replace(['.',','],'',$data['WD_03']) ;
					$data['WD_04'] = str_replace(['.',','],'',$data['WD_04']) ;
					$data['WD_05'] = str_replace(['.',','],'',$data['WD_05']) ;
					$data['WD_06'] = str_replace(['.',','],'',$data['WD_06']) ;
					$data['WD_07'] = str_replace(['.',','],'',$data['WD_07']) ;
					$data['WD_08'] = str_replace(['.',','],'',$data['WD_08']) ;
					$data['WD_09'] = str_replace(['.',','],'',$data['WD_09']) ;
					$data['WD_10'] = str_replace(['.',','],'',$data['WD_10']) ;
					$data['WD_11'] = str_replace(['.',','],'',$data['WD_11']) ;
					$data['WD_12'] = str_replace(['.',','],'',$data['WD_12']) ;

					$cek = get_data('tbl_kapasitas_produksi', 'cost_centre', $data['cost_centre'])->row();
					if(!isset($cek->id)) {
						$data['create_at'] = date('Y-m-d H:i:s');
						$data['create_by'] = user('nama');
						$save = insert_data('tbl_kapasitas_produksi',$data);
					}else{
						$data['update_at'] = date('Y-m-d H:i:s');
						$data['update_by'] = user('nama');
						$save = update_data('tbl_kapasitas_produksi',$data,['id'=>$cek->id]);
					}
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
		$arr = ['cost_centre' => 'Cost Centre','kapasitas' => 'Kapasitas','factory' => 'Factory','WD_01' => 'WD 01','WD_02' => 'WD 02','WD_03' => 'WD 03','WD_04' => 'WD 04','WD_05' => 'WD 05','WD_06' => 'WD 06','WD_07' => 'WD 07','WD_08' => 'WD 08','WD_09' => 'WD 09','WD_10' => 'WD 10','WD_11' => 'WD 11','WD_12' => 'WD 12','is_active' => 'Aktif'];
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