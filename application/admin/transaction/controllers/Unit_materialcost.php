<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');

class Unit_materialcost extends BE_Controller {
    var $controller = 'unit_materialcost';
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

		$data['status_cost'] = get_data('tbl_scm_submit',[
			'where' => [
				'is_submit' => 1,
				'is_active' => 1,
				'tahun' => user('tahun_budget')
			],
		])->row_array();


		$access         = get_access($this->controller);
        $data['access'] = $access;
        $data['access_additional']  = $access['access_additional'];

		render($data);
	}

	function data($tahun="") {
		$access         = get_access($this->controller);
		if($access['access_delete'] || user('id_group')==1 || user('id_group') ==2) {
			$config =[
				'access_edit'	=> false,
				// 'access_delete'	=> false,
				'access_view'	=> false,
			];
		}else{
			$config =[
				'access_edit'	=> false,
				'access_delete'	=> false,
				'access_view'	=> false,
			];
		}
		
		if($tahun) {
	    	$config['where']['tahun']	= $tahun;	
	    }

		$data = data_serverside($config);
		render($data,'json');
	}

	function get_data() {
		$data = get_data('tbl_unit_material_cost','id',post('id'))->row_array();
		render($data,'json');
	}

	function save() {
		$response = save_data('tbl_unit_material_cost',post(),post(':validation'));
		render($response,'json');
	}

	function delete() {
		$response = destroy_data('tbl_unit_material_cost','id',post('id'));
		render($response,'json');
	}

	function template() {
		ini_set('memory_limit', '-1');
		$arr = ['tahun' => 'tahun','id_product' => 'id_product','product_code' => 'product_code','description'=>'description','qty_production' => 'qty_production','bottle' => 'bottle','content' => 'content','packing' => 'packing','set' => 'set','subrm_total' => 'subrm_total','is_active' => 'is_active'];
		$config[] = [
			'title' => 'template_import_unit_materialcost',
			'header' => $arr,
		];
		$this->load->library('simpleexcel',$config);
		$this->simpleexcel->export();
	}

	function import() {
		ini_set('memory_limit', '-1');
		$file = post('fileimport');
		$col = ['tahun','id_product','product_code','description','qty_production','bottle','content','packing','set','subrm_total','is_active'];
		$this->load->library('simpleexcel');
		$this->simpleexcel->define_column($col);
		$jml = $this->simpleexcel->read($file);
		$c = 0;
		foreach($jml as $i => $k) {
			if($i==0) {
				for($j = 2; $j <= $k; $j++) {
					$data = $this->simpleexcel->parsing($i,$j);

	

					$data['id_product'] = 0;
					$product = get_data('tbl_fact_product','code',$data['product_code'])->row();
					if(isset($product->id)) $data['id_product'] = $product->id;

					$data['create_at'] = date('Y-m-d H:i:s');
					$data['create_by'] = user('nama');

	
					$cek = get_data('tbl_unit_material_cost',[
						'where' => [
							'tahun' => $data['tahun'],
							'product_code' => $data['product_code'],
						]
					])->row();

					if(!isset($cek->product_code)){
						$save = insert_data('tbl_unit_material_cost',$data);
					}else{	
						$save = update_data('tbl_unit_material_cost',$data,['tahun'=>$data['tahun'],'product_code'=>$data['product_code']]);
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
		$arr = ['tahun' => 'Tahun','id_product' => 'Id Product','product_code' => 'Product Code','description'=>'description','qty_production' => 'Qty Production','bottle' => 'Bottle','content' => 'Content','packing' => 'Packing','set' => 'Set','subrm_total' => 'Subrm Toal','is_active' => 'Aktif'];
		$data = get_data('tbl_unit_material_cost','tahun',get('tahun'))->result_array();
		$config = [
			'title' => 'data_unit_materialcost',
			'data' => $data,
			'header' => $arr,
		];
		$this->load->library('simpleexcel',$config);
		$this->simpleexcel->export();
	}

	function proses(){
		ini_set('memory_limit', '-1');
		ini_set('max_execution_time', 0);

		$tahun = post('tahun');

        $table1 = 'tbl_budget_unitcogs_' .$tahun;

		$unitrm = get_data('tbl_unit_material_cost a',[
            'select' => 'a.id_product, a.product_code,a.description,a.qty_production, a.bottle, a.content, a.packing, a.set, a.subrm_total,
						 b.destination, b.cost_centre, b.id_cost_centre, b.product_line, b.sub_product,b.divisi',
			'join' => 'tbl_fact_product b on a.product_code = b.code',
            'where' => [
			    'a.tahun' => $tahun,
				// 'a.product_code' => 'CIHODNSPDM'
            ],
            'group_by' => 'id_product,product_code',
		    ])->result();
		

		foreach($unitrm as $u) {
            $arr            = [
                'select'    => 'a.*',
                'where'     => [
                    'a.id_budget_product' => $u->id_product,
					'a.budget_product_code' => $u->product_code,
					// 'a.budget_product_code' => 'CIHODNSPDM',
					'a.tahun' => $tahun,
                ],
            ];

			$cek1 = get_data($table1 . ' a',$arr)->row();

			

  
			if(isset($cek1->budget_product_code)) {	 

				$field1 = "";

				for ($i = 1; $i <= 12; $i++) { 
					$field1 = 'B_' . sprintf('%02d', $i);
					$$field1 = $u->subrm_total + $cek1->$field1;

					// debug($$field1);die;

					update_data($table1,[$field1=>$$field1],['budget_product_code'=>$cek1->budget_product_code]);
				}
			}
			// else{

			// 	$sector = get_data('tbl_sector_price','is_active',1)->result();
			// 	foreach($sector as $s) {

			// 		$data_insert = [
			// 			'tahun' => $tahun,
			// 			'product_line' => $u->product_line,
			// 			'divisi' => $u->divisi,
			// 			'category' => $u->sub_product,
			// 			'id_budget_product' => $u->id_product,
			// 			'budget_product_code' => $u->product_code,
			// 			'budget_product_name' => $u->description,
			// 			'budget_product_sector' => $s->id

			// 		];

			// 		for ($i = 1; $i <= 12; $i++) { 
			// 			$field1 = 'B_' . sprintf('%02d', $i);

			// 			$data_insert[$field1] = $u->subrm_total ;
			// 		}

			// 		insert_data($table1,$data_insert);
			// 	}
			// }
		}


		render([
			'status'	=> 'success',
			'message'	=> 'Posting Actual Sales data has benn succesfuly'
		],'json');	
	}

	function unlock_cost(){
		ini_set('memory_limit', '-1');
		ini_set('max_execution_time', 0);

		$tahun = post('tahun');

		update_data('tbl_scm_submit',[
			'is_active' => 0,
			'is_submit' => 0,
		],
		[	'tahun' => $tahun,
		]);

		render([
			'status'	=> 'success',
			'message'	=> 'Costing Data has been unlock'
		],'json');	

	}

}