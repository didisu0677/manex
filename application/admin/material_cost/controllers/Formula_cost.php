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

        if($cost_centre && $cost_centre !='ALL') $arr['where']['a.cost_centre'] = $cost_centre;


	    $data['grup'][0]= get_data('tbl_fact_product a',$arr)->result();


        foreach($data['grup'][0] as $m0) {	

            $cproduk = get_data('tbl_fact_product a',[
                'where' => [
                    'a.is_active' => 1,
                    'a.id_cost_centre' => $m0->id,
                ],
                'sort_by' => 'a.id_cost_centre'
            ])->result();
            
            foreach($cproduk as $p) {   
                $cek = get_data($table . ' a',[
                    'select' => 'a.*',
                    'where' => [
                        'a.tahun' => $tahun,
                        'a.budget_product_code' => $p->code,
                        'a.product_line' => $p->product_line,
                    ]
                ])->row();
                if(!isset($cek->id)){
                    insert_data($table,
                    ['tahun' => $tahun, 'id_cost_centre' => $p->id_cost_centre ,'divisi' => $p->divisi, 'product_line' => $p->product_line, 'id_budget_product'=>$p->id, 'budget_product_code'=>$p->code, 
                    'budget_product_name' => $p->product_name, 'category' => $p->sub_product]
                );
                }
            }


            $data['produk'][$m0->id]= get_data('tbl_budget_production_dev a',[
                'select' => 'a.*,b.code,b.product_name,b.destination, c.abbreviation as initial, c.cost_centre,
                            SUM(CASE WHEN d.group_formula = "A" THEN (d.quantity * (e.price_us * f.rates)) ELSE 0 END) AS Bottle,
                            SUM(CASE WHEN d.group_formula = "B" THEN (d.quantity * (e.price_us * f.rates)) ELSE 0 END) AS Content,
                            SUM(CASE WHEN d.group_formula = "C" THEN (d.quantity * (e.price_us * f.rates)) ELSE 0 END) AS Packing,
                            SUM(CASE WHEN d.group_formula = "D" THEN (d.quantity * (e.price_us * f.rates)) ELSE 0 END) AS Sets',
                'join' =>  ['tbl_fact_product b on a.budget_product_code = b.code',
                            'tbl_fact_cost_centre c on a.id_cost_centre = c.id type LEFT',
                            'tbl_material_formula d on a.budget_product_code = d.parent_item and d.tahun ="'.user('tahun_budget').'" type LEFT',
                            'tbl_material_price e on d.component_item = e.material_code and e.year ="'.user('tahun_budget').'" type LEFT',
                            'tbl_currency_rates f on e.curr = f.curr and e.year ="'.user('tahun_budget').'" type LEFT'
                           ],
                'where' => [
                    'a.tahun' => $tahun,
                    'a.id_cost_centre' =>$m0->id,
                    // 'a.budget_product_code1' => 'CIKRTRUNDM'
                ],
                'group_by' => 'a.budget_product_code',
            ])->result();

        }


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