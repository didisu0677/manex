<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');

class Formula extends BE_Controller {

	function __construct() {
		parent::__construct();
	}

	function index() {
		$arr = [
            'select' => 'distinct a.parent_item,a.item_name',
            'where' => [
                'a.is_active' => 1,
            ],
        ];

        $data['produk_items'] = get_data('tbl_material_formula a', $arr)->result(); 
		
		render($data);
	}

	function data($produk="") {
		$config =[];
		
		if($produk && $produk != 'ALL') {
	    	$config['where']['parent_item']	= $produk;	
	    }

		$data = data_serverside($config);
		render($data,'json');
	}

	function get_data() {
		$data = get_data('tbl_material_formula','id',post('id'))->row_array();
		render($data,'json');
	}

	function save() {
		$response = save_data('tbl_material_formula',post(),post(':validation'));
		render($response,'json');
	}

	function delete() {
		$response = destroy_data('tbl_material_formula','id',post('id'));
		render($response,'json');
	}

	function template() {
		ini_set('memory_limit', '-1');
		$arr = ['parent_item' => 'parent_item','item_name' => 'item_name','description' => 'description','component_item' => 'component_item','material_name' => 'material_name','um' => 'um','quantity' => 'quantity','scrap' => 'scrap','total' => 'total','operation' => 'operation','article_number' => 'article_number','start_effective' => 'start_effective','is_active' => 'is_active'];

		$config[] = [
			'title' => 'template_import',
			'header' => $arr,
		];
		$this->load->library('simpleexcel',$config);
		$this->simpleexcel->export();
	}

	function import() {
		//
		ini_set('memory_limit', '-1');
        ini_set('max_execution_time', -1);
		$file = post('fileimport');
		$col = ['parent_item','item_name','description','component_item','material_name','um','quantity','scrap','total','operation','article_number','start_effective','is_active'];
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
					$save = insert_data('tbl_material_formula',$data);
					if($save) $c++;
				}
			}
		}
		/// test
		$response = [
			'status' => 'success',
			'message' => $c.' '.lang('data_berhasil_disimpan').'.'
		];
		@unlink($file);
		render($response,'json');
	}

	function export() {
		ini_set('memory_limit', '-1');
		$arr = ['parent_item' => 'Parent Item','item_name' => 'Item Name','description' => 'Description','component_item' => 'Component Item','material_name' => 'Material Name','um' => 'Um','quantity' => 'Quantity','scrap' => 'Scrap','total' => 'Total','operation' => 'Operation','article_number' => 'Article Number','start_effective' => '-dStart Effective','is_active' => 'Aktif'];
		$data = get_data('tbl_material_formula')->result_array();
		$config = [
			'title' => 'data_formula',
			'data' => $data,
			'header' => $arr,
		];
		$this->load->library('simpleexcel',$config);
		$this->simpleexcel->export();
	}

}