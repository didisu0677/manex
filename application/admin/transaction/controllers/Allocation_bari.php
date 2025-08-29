<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');

class Allocation_bari extends BE_Controller {
	var $controller = 'Allocation_bari';
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

		$access         = get_access($this->controller);
        $data['access'] = $access ;

		render($data);
	}

	function data($tahun="") {
		$config = [];

		if($tahun) {
	    	$config['where']['tahun']	= $tahun;	
	    }

		$data = data_serverside($config);
		render($data,'json');
	}

	function get_data() {
		$data = get_data('tbl_allocation_bari','id',post('id'))->row_array();
		render($data,'json');
	}

	function save() {
		$response = save_data('tbl_allocation_bari',post(),post(':validation'));
		render($response,'json');
	}

	function proses(){
		ini_set('memory_limit', '-1');
		ini_set('max_execution_time', 0);

		$tahun = post('tahun');

		$bari = get_data('tbl_allocation_bari a',[
			'select' => 'a.product_code, a.prsn_alloc, b.bottle as before_bari, b.bottle * (a.prsn_alloc / 100) as bari, b.bottle - (b.bottle * (a.prsn_alloc / 100)) as after_bari',
				'join' =>  'tbl_unit_material_cost b on a.tahun = b.tahun and a.product_code = b.product_code',
			'where' => [
				'a.tahun' => $tahun,
				],
		])->result();
		
		foreach($bari as $b) {
			update_data('tbl_allocation_bari',[
				'before_bari' => $b->before_bari,
				'bari' => $b->bari,
				'after_bari' => $b->after_bari
			],['tahun' => $tahun, 'product_code' => $b->product_code]);
		}


		render([
			'status'	=> 'success',
			'message'	=> 'Proses Allocation Bari berhasil dilakukan'
		],'json');	
	}

	function delete() {
		$response = destroy_data('tbl_allocation_bari','id',post('id'));
		render($response,'json');
	}

	function template() {
		ini_set('memory_limit', '-1');
		$arr = ['tahun' => 'tahun','product_code' => 'product_code','product_name' => 'product_name','prsn_alloc' => 'prsn_alloc','is_active' => 'is_active'];
		$config[] = [
			'title' => 'template_import_allocation_bari',
			'header' => $arr,
		];
		$this->load->library('simpleexcel',$config);
		$this->simpleexcel->export();
	}

	function import() {
		ini_set('memory_limit', '-1');
		$file = post('fileimport');
		$col = ['tahun','product_code','product_name','prsn_alloc','is_active'];
		$this->load->library('simpleexcel');
		$this->simpleexcel->define_column($col);
		$jml = $this->simpleexcel->read($file);
		$c = 0;
		foreach($jml as $i => $k) {
			if($i==0) {
				for($j = 2; $j <= $k; $j++) {
					$data = $this->simpleexcel->parsing($i,$j);

					$cek_nama = get_data('tbl_fact_product','code',trim($data['product_code']))->row_array();
					if(!empty($cek_nama)) {
						$data['product_name'] = $cek_nama['product_name'];
					} 	
					$data['create_at'] = date('Y-m-d H:i:s');
					$data['create_by'] = user('nama');
					$save = insert_data('tbl_allocation_bari',$data);
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
		$arr = ['tahun' => 'Tahun','product_code' => 'Product Code','product_name' => 'Product Name','prsn_alloc' => 'Prsn Alloc','before_bari' => 'Before Bari','bari' => 'Bari','after_bari' => 'After Bari','is_active' => 'Aktif'];
		$data = get_data('tbl_allocation_bari')->result_array();
		$config = [
			'title' => 'data_allocation_bari',
			'data' => $data,
			'header' => $arr,
		];
		$this->load->library('simpleexcel',$config);
		$this->simpleexcel->export();
	}

}