<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');

class Actual_manex extends BE_Controller {
	var $controller = 'Actual_manex';
	function __construct() {
		parent::__construct();
	}

	function index() {
		$data['tahun'] = get_data('tbl_fact_tahun_budget', [
            'where' => [
                'is_active' => 1,
                'tahun' => user('tahun_budget') -1 
            ]
        ])->result();     

		$access         = get_access($this->controller);
        $data['access'] = $access ;
		
		render($data);
	}

	function data($tahun="",$bulan="", $estimate="") {

		$config =[
	        'access_edit'	=> false,
	        'access_delete'	=> false,
	        'access_view'	=> false,
	    ];
		
		if($tahun) {
	    	$config['where']['tahun']	= $tahun;	
	    }

		if($bulan) {
	    	$config['where']['bulan']	= $bulan;	
	    }

		if(empty($estimate) || $estimate == "0") {
	    	$config['where']['is_estimate']	= "0";	
	    }else{
			$config['where']['is_estimate']	= "1";	
		}

		$data = data_serverside($config);
		render($data,'json');
	}

	function get_data() {
		$data = get_data('tbl_actual_manex','id',post('id'))->row_array();
		render($data,'json');
	}

	function save() {
		$response = save_data('tbl_actual_manex',post(),post(':validation'));
		render($response,'json');
	}

	function delete() {
		$response = destroy_data('tbl_actual_manex','id',post('id'));
		render($response,'json');
	}

	function template() {
		ini_set('memory_limit', '-1');
		$arr = ['tahun' => 'tahun','bulan' => 'bulan','account_code' => 'account_code','cost_centre' => 'cost_centre','sub_account' => 'sub_account','initial_cc' => 'initial_cc','total' => 'total','is_active' => 'is_active'];
		$config[] = [
			'title' => 'template_import_actual_manex',
			'header' => $arr,
		];
		$this->load->library('simpleexcel',$config);
		$this->simpleexcel->export();
	}

	function import() {
		ini_set('memory_limit', '-1');
		$file = post('fileimport');
		$estimate = post('filter_import');

		$col = ['tahun','bulan','account_code','cost_centre','sub_account','initial_cc','total','is_active'];
		$this->load->library('simpleexcel');
		$this->simpleexcel->define_column($col);
		$jml = $this->simpleexcel->read($file);
		$c = 0;

		delete_data('tbl_actual_manex',[
			'tahun' => post('tahun_import'),
			'bulan' => post('bulan_import')
		]);
		foreach($jml as $i => $k) {
			if($i==0) {
				for($j = 2; $j <= $k; $j++) {
					$data = $this->simpleexcel->parsing($i,$j);
					$data['is_estimate'] = $estimate;
					$data['total'] = str_replace(['.',','],'',$data['total']);
					$data['create_at'] = date('Y-m-d H:i:s');
					$data['create_by'] = user('nama');

					$cek = get_data('tbl_actual_manex', [
						'where' => [
							'tahun' => $data['tahun'],
							'bulan' => $data['bulan'],
							'account_code' => $data['account_code'],
							'cost_centre' => $data['cost_centre'],
							'sub_account' => $data['sub_account'],
							'is_estimate' => $estimate,
						]
					])->row();

					if(!isset($cek->id)) {
						$save = insert_data('tbl_actual_manex',$data);
					}else{
						$save = update_data('tbl_actual_manex',$data,['id'=>$cek->id]);
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
		$arr = ['tahun' => 'Tahun','bulan' => 'Bulan','account_code' => 'Account Code','cost_centre' => 'Cost Centre','sub_account'=> 'Sub Account','initial_cc' => 'Initial Cc','total' => 'Total','is_active' => 'Aktif'];
		$data = get_data('tbl_actual_manex')->result_array();
		$config = [
			'title' => 'data_actual_manex',
			'data' => $data,
			'header' => $arr,
		];
		$this->load->library('simpleexcel',$config);
		$this->simpleexcel->export();
	}

	function proses(){
		ini_set('memory_limit', '-1');
		ini_set('max_execution_time', 0);

        $tahun = user('tahun_budget');
		$tahun_actual = post('tahun');

		$bulan = sprintf('%02d', post('bulan'));

		$is_estimate = post('is_estimate') ;

        $table1 = 'tbl_fact_lstbudget_' .$tahun;

		$arr = [
            'select' => 'a.account_code,b.account_name,b.id as id_account,c.id as id_cost_centre,a.bulan,a.cost_centre,a.sub_account, sum(a.total) as total',
			'join'   => ['tbl_fact_account b on a.account_code  = b.account_code',
			             'tbl_fact_cost_centre c on a.cost_centre = c.kode'
						],
            'where' => [
			    'tahun' => $tahun_actual,
				'bulan' => $bulan,
				'is_estimate' => $is_estimate,
            ],
            'group_by' => 'a.account_code,a.bulan,a.cost_centre,a.sub_account',
		];

		if($is_estimate == "1") $arr['where']['bulan >'] = sprintf('%02d',setting('actual_budget'));
		

		$actual = get_data('tbl_actual_manex a',$arr)->result();


        $field1 = "";

        for ($i = 1; $i <= 12; $i++) { 
            $field1 = 'qB_' . sprintf('%02d', $i);
            $$field1 = 0;
        }

		$field = "";
		$field = 'EST_' . sprintf('%02d', $bulan);

		update_data($table1,[$field=>0]);
		$this->db->set('total_le', '(EST_01+EST_02+EST_03+EST_04+EST_05+EST_06+EST_07+EST_08+EST_09+EST_10+EST_11+EST_12)', FALSE);
		$this->db->update($table1);

		$total_le = 0;
		foreach ($actual as $a) {
			switch ($a->bulan) {
				case "01"; 
					$qB_01 = ($a->total != 0 ? $a->total : 0);
					break;
				case "02":
					$qB_02 = ($a->total != 0 ? $a->total : 0);
					break;
				case "03":
					$qB_03 = ($a->total != 0 ? $a->total : 0);
					break;
				case "04";
					$qB_04 = ($a->total != 0 ? $a->total : 0);
					break;
				case "05":
					$qB_05 = ($a->total != 0 ? $a->total : 0);
					break;
				case "06":
					$qB_06 = ($a->total != 0 ? $a->total : 0);
					break;
				case "07";
					$qB_07 = ($a->total != 0 ? $a->total : 0);
					break;
				case "08":
					$qB_08 = ($a->total != 0 ? $a->total : 0);
					break;
				case "09":
					$qB_09 = ($a->total != 0 ? $a->total : 0);
					break;
				case "10":
					$qB_10 = ($a->total != 0 ? $a->total : 0);
					break;
				case "11":
					$qB_11 = ($a->total != 0 ? $a->total : 0);
				case "12":
					$qB_12 = ($a->total != 0 ? $a->total : 0);
					break;
				default:
					echo "The color is neither red, blue, nor green!";
			}




            $arr            = [
                'select'    => 'a.*',
                'where'     => [
                    'a.account_code' => $a->account_code,
					'a.cost_centre' => $a->cost_centre,
					'a.sub_account' => $a->sub_account,
                ],
            ];

			$cek1 = get_data($table1 . ' a',$arr)->row();
  
			if(isset($cek1->account_code)) {	 
                $field1 = "" ;
                $field = "";
                $field = 'EST_' . sprintf('%02d', $a->bulan);
                $field1 = 'qB_' . sprintf('%02d', $a->bulan);      

	            update_data($table1,[$field=>$$field1],['id'=>$cek1->id]);
			}else{
				$field1 = "" ;
                $field = "";
                $field = 'EST_' . sprintf('%02d', $a->bulan);
                $field1 = 'qB_' . sprintf('%02d', $a->bulan);      

				$data2['tahun'] = $tahun;
				$data2['id_cost_centre'] = $a->id_cost_centre;
				$data2['cost_centre'] = $a->cost_centre;
				$data2['id_account'] = $a->id_account;
				$data2['account_code'] = $a->account_code;
				$data2['account_name'] = $a->account_name;
				$data2['sub_account'] = $a->sub_account;
				$data2[$field] = $$field1;

				insert_data($table1,$data2);
			}
		}

		$total = get_data($table1)->result();

		foreach($total as $t) {
			update_data($table1,
				['total_le' => $t->EST_01+$t->EST_02+$t->EST_03+$t->EST_04+$t->EST_05+$t->EST_06+$t->EST_07+$t->EST_08+$t->EST_09+$t->EST_10+$t->EST_11+$t->EST_12],['id'=>$t->id]
			);
		}

		render([
			'status'	=> 'success',
			'message'	=> 'Posting Actual Sales data has benn succesfuly'
		],'json');	
	}
}