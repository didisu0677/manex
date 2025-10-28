<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');

class Aloc_service_actual extends BE_Controller {
    var $controller = 'aloc_service';
    function __construct() {
        parent::__construct();
    }
    
    function index() {
        $data['tahun'] = get_data('tbl_fact_tahun_budget', 'is_active',1)->result();   
        $data['cc_allocation'] = get_data('tbl_fact_ccallocation', [
            'where' => [
                'is_active' => 1
            ],
            'sort_by' => 'urutan'
            ])->result(); 
            
        $access         = get_access($this->controller);
        $data['access'] = $access;
        $data['access_additional']  = $access['access_additional'];
        render($data);
    }
    
    function sortable() {
        render();
    }

    function data($tahun= "", $bulan="", $cc_allocation ="", $tipe = 'table') {
        $arr            = [
	        'select'	=> 'b.*',
            'join'      => 'tbl_fact_cost_centre b on a.cost_centre = b.kode type LEFT',
	        'where'     => [
	            'b.is_active' => 1,
                'a.id_ccallocation' => $cc_allocation,
                // 'a.id' => 4
	        ],
	    ];

        $cc = get_data('tbl_fact_ccallocation_detail a',$arr)->result();
        foreach($cc as $c) {
            $cek = get_data('tbl_fact_alocation_service_actual a',[
                'select' => 'a.*,b.prsn_aloc as prsn_aloc1',
                'join' => 'tbl_fact_alocation_service b on a.id_ccallocation = b.id_ccallocation and a.cost_centre = b.cost_centre and a.id_cost_centre = b.id_cost_centre and a.tahun = b.tahun type LEFT',
                'where' => [
                    'a.tahun' => $tahun,
                    'a.bulan' => $bulan,
                    'a.id_ccallocation' => $cc_allocation,
                    'a.id_cost_centre' => $c->id,
                    'a.cost_centre' => $c->kode,
                ]
            ])->row();

            if(isset($cek->id_ccallocation) && $cek->id_ccallocation == '4') {
                $prsn_aloc = @$cek->prsn_aloc1??0;
            } else {
                $prsn_aloc = @$cek->prsn_aloc??0;
            }

            $data_insert = [
                'tahun' => $tahun,
                'bulan' => $bulan,
                'id_ccallocation' => $cc_allocation,
                'id_cost_centre' => $c->id,
                'cost_centre' => $c->kode,
                'prsn_aloc' => $prsn_aloc,
            ];

            if(!isset($cek->id)) {
                insert_data('tbl_fact_alocation_service_actual',$data_insert);
            }else{
                update_data('tbl_fact_alocation_service_actual',$data_insert,'id',$cek->id);
            }
        }

	    $data['factory']= get_data('tbl_fact_alocation_service_actual a',[
            'select' => 'a.*,b.cost_centre as cost_centre_name',
            'join' => 'tbl_fact_cost_centre b on a.id_cost_centre = b.id',
            'where' => [
                'a.tahun' => $tahun,
                'a.bulan' => $bulan,
                'a.id_ccallocation' => $cc_allocation
            ]
        ])->result();

        // debug($data['grup'][0]);die;


       
    //    debug($data['produk']);die;
        $response	= array(
            'table'		=> $this->load->view('transaction/aloc_service_actual/table',$data,true),
        );
	   
	    render($response,'json');
	}


    function save_perubahan() {           
        $data   = json_decode(post('json'),true);
        foreach($data as $id => $record) {
            $result = $record;
            foreach ($result as $r => $v) 
                update_data('tbl_fact_alocation_service_actual', $result,'id',$id);
        }
    } 

    function proses() {
        ini_set('memory_limit', '-1');
		ini_set('max_execution_time', 0);

        $tahun = post('tahun') ;
        $tahun_budget = user('tahun_budget') ;
        $bulan = post('bulan') ;
        $field_est = 'EST_' . sprintf('%02d', $bulan);
        $field_b = 'B_' . sprintf('%02d', $bulan);

        $table0 = 'tbl_fact_lstbudget_' .$tahun_budget ;
        $table = 'act_tbl_fact_lstbudget_' .$tahun ;
        $source = get_data('tbl_fact_ccallocation',[
            'where' => [
                'id' => post('id_allocation'),
                'is_active' => 1
            ],
        ])->row();


        $cc_source =[];
        if(isset($source->id)) $cc_source = json_decode($source->source_allocation) ;

        if(count($cc_source)) {
            // Debug: Cek total source data
            $total_source = 0;
            $debug_info = [];
            foreach($cc_source as $c) {
                $source_sum = get_data($table0 . ' a',[
                    'select' => 'sum(a.'.$field_est.') as total_est, count(*) as count_records',
                    'where' => ['a.cost_centre' => $c],
                ])->row();
                $total_source += $source_sum->total_est;
                $debug_info[] = "CC: $c = " . number_format($source_sum->total_est) . " (records: {$source_sum->count_records})";
            }
            
            // Validasi total persentase alokasi 
            $alloc_data = get_data('tbl_fact_alocation_service_actual',[
                'select' => 'cost_centre, prsn_aloc, sum(prsn_aloc) as total_prsn',
                'where' => [
                    'tahun' => $tahun,
                    'bulan' => $bulan,
                    'id_ccallocation' => $source->id,
                ],
                'group_by' => 'cost_centre, prsn_aloc'
            ])->result();

            $total_prsn = 0;
            $alloc_info = [];
            foreach($alloc_data as $ad) {
                $total_prsn += $ad->prsn_aloc;
                $alloc_info[] = "CC: {$ad->cost_centre} = {$ad->prsn_aloc}%";
            }

            // Tampilkan debug info bahkan jika persentase benar
            render([
                'status' => 'debug',
                'message' => 'DEBUG INFO:<br/>' . 
                           'Source Table: ' . $table0 . '<br/>' .
                           'Target Table: ' . $table . '<br/>' .
                           'Field EST: ' . $field_est . '<br/>' .
                           'Field B: ' . $field_b . '<br/>' .
                           'Total Source: ' . number_format($total_source) . '<br/>' .
                           'Source Details: ' . implode(', ', $debug_info) . '<br/>' .
                           'Total Persentase: ' . $total_prsn . '%<br/>' .
                           'Allocation Details: ' . implode(', ', $alloc_info) . '<br/>' .
                           'CC Sources: ' . implode(', ', $cc_source)
            ],'json');
            return;

            // Delete data dengan kondisi yang spesifik untuk id_ccallocation
            delete_data($table,'id_ccallocation',post('id_allocation'));

            foreach($cc_source as $c) {
                $sum = get_data($table0 . ' a',[
                    'select' => 'a.cost_centre,a.id_cost_centre,a.sub_account,a.account_code,a.id_account,a.account_name,
                          sum(a.'.$field_est.') as "'.$field_est.'", sum(a.total_budget) as total_budget',
                     'where' => [
                        'a.cost_centre' => $c,
                    ],
                    'group_by' => 'a.cost_centre,a.id_cost_centre,a.sub_account,a.account_code,a.id_account'
                ])->result();   

                
                if(count($sum)) {
                     foreach($sum as $s) {
                        $alloc = get_data('tbl_fact_alocation_service_actual',[
                            'where' => [
                                'tahun' => $tahun,
                                'bulan' => $bulan,
                                'id_ccallocation' => $source->id,
                            ],
                        ])->result();

                        foreach($alloc as $a){
                            $data2['tahun'] = $tahun;
                            $data2['id_ccallocation'] = $source->id;
                            $data2['id_cost_centre'] = $a->id_cost_centre;
                            $data2['cost_centre'] = $a->cost_centre;
                            $data2['prsn_aloc'] = $a->prsn_aloc;
                            $data2['sub_account'] = $s->sub_account;
                            $data2['id_account'] = $s->id_account;
                            $data2['account_code'] = $s->account_code;
                            $data2['account_name'] = $s->account_name;                   
                            $data2[$field_b] = $s->$field_est * ($a->prsn_aloc/100);
                            $data2['total_budget'] = $s->total_budget * ($a->prsn_aloc/100);        
                            insert_data($table,$data2);
                        }
                    }
                }
            }
        }

        render([
			'status'	=> 'success',
			'message'	=> 'Allocation Process Successfuly'
		],'json');	

    }
}

