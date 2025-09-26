<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');

class Additional_allocation_actual extends BE_Controller {
	var $controller = 'additional_allocation';
	function __construct() {
		parent::__construct();
	}

	function index() {
		$data['tahun'] = get_data('tbl_fact_tahun_budget', 'is_active',1)->result();   

		$data['product'] = get_data('tbl_fact_product',[
			'select' => 'code, CONCAT(code, " - ", product_name) as product_name',
			'where' => [
				'is_active' => 1
			],
		])->result_array();

		$data['account'] = get_data('tbl_fact_account',[
			'select' => 'account_code, CONCAT(account_code, " - ", account_name) as account_name',
			'where' => [
				'is_active' => 1
			],
		])->result_array();

		$arr = [
			'select' => 'a.cost_centre as kode, b.id, b.cost_centre',
			'join' => 'tbl_fact_cost_centre b on a.cost_centre = b.kode type LEFT',
			'where' => [
				'a.is_active' => 1,
				'a.id_cost_centre !=' => 0,
			],
			'group_by' => 'a.id_cost_centre',
			'sort_by' => 'id', 
			 ];


		$data['cc'] = get_data('tbl_fact_product a',$arr)->result_array();

		$access         = get_access($this->controller);
        $data['access'] = $access ;

		render($data);
	}

	function data($tahun="") {
		$config = [
			'access_input' => true,
			'access_edit' => true,
			'access_delete' => true,
			'access_view' => true,
		];

		if($tahun) {
	    	$config['where']['tahun']	= $tahun;	
	    }


		$data = data_serverside($config);
		render($data,'json');
	}

	function get_data() {
		$data = get_data('tbl_add_alloc_product_actual','id',post('id'))->row_array();
		render($data,'json');
	}

	function detail($id=0) {
	    $data	= get_data('tbl_add_alloc_product_actual a',[
			'select' => 'a.*,b.product_name, c.cost_centre',
			'join' => ['tbl_fact_product b on a.product_code = b.code',
						'tbl_fact_cost_centre c on a.alloc_cc_product = c.kode'],
	        'where' => [
	            'a.id' =>  $id
	        ],
	        
	    ])->row_array();
	    
	    $data['new_allocated'] 	= get_data('tbl_new_allocation_product_actual a',[
	        'select'    => 'a.*,b.product_name, d.qty_production',
	        'join'      => ['tbl_fact_product b ON a.product_code = b.code TYPE LEFT',
							'tbl_fact_cost_centre c ON b.cost_centre = c.kode type LEFT',
							'tbl_fact_product_ovh_actual d on a.tahun = d.tahun and a.product_code = d.product_code'],
	        'where'     => [
	            'a.id_allocation'     => $id,
	        ]
	        
	    ])->result();


	    
	    render($data,'layout:false');
	}

	function save() {
		$response = save_data('tbl_add_alloc_product_actual',post(),post(':validation'));
		if($response['status']== 'success'){

			$ovh_1 = get_data('tbl_fact_product_ovh_actual',[
				'select' => '*',
				'where' => [
					'tahun' => post('tahun'),
					'bulan' => post('bulan'),
					'product_code' => post('product_code'),
				]
			])->row();

			
			$product_alloc = get_data('tbl_fact_product',[
				'where' => [
					'is_active' => 1,
					'cost_centre' => post('alloc_cc_product'),
					'code !=' => post('product_code'),
				],
			])->result();
			foreach($product_alloc as $p) {

				switch (post('account_code')) {
					case '7211':
						$account = 'direct_labour';
						break;
					case '731':
						$account = 'utilities';
						// Handle another case
						break;
					case '733':
						$account = 'utilities';
							// Handle another case
						break;
					case '7212':
						$account = 'indirect_labour';
						// Handle another case
						break;
					case '735':
						$account = 'repair';
							// Handle another case
						break;
					case '736':
						$account = 'depreciation';
						// Handle another case
						break;
					case '738':
						$account = 'rent';
						// Handle another case
						break;
					case '759':
						$account = 'others';
						// Handle another case
						break;
					default:
						$account = '';
						// Handle default case
						break;
				}

				$ovh = get_data('tbl_fact_product_ovh_actual',[
					'select' => $account . ' as jumlah, macwh_total',
					'where' => [
						'tahun' => post('tahun'),
						'bulan' => post('bulan'),
						'product_code' => $p->code,
					]

				])->row();

				$cek = get_data('tbl_new_allocation_product_actual',[
					'where' => [
						'tahun' => post('tahun'),
						'bulan' => post('bulan'),
						'product_code' => $p->code,
						'account_code' => post('account_code'),
					]
				])->row();

				if(!isset($cek->product_code)) {

					$data_insert = [
						'tahun' => post('tahun'),
						'bulan' => post('bulan'),
						'id_allocation' => $response['id'],
						'product_code' => $p->code,
						'account_code' => post('account_code'),
						'nilai_akun_awal' => $ovh->jumlah,
						'machine_wh' => $ovh->macwh_total,
						'unit_produksi_alokasi' => (str_replace(['.',','],'',post('jumlah_allocation')) * $ovh_1->qty_production),
					];
					insert_data('tbl_new_allocation_product_actual',$data_insert);
				}else{
					$data_update = [
						'nilai_akun_awal' => $ovh->jumlah,
						'machine_wh' => $ovh->macwh_total,
						'unit_produksi_alokasi' => (str_replace(['.',','],'',post('jumlah_allocation'))  * $ovh_1->qty_production),
					];
					update_data('tbl_new_allocation_product_actual',$data_update,['tahun'=>post('tahun'),'product_code'=>$p->code, 'id_allocation' => $response['id']]);
				}
			}

			$sum = get_data('tbl_new_allocation_product_actual',[
				'select' => 'sum(machine_wh) as total_machine',
				'where' => [
					'id_allocation' => $response['id'],
				],
			])->row();



			$hit = get_data('tbl_new_allocation_product_actual',[
				'where' => [
					'id_allocation' => $response['id'],
				]
			])->result();


			foreach($hit as $h) {
				update_data('tbl_new_allocation_product_actual',['rasio_mesin' => ($h->machine_wh / $sum->total_machine),
				'nilai_akun_current' => ($h->machine_wh / $sum->total_machine) * $h->unit_produksi_alokasi],['id'=>$h->id]);
			}
		}

		render($response,'json');
	}

	function proses() {

		ini_set('memory_limit', '-1');
		ini_set('max_execution_time', 0);

		$awal = get_data('tbl_add_alloc_product_actual','id',post('id_allocation'))->row();
		if(isset($awal->product_code)) {
			update_data('tbl_fact_product_ovh_actual',['depreciation' => $awal->jumlah_penyesuaian],['tahun' =>$awal->tahun, 'bulan' =>$awal->bulan, 'product_code'=>$awal->product_code]);
		}

		$alloc = get_data('tbl_new_allocation_product_actual','id_allocation',post('id_allocation'))->result();

		foreach($alloc as $a) {

			switch ($a->account_code) {
				case '7211':
					$account = 'direct_labour';
					break;
				case '731':
					$account = 'utilities';
					// Handle another case
					break;
				case '733':
					$account = 'utilities';
						// Handle another case
					break;
				case '7212':
					$account = 'indirect_labour';
					// Handle another case
					break;
				case '735':
					$account = 'repair';
						// Handle another case
					break;
				case '736':
					$account = 'depreciation';
					// Handle another case
					break;
				case '738':
					$account = 'rent';
					// Handle another case
					break;
				case '759':
					$account = 'others';
					// Handle another case
					break;
				default:
					$account = '';
					// Handle default case
					break;
			}

			update_data('tbl_fact_product_ovh_actual',[$account => $a->nilai_akun_current],['tahun' =>$a->tahun, 'bulan' =>$a->bulan, 'product_code'=>$a->product_code]);
		}

		render([
			'status'	=> 'success',
			'message'	=> 'Allocation Succesfully'
		],'json');	
	}

	function delete() {
		$response = destroy_data('tbl_add_alloc_product_actual','id',post('id'));
		if($response['status'] = 'success') {
			destroy_data('tbl_new_allocation_product_actual','id_allocation',post('id'));
		}
		render($response,'json');
	}

	function template() {
		ini_set('memory_limit', '-1');
		$arr = ['tahun' => 'tahun', 'bulan' => 'bulan', 'product_code' => 'product_code','account_code' => 'account_code','jumlah_asal' => 'jumlah_asal','jumlah_penyesuaian' => 'jumlah_penyesuaian','jumlah_allocation' => 'jumlah_allocation','alloc_cc_product' => 'alloc_cc_product','product_detail' => 'product_detail','is_active' => 'is_active'];
		$config[] = [
			'title' => 'template_import_additional_allocation',
			'header' => $arr,
		];
		$this->load->library('simpleexcel',$config);
		$this->simpleexcel->export();
	}

	function import() {
		ini_set('memory_limit', '-1');
		$file = post('fileimport');
		$col = ['tahun', 'bulan', 'product_code','account_code','jumlah_asal','jumlah_penyesuaian','jumlah_allocation','alloc_cc_product','product_detail','is_active'];
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
					$save = insert_data('tbl_add_alloc_product_actual',$data);
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
		$arr = ['tahun' => 'Tahun', 'bulan' => 'Bulan', 'product_code' => 'Product Code','account_code' => 'Account Code','jumlah_asal' => 'Jumlah Asal','jumlah_penyesuaian' => 'Jumlah Penyesuaian','jumlah_allocation' => 'Jumlah Allocation','alloc_cc_product' => 'Alloc Cc Product','product_detail' => 'Product Detail','is_active' => 'Aktif'];
		$data = get_data('tbl_add_alloc_product_actual')->result_array();
		$config = [
			'title' => 'data_additional_allocation',
			'data' => $data,
			'header' => $arr,
		];
		$this->load->library('simpleexcel',$config);
		$this->simpleexcel->export();
	}

}