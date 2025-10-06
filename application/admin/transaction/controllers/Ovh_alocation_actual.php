<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');

class Ovh_alocation_actual extends BE_Controller {
    var $controller = 'ovh_alocation';
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

    function data($tahun = "", $bulan ="", $cost_centre="" , $tipe = 'table') {
  
        $arr = [
                'select' => 'a.cost_centre as kode, b.id, b.cost_centre',
                'join' => 'tbl_fact_cost_centre b on a.cost_centre = b.kode type LEFT',
                'where' => [
                    'a.is_active' => 1,
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
                $cek = get_data('tbl_fact_product_ovh_actual a',[
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
                    insert_data('tbl_fact_product_ovh_actual',
                    ['tahun' => $tahun, 'bulan' => $bulan, 'id_product'=>$p->id, 'product_code'=>$p->code, 'id_cost_centre' => $p->id_cost_centre]
                );
                }
            }

            $data['produk'][$m0->id]= get_data('tbl_fact_product_ovh_actual a',[
                'select' => 'a.*,b.product_name,b.destination, c.abbreviation as initial, c.cost_centre',
                'join' =>  ['tbl_fact_product b on a.product_code = b.code',
                            'tbl_fact_cost_centre c on a.id_cost_centre = c.id type LEFT',
                           ],
                'where' => [
                    'a.tahun' => $tahun,
                    'a.bulan' => $bulan,
                    'a.id_cost_centre' =>$m0->id,
                    'a.qty_production !=' => 0
                ],
                'sort_by' => 'a.id_cost_centre'
            ])->result();

        }

    //    debug($data['produk']);die;
        $response	= array(
            'table'		=> $this->load->view('transaction/ovh_alocation_actual/table',$data,true),
        );
	   
	    render($response,'json');
	}


    function save_perubahan() {       
        $data   = json_decode(post('json'),true);

        foreach($data as $id => $record) {
            $result = $record;
            foreach ($result as $r => $v) {               
                update_data('tbl_fact_product_ovh_actual', $result,'id',$id);
            }

            $upd = get_data('tbl_fact_product_ovh_actual', 'id',$id)->row();
            $manwh_total = 0;
            $macwh_total = 0;

            if($upd->manwh_productivity > 0 && $upd->qty_production > 0) $manwh_total = $upd->qty_production / $upd->manwh_productivity ;
            if($upd->macwh_productivity > 0 && $upd->qty_production > 0) $macwh_total = $upd->qty_production / $upd->macwh_productivity ;

            update_data('tbl_fact_product_ovh_actual',['manwh_total' => $manwh_total,'macwh_total' => $macwh_total],'id',$upd->id);

        }
    } 

    function import() {
		ini_set('memory_limit', '-1');

        $table = 'tbl_fact_product_ovh_actual';
        $tahun = post('tahun') ;
        $bulan = post('bulan_import') ;
   
		$file = post('fileimport');
		$col = ['description','code','factory', 'quantity','manworking','machineworking', 'total_manwh', 'total_machinewh'];

		$this->load->library('simpleexcel');
		$this->simpleexcel->define_column($col);
		$jml = $this->simpleexcel->read($file);

		$c = 0;
        $save = 0;
		foreach($jml as $i => $k) {
			if($i==0) {
				for($j = 11; $j <= $k; $j++) {
					$data = $this->simpleexcel->parsing($i,$j);

                    $cek = get_data($table,[
                        'where' => [
                            'tahun' => $tahun,
                            'bulan' => $bulan,
                            'product_code' => $data['code'],
                            // 'product_code' => 'CIHODD5PDM',
                        ],
                    ])->row();
                    if(isset($cek->id)) {
                        $data2['qty_production'] = str_replace([','],'',$data['quantity']) ;
                        $data2['manwh_productivity'] = str_replace([','],'',$data['manworking']) ;
                        $data2['macwh_productivity'] = str_replace([','],'',$data['machineworking']);

                        if(str_replace([','],'',$data['quantity']) != 0 && str_replace([','],'',$data['manworking']) != 0){
                            $data2['manwh_total'] = (str_replace([','],'',$data['quantity']) / str_replace([','],'',$data['manworking'])) ;
                        }else{
                            $data2['manwh_total'] = 0;
                        }
                        
                        if(str_replace([','],'',$data['quantity']) != 0 && str_replace([','],'',$data['machineworking']) != 0){
                            $data2['macwh_total'] = (str_replace([','],'',$data['quantity']) / str_replace([','],'',$data['machineworking'])) ;
                        }else{
                            $data2['macwh_total'] = 0;
                        }
                       
                        $data2['update_at'] = date('Y-m-d H:i:s');
                        $data2['update_by'] = user('nama');
                    
					    $save = update_data($table,$data2,['id'=>$cek->id]);					
                    }

                    if($save) $c++;
				}
			}
		}

		
        $upd = get_data($table . ' a',[
            'select' => 'a.*,b.cost_centre',
            'join' =>  'tbl_fact_product b on a.product_code = b.code',
            'where' => [
                'tahun' => $tahun,
            ],
        ])->result();


        foreach($upd as $u) {
            $prsnmanwh = 0;
            $prsnmacwh = 0;
            $sum = get_data($table . ' a',[
                'select' => 'b.cost_centre,sum(a.manwh_total) as sum_manwh, sum(a.macwh_total) as sum_macwh ',
                'join' =>  'tbl_fact_product b on a.product_code = b.code',
                'where' => [
                    'a.tahun' => $tahun,
                    'b.cost_centre' => $u->cost_centre,
                ],
            ])->row();

            if($u->manwh_total > 0 && $sum->sum_manwh > 0) $prsnmanwh = ($u->manwh_total / $sum->sum_manwh) ;
            if($u->macwh_total > 0 && $sum->sum_macwh > 0) $prsnmacwh = ($u->macwh_total / $sum->sum_macwh) ;

            update_data($table,['manwh_prsn' => $prsnmanwh, 'macwh_prsn' => $prsnmacwh],['id'=>$u->id]);
        }

        $response = [
			'status' => 'success',
			'message' => $c.' '.lang('data_berhasil_disimpan').'.'
		];
		@unlink($file);
		render($response,'json');
    }

    function proses_rounding($tahun="", $cost_centre="") {
        ini_set('memory_limit', '-1');
		ini_set('max_execution_time', 0);

        $table = 'tbl_fact_product_ovh_actual';

        $arr = [
            'select' => 'a.*,b.cost_centre',
            'join' =>  'tbl_fact_product b on a.product_code = b.code',
            'where' => [
                'a.tahun' => $tahun,
                'a.qty_production !=' => 0
            ],
        ];

        if(!empty($cost_centre) && $cost_centre != 'ALL') $arr['where']['b.cost_centre'] = $cost_centre ;

        $upd = get_data($table . ' a',$arr)->result();



        foreach($upd as $u) {


            $manwh_total = 0;
            $macwh_total = 0;

            if($u->manwh_productivity > 0 && $u->qty_production > 0) $manwh_total = round($u->qty_production / $u->manwh_productivity,15) ;
            if($u->macwh_productivity > 0 && $u->qty_production > 0) $macwh_total = round($u->qty_production / $u->macwh_productivity,15) ;

            update_data('tbl_fact_product_ovh_actual',['manwh_total' => $manwh_total,'macwh_total' => $macwh_total],'id',$u->id);


            $prsnmanwh = 0;
            $prsnmacwh = 0;
            $sum = get_data($table . ' a',[
                'select' => 'b.cost_centre,sum(a.manwh_total) as sum_manwh, sum(a.macwh_total) as sum_macwh ',
                'join' =>  'tbl_fact_product b on a.product_code = b.code',
                'where' => [
                    'a.tahun' => $tahun,
                    'b.cost_centre' => $u->cost_centre,
                    'a.qty_production !=' => 0,
                ],
            ])->row();

            if($u->manwh_total > 0 && $sum->sum_manwh > 0) $prsnmanwh = round($u->manwh_total / $sum->sum_manwh, 18) ;
            if($u->macwh_total > 0 && $sum->sum_macwh > 0) $prsnmacwh = round($u->macwh_total / $sum->sum_macwh, 18) ;

            update_data($table,['manwh_prsn' => $prsnmanwh, 'macwh_prsn' => $prsnmacwh],['id'=>$u->id]);
        }

        
		render([
			'status'	=> 'success',
			'message'	=> 'Rounding Sukses'
		],'json');	

        // echo 'success update ' .$jum . ' data' ;

    }

    function update_cost_centre($tahun="") {
        $prod = get_data('tbl_fact_product_ovh_actual a',[
            'select' => 'a.*, c.kode as cost_centre, b.id_cost_centre',
            'join' => ['tbl_fact_product b on a.product_code = b.code type LEFT',
                       'tbl_fact_cost_centre c on b.id_cost_centre = c.id type LEFT',
                      ],
            'where' => [
                'tahun' => $tahun,
            ],
        ])->result();

        foreach($prod as $p) {
            update_data('tbl_fact_product_ovh_actual',['id_cost_centre' => $p->id_cost_centre],['product_code'=>$p->product_code,'tahun'=>$tahun]);
        }
        echo 'success' ;
    }

}

