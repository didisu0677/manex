<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');

class Allocation_qc_actual extends BE_Controller {
    var $controller = 'allocation_qc';
    function __construct() {
        parent::__construct();
    }
    
    function index() {      
        $data['factory'] = get_data('tbl_fact_cost_centre',[
            'where' => [
                'is_active' => 1,
                // 'id_fact_department' => 2,
            ],
        ])->result();
        $data['tahun'] = get_data('tbl_fact_tahun_budget', 'is_active',1)->result();   

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
        $data['access'] = $access;
        $data['access_additional']  = $access['access_additional'];

        render($data);
    }
    
    function sortable() {
        render();
    }

    function data($tahun = "" , $bulan = "" , $cost_centre = "" , $tipe = 'table') {

        $arr = [
                'select' => 'a.cost_centre as kode, b.id, b.cost_centre',
                'join' => 'tbl_fact_cost_centre b on a.cost_centre = b.kode type LEFT',
                'where' => [
                    'a.is_active' => 1,
                    'a.id_cost_centre !=' => 0,
                ],
                'group_by' => 'a.id_cost_centre',
                'sort_by' => 'id', 
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
                $cek = get_data('tbl_fact_allocation_qc_actual a',[
                    'select' => 'a.*,b.id_cost_centre',
                    'join' => 'tbl_fact_product b on a.product_code = b.code',
                    'where' => [
                        'a.tahun' => $tahun,
                        'a.bulan' => $bulan,
                        'a.id_product' => $p->id,
                        'a.product_code' => $p->code,
                    ]
                ])->row();
                if(!isset($cek->id)){
                    insert_data('tbl_fact_allocation_qc_actual',
                    ['tahun' => $tahun, 'bulan' => $bulan, 'id_product'=>$p->id, 'product_code'=>$p->code, 'id_cost_centre' => $p->id_cost_centre]
                );
                }
            }

            $data['produk'][$m0->id]= get_data('tbl_fact_allocation_qc_actual a',[
                'select' => 'a.*,b.product_name,b.destination, c.abbreviation as initial, c.cost_centre',
                'join' =>  ['tbl_fact_product b on a.product_code = b.code',
                            'tbl_fact_cost_centre c on a.id_cost_centre = c.id type LEFT',
                           ],
                'where' => [
                    'a.tahun' => $tahun,
                    'a.bulan' => $bulan,
                    'a.id_cost_centre' =>$m0->id
                ],
                'sort_by' => 'a.id_cost_centre'
            ])->result();

            $this->isi_budget_production_actual($tahun) ;

        }

        $response	= array(
            'table'		=> $this->load->view('transaction/allocation_qc_actual/table',$data,true),
        );
	   
	    render($response,'json');
	}

    function save_perubahan() {           
        $data   = json_decode(post('json'),true);
        foreach($data as $id => $record) {
            $result = $record;
            foreach ($result as $r => $v) 
                update_data('tbl_fact_allocation_qc_actual', $result,'id',$id);
        }
    } 

    function import() {
		ini_set('memory_limit', '-1');

        $table = 'tbl_fact_allocation_qc_actual';
        $tahun = post('tahun') ;
        $bulan = post('bulan_import') ;

        $field = 'B_' . sprintf('%02d', $bulan);
   
		$file = post('fileimport');
		$col = ['description','code','factory', 'quantity', 'pointunit', 'total_point', 'prsn_aloc'];

		$this->load->library('simpleexcel');
		$this->simpleexcel->define_column($col);
		$jml = $this->simpleexcel->read($file);

		$c = 0;
        $save = 0;
		foreach($jml as $i => $k) {
			if($i==0) {
				for($j = 9; $j <= $k; $j++) {
					$data = $this->simpleexcel->parsing($i,$j);

                    $cek = get_data($table,[
                        'where' => [
                            'tahun' => $tahun,
                            'bulan' => $bulan,
                            'product_code' => $data['code'],
                        ],
                    ])->row();

                    if(isset($cek->id)) {
                        $data2['product_qty'] = str_replace([','],'',$data['quantity']) ;
                        $data2['point_perunit'] = str_replace([','],'',$data['pointunit']) ;
                        $data2['total_point'] = floatval(str_replace([','],'',@$data['quantity']??0)) * floatval(str_replace([','],'',@$data['pointunit']??0)) ;
                        $data2['update_at'] = date('Y-m-d H:i:s');
                        $data2['update_by'] = user('nama');
                    
					    $save = update_data($table,$data2,['id'=>$cek->id]);		
                        if($save) {
                            update_data('tbl_budget_production_actual',
                                [$field => str_replace([','],'',$data['quantity'])],
                                ['tahun'=>$tahun,'budget_product_code'=>$data['code']]);  
                        }					
                    }

                    if($save) $c++;
				}
			}
		}

        $sum = get_data($table,[
            'select' => 'sum(total_point) as total_point',
            'where' => [
                'tahun' => $tahun,
            ],
        ])->row();
		
        $upd = get_data($table,[
            'where' => [
                'tahun' => $tahun,
            ],
        ])->result();

        foreach($upd as $u) {
            $prsn_aloc = 0 ;
            if($u->total_point != 0 && $sum->total_point != 0) {
                $prsn_aloc = ($u->total_point / $sum->total_point) * 100 ;
            }
            update_data($table,['prsn_aloc' => $prsn_aloc],['id'=>$u->id]);
        }
		
		$response = [
			'status' => 'success',
			'message' => $c.' '.lang('data_berhasil_disimpan').'.'
		];
		@unlink($file);
		render($response,'json');
	}

    function proses_rounding($tahun="") {
        ini_set('memory_limit', '-1');
		ini_set('max_execution_time', 0);

        $table = 'tbl_fact_allocation_qc_actual';
        $bulan = post('bulan') ;

        update_data('tbl_fact_allocation_qc_actual',['point_perunit' =>0, 'total_point' => 0],['tahun' => $tahun, 'product_qty' => 0]);

        $sum = get_data($table,[
            'select' => 'sum(total_point) as total_point',
            'where' => [
                'tahun' => $tahun,
                'bulan' => $bulan,
                'total_point !=' => 0,
            ],
        ])->row();
        
        $upd = get_data($table,[
            'where' => [
                'tahun' => $tahun,
                'bulan' => $bulan,
                'total_point !=' => 0,
            ],
        ])->result();

        foreach($upd as $u) {
            update_data($table,['total_point' => $u->product_qty * $u->point_perunit,'prsn_aloc' => round(($u->total_point / $sum->total_point),10) * 100],['id'=>$u->id]);
        }

        
		render([
			'status'	=> 'success',
			'message'	=> 'Rounding Sukses'
		],'json');	

        // echo 'success update ' .$jum . ' data' ;

    }

    function isi_budget_production_actual($tahun="") {
        ini_set('memory_limit', '-1');
        ini_set('max_execution_time', 0);

        $table = 'tbl_budget_production_actual';

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

        }
    }
}

