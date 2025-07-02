<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');

class Product_factory extends BE_Controller {

	function __construct() {
		parent::__construct();
		switch_database();
	}

	function index() {
		switch_database();
		$data['opt_cc'] = get_data('tbl_fact_cost_centre',[
			'where' => [
				'is_active' => 1,
				'id_fact_department' => 2
			],
			])->result_array();

		switch_database('budget_ho');
		$data['sub_product'] = get_data('tbl_subaccount',[
			'select' => 'id,subaccount_code,CONCAT(subaccount_code," - ", subaccount_desc) as subaccount_desc',
			'where' => [
				'status' => 1,
				'parent_id' => 0
			],
		])->result_array();
		switch_database();
		render($data);
	}

	function data() {
		$data = data_serverside();
		render($data,'json');
	}

	function get_data() {
		$data = get_data('tbl_fact_product','id',post('id'))->row_array();
		render($data,'json');
	}

	function save() {
		$data = post();

		switch_database('budget_ho');
		$data['sub_product']  = '';
		$data['divisi']  = '';
		$sub_product = get_data('tbl_subaccount','subaccount_code',$data['product_line'])->row();
		if(isset($sub_product->subaccount_code)) {
			 $data['sub_product'] = $sub_product->subaccount_desc;
			 $data['divisi'] = $sub_product->bisunit;
		}
		
		switch_database();

		$cc = get_data('tbl_fact_cost_centre','id',$data['id_cost_centre'])->row();
		if(isset($cc->kode)) {
			 $data['cost_centre'] = $cc->kode;
		}

		$response = save_data('tbl_fact_product',$data,post(':validation'));
		render($response,'json');
	}

	function delete() {
		$response = destroy_data('tbl_fact_product','id',post('id'));
		render($response,'json');
	}

	function template() {
		ini_set('memory_limit', '-1');
		$arr = ['code' => 'code','product_name' => 'product_name','destination' => 'destination','cost_centre' => 'cost_centre','product_line' => 'product_line','is_active' => 'is_active'];
		$config[] = [
			'title' => 'template_import_product_factory',
			'header' => $arr,
		];
		$this->load->library('simpleexcel',$config);
		$this->simpleexcel->export();
	}

	function import() {
		ini_set('memory_limit', '-1');
		$file = post('fileimport');
		$col = ['code','product_name','destination','cost_centre','product_line','is_active'];
		$this->load->library('simpleexcel');
		$this->simpleexcel->define_column($col);
		$jml = $this->simpleexcel->read($file);
		$c = 0;
		foreach($jml as $i => $k) {
			if($i==0) {
				for($j = 2; $j <= $k; $j++) {
					$data = $this->simpleexcel->parsing($i,$j);

					$id = get_data('tbl_fact_cost_centre','kode',$data['cost_centre'])->row();
					$data['id_cost_centre'] = 0;
					if(isset($id->id)) {
						$data['id_cost_centre'] = $id->id;
					}

					$data['sub_product'] = '';
					$data['divisi'] = '';
					switch_database('budget_ho');
					$sub = get_data('tbl_subaccount',[
						'where' => [
							'subaccount_code'=>$data['product_line'],
							'parent_id' => 0,
						],
					])->row() ;
					if(isset($sub->subaccount_code)) $data['sub_product'] = $sub->subaccount_desc;
					if(isset($sub->bisunit)) $data['divisi'] = $sub->bisunit;


					if(empty($data['divisi']) || empty($data['sub_product'])) {
						$response = [
							'status' => 'failed',
							'message' => 'sub product and division must be filled'
						];
						render($response,'json');
						die;
					}


					switch_database();
					$data['create_at'] = date('Y-m-d H:i:s');
					$data['create_by'] = user('nama');

					$cek = get_data('tbl_fact_product','code',$data['code'])->row();
					if(!isset($cek->id)) {
						$save = insert_data('tbl_fact_product',$data);
					}else{
						$save = update_data('tbl_fact_product',$data,['id'=>$cek->id]);
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
		$arr = ['code' => 'Code','product_name' => 'Product Name','destination' => 'Destination','cost_centre'=>'Cost Centre','product_line' => 'Product Line','is_active' => 'Aktif'];
		$data = get_data('tbl_fact_product')->result_array();
		$config = [
			'title' => 'data_product_factory',
			'data' => $data,
			'header' => $arr,
		];
		$this->load->library('simpleexcel',$config);
		$this->simpleexcel->export();
	}

	function update_divisi(){
		$product = get_data('tbl_fact_product','is_active',1)->result();
		foreach($product as $p) {

			switch_database('budget_ho');
			$bu = get_data('tbl_subaccount','subaccount_code',$p->product_line)->row();
			$bisnis_unit = "";
			if(isset($bu->bisunit)){
				$bisnis_unit = $bu->bisunit;
			}

			switch_database();
			update_data('tbl_fact_product',['divisi'=>$bisnis_unit],['product_line'=>$p->product_line]);
			
		}

		echo 'success' ;
	}

}