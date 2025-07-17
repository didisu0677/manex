<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');

class Cogm extends BE_Controller {
    var $controller = 'cogm';
    function __construct() {
        parent::__construct();
    }
    
    function index() {      
        $arr = [
            'select' => 'a.cost_centre as kode, b.id, b.cost_centre',
            'join' => 'tbl_fact_cost_centre b on a.cost_centre = b.kode',
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

        $data['material'] = get_data('tbl_fact_material',[
            'where' => [
                'is_active' => 1,
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
                        // 'a.cost_centre' => '0000'
                    ],
                    'group_by' => 'a.cost_centre',
                    'sort_by' => 'id', 
                 ];

        if($cost_centre && $cost_centre != "ALL") $arr['where']['a.cost_centre'] =$cost_centre;


        $data['grup'][0]= get_data('tbl_fact_product a',$arr)->result();
        $data['total_biaya'] = [];
        foreach($data['grup'][0] as $m0) {	

           
            $data['produk'][$m0->id]= get_data('tbl_fact_product_ovh a',[
                'select' => 'a.product_code,a.qty_production,(a.direct_labour+d.direct_labour) as direct_labour,(a.utilities+d.utilities) as utilities
                            ,(a.supplies+d.supplies) as supplies, (a.indirect_labour+d.indirect_labour) as indirect_labour
                            ,(a.repair+d.repair) as repair, (a.depreciation+d.depreciation) as depreciation,
                            ,(a.rent+d.rent) as rent, (a.others+d.others) as others
                            ,b.product_name,b.destination, c.abbreviation as initial, c.cost_centre, c.kode
                            ,e.bottle,e.content,e.packing,e.set,e.subrm_total',
                'join' =>  ['tbl_fact_allocation_qc d on a.tahun = d.tahun and a.product_code = d.product_code',
                            'tbl_fact_product b on a.product_code = b.code',
                            'tbl_fact_cost_centre c on a.id_cost_centre = c.id type LEFT',
                            'tbl_unit_material_cost e on a.tahun = e.tahun and a.product_code = e.product_code type LEFT'
                           ],
                'where' => [
                    'a.tahun' => $tahun,
                    'd.tahun' => $tahun,
                    'a.id_cost_centre' =>$m0->id,
                    'a.qty_production !=' => 0,
                    '__m' => 'a.product_code in (select budget_product_code from tbl_beginning_stock where is_active ="1" and tahun="'.$tahun.'")',
                ],
                'sort_by' => 'a.id_cost_centre'
            ])->result();
            
            $n1 = [];
            $new_alloc = get_data('tbl_new_allocation_product',[
                'where' => [
                    'tahun' => $tahun,
                    'account_code' => '736'
                ]
            ])->result();
            
            if($new_alloc) {
                foreach($new_alloc as $n){
                    $n1[$n->product_code] = $n->nilai_akun_current;
                }
            }

            $new_alloc2 = get_data('tbl_add_alloc_product',[
                'where' => [
                    'tahun' => $tahun,
                    'account_code' => '736'
                ]
            ])->row();
            
            if($new_alloc2) $n1[$new_alloc2->product_code] = $new_alloc2->jumlah_penyesuaian;

            $data['depr'] = $n1;
            // debug($data);die;

            // $biaya = get_data('tbl_fact_manex_allocation a',[
            //     'select' => 'a.manex_account,sum(total) as total',
            //     'join' => 'tbl_fact_cost_centre b on a.cost_centre = b.kode',
            //     'where' => [
            //         'a.tahun' => $tahun ,
            //         'b.id' => $m0->id
            //     ],
            //     'group_by' => 'a.manex_account'
            // ])->result();

     

            // foreach($biaya as $b) {
            //     $data['total_biaya'][$m0->kode][$b->manex_account] = $b->total; 
            // }

            // debug($data['total_biaya'][$m0->kode]);die;

        }

        $data['variable'] = get_data('tbl_fact_manex_account a',[
            'select' => 'a.*',
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

    //    debug($total_biaya);die;
        $response	= array(
            'table'		=> $this->load->view('reporting/cogm/table',$data,true),
        );
	   
	    render($response,'json');
	}

    function save_overhead_unit() {
        $tahun = post('tahun');

        $table = 'tbl_budget_unitcogs_' . post('tahun');

        $ovh= get_data('tbl_fact_product_ovh a',[
            'select' => 'b.id as id_product, a.product_code,a.qty_production,(a.direct_labour+d.direct_labour) as direct_labour,(a.utilities+d.utilities) as utilities
                        ,(a.supplies+d.supplies) as supplies, (a.indirect_labour+d.indirect_labour) as indirect_labour
                        ,(a.repair+d.repair) as repair, (a.depreciation+d.depreciation) as depreciation,
                        ,(a.rent+d.rent) as rent, (a.others+d.others) as others, e.subrm_total,
                        ,b.product_name,b.destination, b.product_line, b.divisi, b.sub_product, b.product_name as description, c.abbreviation as initial, c.cost_centre, c.kode',
            'join' =>  ['tbl_fact_allocation_qc d on a.tahun = d.tahun and a.product_code = d.product_code',
                        'tbl_fact_product b on a.product_code = b.code',
                        'tbl_fact_cost_centre c on a.id_cost_centre = c.id type LEFT',
                        'tbl_unit_material_cost e on a.tahun = e.tahun and a.product_code = e.product_code'
                       ],
            'where' => [
                'a.tahun' => $tahun,
                'd.tahun' => $tahun,
                'a.qty_production !=' => 0
            ],
            'sort_by' => 'a.id_cost_centre'
        ])->result();

        foreach($ovh as $u) {
            $arr            = [
                'select'    => 'a.*',
                'where'     => [
                    'a.id_budget_product' => $u->id_product,
					'a.budget_product_code' => $u->product_code,
					'a.tahun' => $tahun,
                ],
            ];

			$cek1 = get_data($table . ' a',$arr)->row();

 
  
			if(isset($cek1->budget_product_code)) {	 

				$field1 = "";
                $total_ovh = 0;
				for ($i = 1; $i <= 12; $i++) { 
					$field1 = 'B_' . sprintf('%02d', $i);
                  
                    update_data($table,[$field1=>0],['budget_product_code'=>$cek1->budget_product_code]);


                    $total_ovh = ($u->direct_labour+$u->utilities+$u->supplies+$u->indirect_labour+$u->repair+$u->depreciation+$u->rent+$u->others) / $u->qty_production;
					$$field1 = $total_ovh + ($u->subrm_total != null ? $u->subrm_total :0) ;
                    // + $cek1->$field1;

					update_data($table,[$field1=>$$field1],['budget_product_code'=>$cek1->budget_product_code]);
				}
			}
            // else{

			// 	$sector = get_data('tbl_sector_price','is_active',1)->result();
			// 	foreach($sector as $s) {

			// 		$data_insert = [
			// 			'tahun' => $tahun,
			// 			'product_line' => $u->product_line,
			// 			'divisi' => $u->divisi,
			// 			'category' => $u->sub_product,
			// 			'id_budget_product' => $u->id_product,
			// 			'budget_product_code' => $u->product_code,
			// 			'budget_product_name' => $u->description,
			// 			'budget_product_sector' => $s->id

			// 		];

            //         $total_ovh = 0;
			// 		for ($i = 1; $i <= 12; $i++) { 
			// 			$field1 = 'B_' . sprintf('%02d', $i);
            //             $total_ovh = ($u->direct_labour+$u->utilities+$u->supplies+$u->indirect_labour+$u->repair+$u->depreciation+$u->rent+$u->others) / $u->qty_production;
			// 			$data_insert[$field1] = $total_ovh ;
			// 		}

			// 		insert_data($table,$data_insert);

			// 	}
			// }
        }

  

		render([
			'status'	=> 'success',
			'message'	=> 'Report Save Sukses'
		],'json');	

        
    }


}

