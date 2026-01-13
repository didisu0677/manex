<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');

class User_management extends BE_Controller {

	function __construct() {
		parent::__construct();
	}

	function index() {
		$data = array();
		$t_menu = uri_segment(2);
		// Strip _actual from target for menu lookup to ensure proper access control
		$lookup_tmenu = str_replace('_actual', '', $t_menu);
		$m_name = get_data('tbl_menu','target',$lookup_tmenu)->row();
		if(isset($m_name->id)) {
			$access = get_data('tbl_user_akses',array('where_array'=>array('act_view'=>1,'id_group'=>user('id_group'))))->result();
			$id_menu = array(0);
			foreach($access as $a) {
				$id_menu[] = $a->id_menu;
			}
			$data['quick_link'] = get_data('tbl_menu',array('where_array'=>array('is_active'=>1,'parent_id'=>$m_name->id),'where_in'=>array('id'=>$id_menu),'sort_by'=>'urutan','sort'=>'ASC'))->result();
		}
		render($data,'view:home/welcome/quick_link');
	}

}