<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');

class Rprod_qcallocation extends BE_Controller {
    var $controller = 'rprod_qcallocation';
    function __construct() {
        parent::__construct();
    }
    
    function index() {      
        $arr = [
            'select' => 'a.cost_centre as kode, b.id, b.cost_centre',
            'join' => 'tbl_fact_cost_centre b on a.cost_centre = b.kode type LEFT',
            'where' => [
                'a.is_active' => 1,
            ],
            'group_by' => 'a.cost_centre',
            'sort_by' => 'id', 
             ];


        $data['cc']= get_data('tbl_fact_product a',$arr)->result();

        $data['variable'] = get_data('tbl_fact_manex_account',[
            'select' => '*',
            'where' => [
                'is_active' => 1,
                'grup' => 'VARIABLE OVERHEAD',
            ],
        ])->result();

        $data['fixed'] = get_data('tbl_fact_manex_account',[
            'where' => [
                'is_active' => 1,
                'grup' => 'FIXED OVERHEAD',
            ],
        ])->result();

        $data['tahun'] = get_data('tbl_fact_tahun_budget', 'is_active',1)->result();   


        $access         = get_access($this->controller);
        $data['access'] = $access;
        $data['access_additional']  = $access['access_additional'];
        render($data);
    }
    
    function sortable() {
        render();
    }

    function data($tahun = "",$cost_centre="" , $tipe = 'table') {

        $arr = [
                    'select' => 'a.cost_centre as kode, b.id, b.cost_centre',
                    'join' => 'tbl_fact_cost_centre b on a.cost_centre = b.kode type LEFT',
                    'where' => [
                        'a.is_active' => 1,
                    ],
                    'group_by' => 'a.cost_centre',
                    'sort_by' => 'id', 
                 ];

        if($cost_centre && $cost_centre != "ALL") $arr['where']['a.cost_centre'] =$cost_centre;


        $data['grup'][0]= get_data('tbl_fact_product a',$arr)->result();

        foreach($data['grup'][0] as $m0) {	

           
            $data['produk'][$m0->id]= get_data('tbl_fact_allocation_qc a',[
                'select' => 'a.*,b.product_name,b.destination, c.abbreviation as initial, c.cost_centre',
                'join' =>  ['tbl_fact_product b on a.product_code = b.code',
                            'tbl_fact_cost_centre c on a.id_cost_centre = c.id type LEFT',
                           ],
                'where' => [
                    'a.tahun' => $tahun,
                    'a.id_cost_centre' =>$m0->id
                ],
                'sort_by' => 'a.id_cost_centre'
            ])->result();


        }

        $biaya = get_data('tbl_fact_manex_allocation a',[
            'select' => 'a.*',
            'join' => 'tbl_fact_cost_centre b on a.cost_centre = b.kode',
            'where' => [
                'a.tahun' => $tahun ,
                'a.cost_centre' => '3100'
            ]
        ])->result();


        foreach($biaya as $b) {
            // $data['total_biaya']['3100'][$b->manex_account] = $b->total; 
            $data['total_biaya']['3100'][$b->manex_account] = $b->after_idle; 
        }

        $data['variable'] = get_data('tbl_fact_manex_account',[
            'select' => '*',
            'where' => [
                'is_active' => 1,
                'grup' => 'VARIABLE OVERHEAD',
            ],
        ])->result();

        $data['fixed'] = get_data('tbl_fact_manex_account',[
            'where' => [
                'is_active' => 1,
                'grup' => 'FIXED OVERHEAD',
            ],
        ])->result();

        $response	= array(
            'table'		=> $this->load->view('reporting/rprod_qcallocation/table',$data,true),
        );
	   
	    render($response,'json');
	}

    function save_alokasi() {
        $tahun = post('tahun');

  
        $produk = get_data('tbl_fact_allocation_qc a',[
            'select' => 'a.*,b.product_name,b.destination, c.abbreviation as initial, c.cost_centre, c.kode',
            'join' =>  ['tbl_fact_product b on a.product_code = b.code',
                        'tbl_fact_cost_centre c on a.id_cost_centre = c.id type LEFT',
                        ],
            'where' => [
                'a.tahun' => $tahun,
            ],
            'sort_by' => 'a.id_cost_centre'
        ])->result();

        $biaya = get_data('tbl_fact_manex_allocation a',[
            'select' => 'a.cost_centre,a.manex_account as account_code, sum(a.total) as total, sum(a.total_idle) as total_idle, sum(a.after_idle) as after_idle',
            'where' => [
                'a.tahun' => $tahun,
                'a.cost_centre' => '3100',
            ],
            'group_by' => 'a.cost_centre,a.manex_account'
        ])->result();


        $direct_labour = 0;
        $utilities = 0;
        $supplies = 0;

        $indirect_labour = 0;
        $repair = 0;
        $depreciation = 0;
        $rent = 0;
        $others = 0;

        $x = 0;
        foreach($produk as $p) {
            $total_biaya = 0;

            foreach($biaya as $b) {
                if($b->cost_centre == '3100') {

                    // $x = (int) $b->total * ($p->prsn_aloc / 100);
                      $x = (int) $b->after_idle * ($p->prsn_aloc / 100);

                    switch ($b->account_code) {
                        case '7211':
                            $direct_labour = $x;
                            break;
                        case '731':
                            $utilities = $x;
                            // Handle another case
                            break;
                        case '733':
                            $supplies = $x;
                                // Handle another case
                            break;
                        case '7212':
                            $indirect_labour = $x;
                            // Handle another case
                            break;
                        case '735':
                            $repair = $x;
                                // Handle another case
                            break;
                        case '736':
                            $depreciation = $x;
                            // Handle another case
                            break;
                        case '738':
                            $rent = $x;
                            // Handle another case
                            break;
                        case '759':
                            $others = $x;
                            // Handle another case
                            break;
    
                        default:
                            // Handle default case
                            break;
                    }
                }
            }

            update_data('tbl_fact_allocation_qc',['direct_labour' => $direct_labour,'utilities' => $utilities,'supplies' => $supplies,
                'indirect_labour' => $indirect_labour,'repair' => $repair, 'depreciation' => $depreciation, 'rent'=>$rent, 'others'=>$others],
                ['tahun'=>$tahun,'product_code'=>$p->product_code]);

        }

		render([
			'status'	=> 'success',
			'message'	=> 'Report Save Sukses'
		],'json');	

        
    }


}

