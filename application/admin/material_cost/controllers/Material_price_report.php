<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');

class Material_price_report extends BE_Controller {

    var $controller = 'material_price_report';
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
        
		$arr = [
            'select' => 'distinct a.material_code,a.nama',
            'where' => [
                'a.is_active' => 1,
            ],
        ];

        $data['produk_items'] = get_data('tbl_material_price a', $arr)->result(); 

        $data['user'] = get_data('tbl_user',[
            'where' => [
                'is_active' => 1,
                'id_group' => SCM
            ],
        ])->result_array();


        $access         = get_access($this->controller);
        $data['access'] = $access ;
        $data['access_additional']  = $access['access_additional'];
        render($data);
	}

    function data($tahun="",$user="",$tipe = 'table'){
		ini_set('memory_limit', '-1');

        //// 
        $arr = [
            'select' => 'a.*,b.rates as kurs, b.pph, b.ppn,
                        CASE WHEN a.update_by != "" THEN a.update_by ELSE c.nama END AS upd, 
                        (b.rates * a.price_us) as total_price,a.bank_charges,a.handling_charges',
            'join' => ['tbl_currency_rates b on a.curr= b.curr type LEFT',
                        'tbl_user c on a.id_user = c.id type LEFT'
                      ],
            'where' => [
                'a.year' => $tahun
        ]];

        if($user && $user !='ALL') $arr['where']['a.id_user'] = $user;
        $data['produk'] = get_data('tbl_material_price a', $arr,)->result_array();

        // debug($data);die;
       

        $response	= array(
            'table'		=> $this->load->view('material_cost/material_price_report/table',$data,true),
        );
	   
	    render($response,'json');
    }

    
    function save_perubahan() {       
 
        $table = 'tbl_material_price';

        $data   = json_decode(post('json'),true);

        foreach($data as $id => $record) {
            $result = $record;
             foreach ($result as $r => $v) {       
                update_data($table, $result,'id',$id);
            }      
        }
    }
   

}