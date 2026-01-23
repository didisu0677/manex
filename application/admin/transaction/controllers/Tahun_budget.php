<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');

class Tahun_budget extends BE_Controller {

	function __construct() {
		parent::__construct();
	}

	function index() {
		// $data['menu'][0] = get_data('tbl_menu',array('where_array'=>array('parent_id'=>0,'id'=>[49,288]),'sort_by'=>'urutan'))->result();
		// foreach($data['menu'][0] as $m0) {
		// 	$data['menu'][$m0->id] = get_data('tbl_menu',array('where_array'=>array('parent_id'=>$m0->id),'sort_by'=>'urutan'))->result();
		// 	foreach($data['menu'][$m0->id] as $m1) {
		// 		$data['menu'][$m1->id] = get_data('tbl_menu',array('where_array'=>array('parent_id'=>$m1->id),'sort_by'=>'urutan'))->result();
		// 		foreach($data['menu'][$m1->id] as $m2) {
		// 			$data['menu'][$m2->id] = get_data('tbl_menu',array('where_array'=>array('parent_id'=>$m2->id),'sort_by'=>'urutan'))->result();
		// 		}
		// 	}
		// }

		render();
	}

	function data() {
		$config['access_view'] = false;

		$config['button'][]	= button_serverside('btn-success','btn-lock',['fa-unlock',lang('manex_lock'),true],'act-lock',['is_lock' => 0]);
		$config['button'][]	= button_serverside('btn-danger','btn-unlock',['fa-lock',lang('manex_unlock'),true],'act-lock',['is_lock' => 1]);

		$config['button'][]	= button_serverside('btn-success','btn-sales-lock',['fa fa-key',lang('sales_lock'),true],'act-lock',['sales_lock' => 0]);
		$config['button'][]	= button_serverside('btn-danger','btn-sales-unlock',['fab fa-keycdn',lang('sales_unlock'),true],'act-lock',['sales_lock' => 1]);


		$data = data_serverside($config);
		
		render($data,'json');
	}

	function get_data() {
		$data = get_data('tbl_fact_tahun_budget','id',post('id'))->row_array();
		render($data,'json');
	}

	function save() {

		$data = post();
		$data['is_active'] = 1;

		$response = save_data('tbl_fact_tahun_budget',$data,post(':validation'));

		if (isset($response['status']) && $response['status'] === 'success' && !empty($data['tahun'])) {
			$this->create_file_budget($data['tahun']);
			$this->create_budget_pic($data['tahun']);
		}

		render($response,'json');
	}

	function create_file_budget($tahun="") {

        $tahun1 = $tahun ;
        $tahun0 = $tahun - 1 ;
    
        $res = get_data('information_schema.tables',[
            'select' => 'table_name',
            'where' => [
                'table_schema' => 'manex',     
                'table_name REGEXP' => '_[0-9]{4}$',
                'table_name like' => "%_" . $tahun0,
                '__m' => "(substr(table_name, 1, 4) != 'act_' AND substr(table_name, 1, 4) != 'sim_')"
 
            ]
        ])->result();

        $jum = 0;
        $old_table = '';
        $new_table = '';
        foreach($res as $v) {
            $old_table = ($v->TABLE_NAME);
            $new_table = str_replace($tahun0, $tahun1, $old_table);

            $sql = "CREATE TABLE $new_table LIKE $old_table";

            if(!table_exists($new_table)) {
                $this->db->query($sql);
                $jum++;
            }
        }

    }

	function create_budget_pic($tahun = '') {
		$tahun = (int) $tahun;
		if ($tahun <= 0) {
			return;
		}

		$tahun_sebelumnya = $tahun - 1;
		$fields = $this->db->list_fields('tbl_fact_pic_budget');
		if (empty($fields)) {
			return;
		}

		if (!in_array('tahun', $fields, true)) {
			return;
		}

		$columns = [];
		$selects = [];
		foreach ($fields as $field) {
			if ($field === 'id') {
				continue;
			}

			$identifier = $this->db->protect_identifiers($field);
			$columns[] = $identifier;
			if ($field === 'tahun') {
				$selects[] = $this->db->escape($tahun) . ' AS ' . $identifier;
				continue;
			}

			$selects[] = 'src.' . $identifier;
		}

		if (empty($columns) || empty($selects)) {
			return;
		}

		// Copy prior-year PIC assignments into the newly created budget year.
		$table = $this->db->protect_identifiers('tbl_fact_pic_budget');
		$where_column = $this->db->protect_identifiers('tahun');
		$sql = 'INSERT INTO ' . $table . ' (' . implode(',', $columns) . ') ' .
			'SELECT ' . implode(',', $selects) . ' FROM ' . $table . ' src ' .
			'WHERE src.' . $where_column . ' = ' . $this->db->escape($tahun_sebelumnya);

		if (in_array('id_cost_centre', $fields, true)) {
			$id_cost_centre = $this->db->protect_identifiers('id_cost_centre');
			$sql .= ' AND NOT EXISTS (SELECT 1 FROM ' . $table . ' dst WHERE dst.' . $where_column . ' = ' .
				$this->db->escape($tahun) . ' AND dst.' . $id_cost_centre . ' = src.' . $id_cost_centre . ')';
		}

		$this->db->query($sql);
	}

	function delete() {
		$response = destroy_data('tbl_fact_tahun_budget','id',post('id'));
		render($response,'json');
	}


	function unlock() {

		if(post('id_lock')) {
			$data = [
				'id' => post('id_lock'),
				'is_lock'	=> 1
			];
		}

		if(post('id_sales_lock')) {
			$data = [
				'id' => post('id_sales_lock'),
				'sales_lock'	=> 1
			];
		}

		$res = save_data('tbl_fact_tahun_budget',$data);
		render($res,'json');
	}

	function lock() {
		
		if(post('id_unlock')) {
			$data = [
				'id' => post('id_unlock'),
				'is_lock'	=> 0
			];
		}

		if(post('id_sales_unlock')) {
			$data = [
				'id' => post('id_sales_unlock'),
				'sales_lock'	=> 0
			];
		}

		$res = save_data('tbl_fact_tahun_budget',$data);
		render($res,'json');
	}

}