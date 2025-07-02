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

        $data['submit'] = FALSE ;

		$s = get_data('tbl_scm_submit',[
            'where' => [
                'code_submit' => 'COST',
                'is_submit' => 1,
                'tahun' => user('tahun_budget')
            ],
		])->row();
		
		if(isset($s->id)) {
			$data['submit'] = TRUE ;
		}

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

    function submit_report() {
        $tahun = post('tahun');

        $cost = get_data('tbl_material_formula a',[
            'select'	=> 'd.id as id_product, a.parent_item, a.item_name, a.component_item, a.material_name, 
                            a.total as quantity, a.um, a.group_formula, b.bm, 
                            b.bank_charges, b.handling_charges, b.price_us ,b.curr, c.rates, c.ppn, c.pph, (b.price_us * c.rates) as total_price,
                            e.total_budget as qty_production',
            'join' => ['tbl_material_price b on a.component_item = b.material_code and b.year="'.$tahun.'" type LEFT ',
                    'tbl_currency_rates c on b.curr = c.curr type LEFT',
                    'tbl_fact_product d on a.parent_item = d.code type LEFT',
                    'tbl_budget_production e on a.parent_item = e.budget_product_code and e.tahun ="'.$tahun.'" type LEFT'
                    ],
            'where'		=> [
                '__m' => 'a.parent_item in (select budget_product_code from tbl_beginning_stock where is_active = 1 and tahun="'.$tahun.'")',
                'a.tahun' => $tahun,
                // 'a.parent_item' => 'CIKRTRUNDM',
                // 'a.group_formula' => 'B',
                ],
            'group_by' => 'a.parent_item,a.component_item',
            'sort_by' => 'a.parent_item'
        ])->result();


        delete_data('tbl_unit_material_cost',['tahun' => $tahun]) ;
        $price_budget = 0;
        $bm_amt = 0;
        $pph = 0;
        $ppn = 0;
        foreach ($cost as $c) {
            $cek = get_data('tbl_unit_material_cost',[
                'where' => [
                    'tahun' => $tahun,
                    'product_code' => $c->parent_item
                ]
            ])->row();

            $bm_amt = $c->total_price * ($c->bm/100);
            $pph = ($bm_amt + $c->total_price) * ($c->pph/100);
            $ppn = ($bm_amt + $c->total_price) * ($c->ppn/100);
            $price_budget = $c->total_price + $bm_amt + $c->bank_charges + $c->handling_charges  ;
            
            $data = [
                'tahun' => $tahun,
                'id_product' => $c->id_product,
                'product_code' => $c->parent_item,
                'description' => $c->item_name,
                'qty_production' => (isset($c->qty_production) ? $c->qty_production : 0),
                // 'bottle' => 0,
                // 'content' => 0,
                // 'packing' => 0,
                // 'set' => 0,
                'is_active' => 1
            ];

            if($c->group_formula == 'A'){
                $data['bottle'] = round($price_budget,5) * round($c->quantity,5);
            }elseif($c->group_formula == 'B'){
                $data['content'] = round($price_budget,5) * round($c->quantity,5);
            }elseif($c->group_formula == 'C') {
                $data['packing'] = round($price_budget,5) * round($c->quantity,5);
            }elseif($c->group_formula == 'C') {
                $data['set'] = round($price_budget,5) * round($c->quantity,5);
            }

            // $data['subrm_total'] = @$data['bottle'] + @$data['content'] + @$data['packing'] + @$data['set'];

            
            if(!isset($cek->product_code)) {
                insert_data('tbl_unit_material_cost',$data);
            }else{
                // $data['bottle'] = $cek->bottle ;
                // $data['content'] = $cek->content;
                // $data['packing'] = $cek->packing;
                // $data['set'] = $cek->set;
                if($c->group_formula == 'A'){
                    $data['bottle'] = $cek->bottle + (round($price_budget,5) * round($c->quantity,5));
                }elseif($c->group_formula == 'B'){
                    $data['content'] = $cek->content + (round($price_budget,5) * round($c->quantity,5));
                }elseif($c->group_formula == 'C') {
                    $data['packing'] = $cek->packing + (round($price_budget,5) * round($c->quantity,5));
                }elseif($c->group_formula == 'D') {
                    $data['set'] = $cek->set + (round($price_budget,5) * round($c->quantity,5));
                }

                // $data['subrm_total'] = $cek->subrm_total + (@$data['bottle'] + @$data['content'] + @$data['packing'] + @$data['set']);
                update_data('tbl_unit_material_cost',$data,['id'=>$cek->id]);
            }

            $this->db->set('subrm_total', '(`bottle` + `content` + `packing` + `set`)', FALSE);
            $this->db->where('product_code', $c->parent_item);  // Replace $x with your actual product code value
            $this->db->where('tahun', $tahun);         // Replace $y with your actual year value
            $this->db->update('tbl_unit_material_cost');

        }


        delete_data('tbl_scm_submit', ['code_submit'=>'COST','tahun'=>$tahun]);

        insert_data('tbl_scm_submit',[
            'tahun' => $tahun,
            'code_submit' => 'COST',
            'is_submit' => 1,
            'is_active' => 1
        ]);

        render([
			'status'	=> 'success',
			'message'	=> 'Data Price Submit has been succesfuly'
		],'json');	

    }
   

}