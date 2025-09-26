<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');

class Idle_allocation_actual extends BE_Controller {
	var $controller = 'Idle_allocation';
	function __construct() {
		parent::__construct();
	}

	function index() {
		$data['tahun'] = get_data('tbl_fact_tahun_budget', 'is_active',1)->result();   
		$data['opt_cc'] = get_data('tbl_fact_cost_centre',[
			'where' => [
				'is_active' => 1,
				'id_fact_department' => 2
			],
			])->result_array();

		
		$access         = get_access($this->controller);
        $data['access'] = $access ;

		render($data);
	}

	function data($tahun="") {
		$config =[];

		if($tahun) {
	    	$config['where']['tahun']	= $tahun;	
	    }

		$data = data_serverside($config);
		render($data,'json');
	}

	function get_data() {
		$data = get_data('tbl_idle_allocation_actual','id',post('id'))->row_array();
		render($data,'json');
	}

	function save() {
		$response = save_data('tbl_idle_allocation_actual',post(),post(':validation'));
		render($response,'json');
	}

	function delete() {
		$response = destroy_data('tbl_idle_allocation_actual','id',post('id'));
		render($response,'json');
	}

	function proses(){
		ini_set('memory_limit', '-1');
		ini_set('max_execution_time', 0);

        $tahun = post('tahun');
		$table = 'tbl_fact_lstbudget_' . $tahun ; 

		$lst = get_data($table . ' a',[
			'select' => 'a.id,b.tahun,a.id_cost_centre, a.cost_centre, a.total_budget,b.prsn_allocation',
			'join' => 'tbl_idle_allocation_actual b on a.id_cost_centre = b.id_cost_centre and b.is_active = 1 type left',
			'where' => [
				'a.total_budget !=' => 0,
			],
		])->result();

		foreach($lst as $l) {
			update_data($table,
				['total_budget_idle' => ($l->total_budget * ($l->prsn_allocation /100)), 'budget_after_idle' => $l->total_budget - ($l->total_budget * ($l->prsn_allocation /100))],
				['id' => $l->id],
			);
		}

		render([
			'status'	=> 'success',
			'message'	=> 'Posting Actual Sales data has benn succesfuly'
		],'json');	
	}

	function template() {
		ini_set('memory_limit', '-1');
		$arr = ['tahun' => 'tahun', 'bulan' => 'bulan', 'id_cost_centre' => 'id_cost_centre','cost_center' => 'cost_center','prsn_allocation' => 'prsn_allocation','is_active' => 'is_active'];
		$config[] = [
			'title' => 'template_import_idle_allocation',
			'header' => $arr,
		];
		$this->load->library('simpleexcel',$config);
		$this->simpleexcel->export();
	}

	function import() {
		ini_set('memory_limit', '-1');
		$file = post('fileimport');
		$col = ['tahun','bulan','id_cost_centre','cost_center','prsn_allocation','is_active'];
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
					$save = insert_data('tbl_idle_allocation_actual',$data);
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
		$arr = ['tahun' => 'Tahun', 'bulan' => 'Bulan', 'id_cost_centre' => 'Id Cost Centre','cost_center' => 'Cost Center','prsn_allocation' => '-dPrsn Allocation','is_active' => 'Aktif'];
		$data = get_data('tbl_idle_allocation_actual')->result_array();
		$config = [
			'title' => 'data_idle_allocation',
			'data' => $data,
			'header' => $arr,
		];
		$this->load->library('simpleexcel',$config);
		$this->simpleexcel->export();
	}

}