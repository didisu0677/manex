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

        $total_source = 0;
        $total_alokasi = 0;
        $log_sum = [];
        $log_alloc = [];
        $log_prsn_aloc = [];
        $log_detail = [];

        // Hitung total prosentase alokasi per cost centre
        $prsn_aloc_map = [];
        if(count($cc_source)) {
            $alloc_all = get_data('tbl_fact_alocation_service_actual', [
                'where' => [
                    'tahun' => $tahun,
                    'bulan' => $bulan,
                    'id_ccallocation' => $source->id,
                ],
            ])->result();
            foreach ($alloc_all as $row) {
                $cc = $row->cost_centre;
                if (!isset($prsn_aloc_map[$cc])) $prsn_aloc_map[$cc] = 0;
                $prsn_aloc_map[$cc] += $row->prsn_aloc;
            }

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
                        if(count($log_sum) < 5) $log_sum[] = (array)$s;
                        $total_source += $s->$field_est;
                        $a = get_data('tbl_fact_alocation_service_actual',[
                            'where' => [
                                'tahun' => $tahun,
                                'bulan' => $bulan,
                                'id_ccallocation' => $source->id,
                                // 'cost_centre' => '2110',
                                'prsn_aloc >' => 0,
                            ],
                        ])->row();
                        $prsn_aloc_val = $a ? $a->prsn_aloc : null;
                        $hasil_kali = ($prsn_aloc_val !== null) ? ($s->$field_est * ($prsn_aloc_val / 100)) : null;
                        $log_detail[] = [
                            'cost_centre' => $s->cost_centre,
                            'field_est' => $s->$field_est,
                            'prsn_aloc' => $prsn_aloc_val,
                            'hasil_kali' => $hasil_kali,
                            'alokasi' => $a ? ($s->$field_est * ($a->prsn_aloc / 100)) : 0
                        ];
                        if($a) {
                            if(count($log_alloc) < 5) $log_alloc[] = (array)$a;
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
                            $total_alokasi += $data2[$field_b];
                            insert_data($table,$data2);
                        }
                    }
                }
            }
        }

        // Siapkan log prosentase alokasi per cost centre
        foreach ($prsn_aloc_map as $cc => $prsn) {
            $log_prsn_aloc[] = [
                'cost_centre' => $cc,
                'total_prsn_aloc' => $prsn
            ];
        }

        render([
			'status'	=> 'success',
			'message'	=> 'Allocation Process Successfully. Total Source: ' . number_format($total_source, 0, ',', '.') . ', Total Alokasi: ' . number_format($total_alokasi, 0, ',', '.') . '. Log aktif, cek log_sum, log_alloc, log_prsn_aloc, log_detail.',
            'log_sum' => $log_sum,
            'log_alloc' => $log_alloc,
            'log_prsn_aloc' => $log_prsn_aloc,
            'log_detail' => $log_detail
		],'json');	

    }
}

