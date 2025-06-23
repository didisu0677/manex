<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');

class Formula_cost extends BE_Controller {

    var $controller = 'formula_cost';
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
            'select' => 'a.cost_centre as kode, b.id, b.cost_centre',
            'join' => 'tbl_fact_cost_centre b on a.cost_centre = b.kode type LEFT',
            'where' => [
                'a.is_active' => 1,
                'a.id_cost_centre !=' => 0,
            ],
            'group_by' => 'a.id_cost_centre',
            'sort_by' => 'b.id', 
             ];


	    $data['cc']= get_data('tbl_fact_product a',$arr)->result();

        $access         = get_access($this->controller);
        $data['access'] = $access ;
        $data['access_additional']  = $access['access_additional'];
        render($data);
	}

    function data($tahun="",$cost_centre="",$tipe = 'table'){
		ini_set('memory_limit', '-1');

        $table = 'tbl_budget_production_dev';

        //$bm_amt = $m1->total_price * ($m1->bm/100);
        //$pph = ($bm_amt + $m1->total_price) * ($m1->pph/100);
		//$ppn = ($bm_amt + $m1->total_price) * ($m1->ppn/100);
        //$price_budget = $m1->total_price + $bm_amt + $m1->bank_charges + $m1->handling_charges ;

        $data['produk']	= get_data('tbl_material_formula a',[
            'select'	=> 'a.parent_item, a.component_item, a.material_name, 
                            a.quantity, a.um, a.group_formula', 
            'join' => ['tbl_material_price b on a.component_item = b.material_code type LEFT and b.year="'.user('tahun_budget').'"',
                    'tbl_currency_rates c on b.curr = c.curr type LEFT'
                    ],
            'where'		=> [
                '__m' => 'a.parent_item in (select budget_product_code from tbl_beginning_stock where is_active = 1 and tahun="'.$tahun.'")',
                'a.tahun' => $tahun,
                'a.component_item' => 'CIRMDBBF1J'
                ],
            'group_by' => 'a.parent_item,a.component_item',
            'sort_by' => 'a.parent_item'
        ])->result();



        $data['detail']	= get_data('tbl_material_formula a',[
            'select'	=> 'a.parent_item, a.component_item, a.material_name, 
                            a.quantity, a.um, a.group_formula, b.bm, 
                            b.bank_charges, b.handling_charges, b.price_us ,b.curr, c.rates, c.ppn, c.pph, (b.price_us * c.rates) as total_price',
            'join' => ['tbl_material_price b on a.component_item = b.material_code type LEFT and b.year="'.user('tahun_budget').'"',
                    'tbl_currency_rates c on b.curr = c.curr type LEFT'
                    ],
            'where'		=> [
                '__m' => 'a.parent_item in (select budget_product_code from tbl_beginning_stock where is_active = 1 and tahun="'.$tahun.'")',
                'a.tahun' => $tahun,
                'a.component_item' => 'CIRMDBBF1J',
                ],
            'sort_by' => 'a.parent_item'
        ])->result();

        debug($data['detail']);die;


        $response	= array(
            'table'		=> $this->load->view('transaction/formula_cost/table',$data,true),
        );
	   
	    render($response,'json');
    }

    function detail($id='') {
		$code 	= get('code');
        $tahun = get('tahun');

        $data	= get_data('tbl_fact_product a',[
            'select' => 'a.*',
            'where' => [
                'a.code' => $code,
            ]
		])->row_array();

		$data['detail']	= get_data('tbl_material_formula a',[
			'select'	=> 'a.*, b.bm, b.bank_charges, b.handling_charges, b.price_us ,b.curr, c.rates, c.ppn, c.pph, (b.price_us * c.rates) as total_price',
            'join' => ['tbl_material_price b on a.component_item = b.material_code type LEFT and b.year="'.user('tahun_budget').'"',
                    'tbl_currency_rates c on b.curr = c.curr type LEFT'
                      ],
			'where'		=> [
				'a.parent_item' => $code,
                'a.tahun' => user('tahun_budget'),
                // 'b.year' => user('tahun_budget')
				],
		])->result();


		render($data,'layout:false');
	}

}