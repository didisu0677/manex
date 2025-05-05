<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');

class Material_formula extends BE_Controller {

	function __construct() {
		parent::__construct();
	}

	function index() {
		render();
	}

	function sortable() {
		render();
	}

	function data($tipe = 'table') {
		ini_set('memory_limit', '-1');
        ini_set('max_execution_time', -1);
		$mst_account = menu();
		if($mst_account['access_view']) {
			$data['mst_account'][0] = get_data('tbl_material_formula',[
					'select' => 'id,parent_item,item_name,description',
					'where'=>[
						'parent_item !='=>'',
					],
					'sort_by'=>'parent_item'
				])->result();
			foreach($data['mst_account'][0] as $m0) {
				$data['mst_account'][$m0->id] = get_data('tbl_material_formula',[
					'select' => 'id,component_item,material_name',
					'where' => [
						'parent_item'=>$m0->parent_item,
					],
					'sort_by'=>'parent_item'
					])->result();
				// foreach($data['mst_account'][$m0->id] as $m1) {
				// 	$data['mst_account'][$m1->id] = get_data('tbl_fact_account',array('where_array'=>array('parent_id'=>$m1->id),'sort_by'=>'urutan'))->result();
				// 	foreach($data['mst_account'][$m1->id] as $m2) {
				// 		$data['mst_account'][$m2->id] = get_data('tbl_fact_account',array('where_array'=>array('parent_id'=>$m2->id),'sort_by'=>'urutan'))->result();
				// 	}
				// }
			}
			if($tipe == 'sortable') {
				$response	= array(
					'content' => $this->load->view('material_cost/material_formula/sortable',$data,true)
				);
			} else {
				$data['access_edit']	= $mst_account['access_edit'];
				$data['access_delete']	= $mst_account['access_delete'];
				$response	= array(
					'table'		=> $this->load->view('material_cost/material_formula/table',$data,true),
					'option'	=> $this->load->view('material_cost/material_formula/option',$data,true)
				);
			}
		} else {
			$response	= array(
				'status'	=> 'error',
				'message'	=> 'Permission Denied'
			);
		}
		render($response,'json');
	}

	function get_data() {
		$data = get_data('tbl_fact_account','id',post('id'))->row_array();
		render($data,'json');
	}

	function save() {
		$data 		= post();
		$validation	= post(':validation');

		$response = save_data('tbl_fact_account',$data,$validation);
		render($response,'json');
	}

	function delete() {
		$child	= array(
			'level1'	=> 'tbl_fact_account',
			'level2'	=> 'tbl_fact_account',
			'level3'	=> 'tbl_fact_account',
			'id_menu'	=> 'tbl_user_akses'
		);
		$response = destroy_data('tbl_fact_account','id',post('id'),$child);
		render($response,'json');
	}

	function save_sortable() {
		$data = post('menuItem');
		update_data('tbl_fact_account',['urutan'=>0]);
		foreach($data as $id => $parent_id) {
			if(!$parent_id || $parent_id == null || $parent_id == 'null') $parent_id = 0;
			$get_urutan	= get_data('tbl_fact_account',[
				'select'	=> 'MAX(urutan) urutan',
				'where'		=> [
					'parent_id'	=> $parent_id
				]
			])->row();
			$urutan 	= $get_urutan->urutan ? $get_urutan->urutan + 1 : 1;
			$save 		= update_data('tbl_fact_account',['parent_id'=>$parent_id,'urutan'=>$urutan],'id',$id);
			if($save) {
				$mn = get_data('tbl_fact_account','id',$id)->row_array();
				if($mn['parent_id'] == 0) {
					update_data('tbl_fact_account',array('level1'=>$mn['id']),'id',$mn['id']);
				} else {
					$parent = get_data('tbl_fact_account','id',$mn['parent_id'])->row_array();
					$data_update = array(
						'level1' => $parent['level1'],
						'level2' => $parent['level2'],
						'level3' => $parent['level3'],
						'level4' => $parent['level4']
					);
					if(!$parent['level2']) $data_update['level2'] = $mn['id'];
					else if(!$parent['level3']) $data_update['level3'] = $mn['id'];
					else if(!$parent['level4']) $data_update['level4'] = $mn['id'];
					update_data('tbl_fact_account',$data_update,'id',$mn['id']);
					
				}
			}
		}
		render([
			'status'	=> 'success',
			'message'	=> lang('data_berhasil_diperbaharui')
		],'json');
	}

}