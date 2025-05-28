<?php defined('BASEPATH') OR exit('No direct script access allowed');
// Create by MW 20201201

function check_min_value($v,$x){
	$val = kali_minus($v,$x);
	// $val = custom_format($val);
	$val = custom_format(view_report($val));
	return $val;
}
function check_value($v){
	// $val = kali_minus($v,$x);
	$val = custom_format(view_report($v));
	return $val;
}

function remove_spaces($val){
	return preg_replace('/^\p{Z}+|\p{Z}+$/u', '', $val);
}

function checkRealisasiKolektibilitas($p1,$data){
	if($p1['tahun'] != $p1['tahun_core']):
		$key = multidimensional_search($data, array(
			'tahun_core' => $p1['tahun_core'],
			'id_kolektibilitas' => $p1['id'],
			'parent_index' => $p1['cabang'],
		));
		$d = $data[$key];
	else:
		$key = multidimensional_search($data, array(
			'tahun_core' => $p1['tahun_core'],
			'id_kolektibilitas' => $p1['id'],
			'parent_index' => '0'
		));
		$d = $data[$key];
	endif;
	return $d;
}

function checkMonthAnggaran($anggaran){
	$bulan 	= sprintf('%02d', $anggaran->bulan_terakhir_realisasi);
	$date 	= "01-".$bulan.'-'.$anggaran->tahun_terakhir_realisasi;
	return minusMonth($date,1);
}

function minusMonth($date,$minus){
	$date = date("m-Y", strtotime($date." -".$minus." months"));
	return $date;
}

function insert_formula_kolektibilitas($data,$anggaran){
	$d=[];
	$table = 'tbl_formula_kolektibilitas';
	foreach ($data as $k => $v) {
		$x 		= explode("-", $k);
		$coa 	= $x[0];
		$thn 	= $x[1];
		$sumber_data = $x[2];
		$cabang = $x[3];

		$h = [
			'coa' => $coa,
		];
		$h['kode_anggaran'] 		= $anggaran->kode_anggaran;
		$h['tahun_anggaran'] 		= $anggaran->tahun_anggaran;
		$h['keterangan_anggaran'] 	= $anggaran->keterangan;
		$h['kode_cabang']			= $cabang;
		$h['tahun_core'] 			= $thn;
		$h['changed'] 				= '[]';
		// $h['sumber_data'] 			= $sumber_data;
		foreach ($v as $k2 => $v2) {
			$h[$k2] 					= $v2;
		}
		$ck = get_data($table,[
			'select'	=> 'id',
			'where'		=> "kode_anggaran = '$anggaran->kode_anggaran' and kode_cabang = '$cabang' and coa = '$coa' and tahun_core = '$thn'"
		])->result();
		if(count($ck)<=0):
			insert_data($table,$h);
		endif;
		$d[] = $h;
	}
	// render($d,'json');
}

function update_formula_kolektibilitas($data,$anggaran){
	$kode_anggaran 		= $anggaran->kode_anggaran;
	$tahun_anggaran 	= $anggaran->tahun_anggaran;
	$keterangan_anggaran 	= $anggaran->keterangan;
	$table = 'tbl_formula_kolektibilitas';
	foreach ($data as $k => $v) {
		$x 		= explode('-', $k);
		$id 	= $x[0];
		$coa 	= $x[1];
		$thn 	= $x[2];
		$sumber_data = $x[3];
		$cabang = $x[4];
		if(strlen(strpos($coa,'sumkol123'))>0):
			$ck = get_data($table,[
				'select'	=> 'id',
				'where' 	=> "coa = '$coa' and kode_cabang = '$cabang' and kode_anggaran = '$kode_anggaran' and tahun_core = '$thn'",
			])->result();
			if(count($ck)>0):
				$where = [
                    'coa' => $coa,
                    'tahun_core' => $thn,
                    'kode_cabang' => $cabang,
                    'kode_anggaran' => $kode_anggaran,
                ];
				update_data($table,$v,$where);
			else:
				$h = $v;
				$h['coa'] 					= $coa;
				$h['kode_anggaran'] 		= $anggaran->kode_anggaran;
				$h['tahun_anggaran'] 		= $anggaran->tahun_anggaran;
				$h['keterangan_anggaran'] 	= $anggaran->keterangan;
				$h['kode_cabang']			= $cabang;
				$h['tahun_core'] 			= $thn;
				$h['changed'] 				= '[]';
				insert_data($table,$h);
			endif;
		elseif(strlen(strpos($coa, '_total'))>0):
			$where = [
                'coa' => $coa,
                'tahun_core' => $thn,
                'kode_cabang' => $cabang,
                'kode_anggaran' => $kode_anggaran,
            ];
			update_data($table,$v,$where);
		else:
			update_data($table,$v,'id',$id);
		endif;
	}
}

function filter_money($val){
 	$value = str_replace('.', '', $val);
    $value = str_replace(',', '.', $value);
    if(strlen(strpos($value, '('))>0):
    	$value = str_replace('(', '', $value);
    	$value = str_replace(')', '', $value);
    	$value = '-'.$value;
    endif;
    return $value;
}

function parse_condition($condition){
    $val = '2 == 2 && 13 < 2';
    $condition = "return ".$val.";";
    $test = eval($condition);
    var_dump($test);
}

function arrSumberData(){
	return ['real' => 'Real'];
}

function bgEdit(){
	// return '#f3f088';
	if(setting('warna_inputan')):
		return setting('warna_inputan');
	else:
		return '#f3f088';
	endif;
}

function get_data_core($arr_coa,$arr_tahun_core,$column){
	$CI         = get_instance();
	// data core / history
    $data_core = [];
    foreach ($arr_tahun_core as $v) {
        $tbl_history = 'tbl_history_'.$v;
        $tbl_history_status = true;
        if(!$CI->db->table_exists($tbl_history)):
            $tbl_history_status = false;
        endif;
        if ($tbl_history_status && !$CI->db->field_exists($column, $tbl_history)):
            $tbl_history_status = false;
        endif;
        if($tbl_history_status):
            $data_core[$v] = get_data($tbl_history,[
                'select' => "
                    coalesce(sum(case when bulan = '1' then ".$column." end), 0) as B_01,
                    coalesce(sum(case when bulan = '2' then ".$column." end), 0) as B_02,
                    coalesce(sum(case when bulan = '3' then ".$column." end), 0) as B_03,
                    coalesce(sum(case when bulan = '4' then ".$column." end), 0) as B_04,
                    coalesce(sum(case when bulan = '5' then ".$column." end), 0) as B_05,
                    coalesce(sum(case when bulan = '6' then ".$column." end), 0) as B_06,
                    coalesce(sum(case when bulan = '7' then ".$column." end), 0) as B_07,
                    coalesce(sum(case when bulan = '8' then ".$column." end), 0) as B_08,
                    coalesce(sum(case when bulan = '9' then ".$column." end), 0) as B_09,
                    coalesce(sum(case when bulan = '10' then ".$column." end), 0) as B_10,
                    coalesce(sum(case when bulan = '11' then ".$column." end), 0) as B_11,
                    coalesce(sum(case when bulan = '12' then ".$column." end), 0) as B_12,
                    account_name,
                    coa,
                    gwlsbi,
                    glwnco",
                'where_in' => ['glwnco' => $arr_coa],
                'group_by' => 'glwnco',
            ])->result_array();
        endif;
    }
    return $data_core;
}

function filter_cabang_admin($access_additional,$cabang,$dt=[]){
	$item = '';

	if(!$access_additional):
		$item .= '<label class="">'.lang('cabang').'  &nbsp</label>';
		$item .= '<select class="select2 custom-select" id="filter_cabang">';
		foreach($cabang as $b){
			$selected = '';
			if($b['kode_cabang'] == user('kode_cabang')) $selected = ' selected';
			$item .= '<option value="'.$b['kode_cabang'].'"'.$selected.'>'.$b['nama_cabang'].'</option>';
		}
		$item .= '</select>';
	else:
		$cab_induk = get_data('tbl_m_cabang',[
			'select' 	=> 'id,kode_cabang,nama_cabang',
			'where' 	=> "kode_cabang like 'G%' and kode_cabang != 'G001' and is_active = '1'",
			'order_by' 	=> 'kode_cabang'
		])->result_array();
		$item .= '<label class="">Cabang Induk  &nbsp</label>';
		$item .= '<select class="select2 custom-select" id="filter_cabang_induk">';
		foreach($cab_induk as $b){
			$selected = '';
			if($b['kode_cabang'] == user('kode_cabang')) $selected = ' selected';
			$nama_cabang = str_replace('GAB', '', $b['nama_cabang']);
			$item .= '<option value="'.$b['id'].'"'.$selected.'>'.$nama_cabang.'</option>';
		}
		$item .= '</select>';

		$item .= '<label class="">&nbsp '.lang('cabang').'  &nbsp</label>';
		$item .= '<select class="select2 custom-select" id="filter_cabang">';
		$item .= '</select>&nbsp';

		if(!isset($dt['no-align'])):
			$item .= '<style>';
			$item .= '.content-header{ height: auto !important; }';
			$item .= '.content-header .float-right{ margin-top: 1rem !important; }';
			$item .= '.content-header .header-info{ position: relative !important; }';
			$item .= '.mt-6{ margin-top: 4em;}';
			$item .= '</style>';
		endif;

	endif;
	return $item;
}

function get_currency($currency){
	$dt_currency = get_data('tbl_m_currency','id',$currency)->row_array();
	$nama  = "Rupiah";
	$nilai = 1;
	if($dt_currency):
		$nama 	= $dt_currency['nama'];
		$nilai	= (float) $dt_currency['nilai'];
	endif;
	return [
		'nama' 	=> $nama,
		'nilai'	=> $nilai,
	];
}

function hitung_rekap_rasio($cabang,$kode,$anggaran){
	$arr_coa 		= [];
	$arr_kode 		= [];
	$status_get 	= false;
	$status_pembagi	= false;
	$status_tambah	= false;
	$status_kurang	= false;
	$s_setahun 		= false;
	$s_no_data 		= false;
	$s_avg 			= false;
	$arr_tambah 	= [];
	$arr_kurang 	= [];
	$arr_bagi 		= [];
	$coa = '';

	if($kode == 'A1'):
		$arr_coa = ['602','5130000']; $status_pembagi = true; $s_setahun = true; $arr_tambah = ['5130000']; $arr_bagi = ['602'];
	elseif($kode == 'A2'):
		$coa = '5130000'; $arr_coa = [$coa];$status_get = true;
	elseif($kode == 'A3'):
		$coa = '602'; $arr_coa = [$coa];$status_get = true;
	elseif($kode == 'A4'):
		$arr_coa = ['4150000','1450000']; $status_pembagi = true; $s_setahun = true; $arr_tambah = ['4150000']; $arr_bagi = ['1450000'];
	elseif($kode == 'A5'):
		$coa = '4150000'; $arr_coa = [$coa];$status_get = true;
	elseif($kode == 'A6'):
		$coa = '1450000'; $arr_coa = [$coa];$status_get = true;
	elseif($kode == 'A7'):
		$s_no_data = true;
	elseif($kode == 'A8'):
		$arr_coa = ['122502','122501']; $status_pembagi = true; $arr_tambah = ['122502']; $arr_bagi = ['122501'];
	elseif($kode == 'A9'):
		$arr_coa = ['122506','122501']; $status_pembagi = true; $arr_tambah = ['122506']; $arr_bagi = ['122501'];
	elseif($kode == 'A10'):
		$arr_kode = ['A15','A16','A17','A22','A23','A24'];
		$arr_coa  = ['122502','122506'];
		$status_pembagi = true; $arr_tambah = $arr_kode; $arr_bagi = $arr_coa;
	elseif($kode == 'A11'):
		$arr_kode = ['A15','A16','A17'];
		$arr_coa  = ['122502'];
		$status_pembagi = true; $arr_tambah = $arr_kode; $arr_bagi = $arr_coa;
	elseif($kode == 'A12'):
		$coa = '122502'; $arr_coa = [$coa];$status_get = true;
	elseif(in_array($kode, ['A13','A14','A15','A16','A17','A20','A21','A22','A23','A24'])):
		$coa = $kode; $arr_kode = [$coa];$status_get = true;
	elseif($kode == 'A18'):
		$arr_kode = ['A22','A23','A24'];
		$arr_coa  = ['122506'];
		$status_pembagi = true; $arr_tambah = $arr_kode; $arr_bagi = $arr_coa;
	elseif($kode == 'A19'):
		$coa = '122506'; $arr_coa = [$coa];$status_get = true;
	elseif($kode == 'A25'):
		$arr_coa = ['602','1450000']; $status_pembagi = true; $arr_tambah = ['1450000']; $arr_bagi = ['602'];
	elseif($kode == 'A26'):
		$arr_coa = ['5100000','5500000','4100000']; $status_pembagi = true; $arr_tambah = ['5100000','5500000']; $arr_bagi = ['5100000','4100000'];
	elseif($kode == 'A27'):
		$arr_coa = ['5100000','5500000']; $status_tambah = true; $arr_tambah = ['5100000','5500000'];
	elseif($kode == 'A28'):
		$arr_coa = ['5100000','4100000']; $status_tambah = true; $arr_tambah = ['5100000','4100000'];
	elseif($kode == 'A29'):
		$arr_coa = ['1000000','59999']; $status_pembagi = true; $s_setahun = true; $s_avg = true; $arr_tambah = ['59999']; $arr_bagi = ['1000000'];
	elseif($kode == 'A30'):
		$coa = '59999'; $arr_coa = [$coa];$status_get = true;
	elseif($kode == 'A31'):
		$coa = '1000000'; $arr_coa = [$coa];$status_get = true;
	elseif($kode == 'A32'):
		$arr_coa = ['2100000','2120011','602']; $status_pembagi = true; $arr_tambah = ['2100000','2120011']; $arr_bagi = ['602'];
	elseif($kode == 'A32_1'):
		$arr_coa = ['2100000','2120011']; $status_tambah = true; $arr_tambah = ['2100000','2120011'];
	elseif($kode == 'A32_2'):
		$arr_coa = ['602']; $status_tambah = true; $arr_tambah = ['602'];
	elseif($kode == 'A33'):
		$arr_coa = ['4100000','5100000','1200000','1220000','1250000','1300000','1400000','1450000']; 
		$status_pembagi = true; $s_setahun = true; $s_avg = true;
		$arr_kurang = ['4100000','5100000']; $arr_bagi = ['1200000','1220000','1250000','1300000','1400000','1450000'];
	elseif($kode == 'A33_1'):
		$arr_coa = ['4100000','5100000']; $status_kurang = true; $arr_kurang = ['4100000','5100000'];
	elseif($kode == 'A33_2'):
		$arr_coa = ['1200000','1220000','1250000','1300000','1400000','1450000']; $status_tambah = true; $arr_tambah = $arr_coa;
	elseif($kode == 'A34'):
		$arr_coa = ['4590000','4100000','4500000']; $status_pembagi = true; $arr_tambah = ['4590000']; $arr_bagi = ['4100000','4500000'];
	elseif($kode == 'A34_1'):
		$coa = '4590000'; $arr_coa = [$coa];$status_get = true;
	elseif($kode == 'A34_2'):
		$arr_coa = ['4100000','4500000']; $status_tambah = true; $arr_tambah = ['4100000','4500000'];
	endif;

	$data = [];
	if(count($arr_coa)>0):
		$dt_budget  = get_data('tbl_budget_nett',[
            'where' => [
                'kode_anggaran' => $anggaran->kode_anggaran,
                'kode_cabang'   => $cabang,
                'coa'           => $arr_coa
            ],
        ])->result_array();
        foreach ($arr_coa as $v) {
            $key = array_search($v, array_column($dt_budget, 'coa'));
            if(strlen($key)>0):
                $data[$v] = $dt_budget[$key];
            else:
                $data[$v] = [
                    'B_01' => 0,
                    'B_02' => 0,
                    'B_03' => 0,
                    'B_04' => 0,
                    'B_05' => 0,
                    'B_06' => 0,
                    'B_07' => 0,
                    'B_08' => 0,
                    'B_09' => 0,
                    'B_10' => 0,
                    'B_11' => 0,
                    'B_12' => 0,
                ];
            endif;
        }
	endif;

	if(count($arr_kode)>0):
		$dt_budget_rekaprasio  = get_data('tbl_budget_nett_rekaprasio',[
            'where' => [
                'kode_anggaran' => $anggaran->kode_anggaran,
                'kode_cabang'   => $cabang,
                'kode'          => $arr_kode
            ],
        ])->result_array();
        foreach ($arr_kode as $v) {
            $key = array_search($v, array_column($dt_budget_rekaprasio, 'kode'));
            if(strlen($key)>0):
                $data[$v] = $dt_budget_rekaprasio[$key];
            else:
                $data[$v] = [
                    'B_01' => 0,
                    'B_02' => 0,
                    'B_03' => 0,
                    'B_04' => 0,
                    'B_05' => 0,
                    'B_06' => 0,
                    'B_07' => 0,
                    'B_08' => 0,
                    'B_09' => 0,
                    'B_10' => 0,
                    'B_11' => 0,
                    'B_12' => 0,
                ];
            endif;
        }
	endif;

	$res = [];
	$total = 0;
	for($i=1;$i<=12;$i++){
		$bulan = 'B_'.sprintf("%02d",$i);
		if($status_pembagi):
			$pembagi = 0; foreach ($arr_bagi as $v) {
				$pembagi += $data[$v][$bulan];
			}
			$total += $pembagi;
			if($s_avg) $pembagi = $total / $i; 
			if(!$pembagi) $pembagi = 1;

			$tambah = 0; foreach ($arr_tambah as $v) {
				$tambah += $data[$v][$bulan];
			}
			foreach ($arr_kurang as $k => $v) {
				if($k == 0):
					$tambah = $data[$v][$bulan];
				else:
					$tambah -= $data[$v][$bulan];
				endif;
			}


			if($s_setahun) $tambah = ($tambah/$i) * 12;

       		$A1 = ( $tambah/ $pembagi) * 100;
       		$res[$bulan] = custom_format($A1,false,2);
       	elseif($status_tambah):
			$tambah = 0; foreach ($arr_tambah as $v) {
				$tambah += $data[$v][$bulan];
			}

			if($s_setahun) $tambah = ($tambah/$i) * 12;

       		$res[$bulan] = custom_format(view_report($tambah));
       	elseif($status_kurang):
			$tambah = 0; foreach ($arr_kurang as $k => $v) {
				if($k == 0):
					$tambah = $data[$v][$bulan];
				else:
					$tambah -= $data[$v][$bulan];
				endif;
			}

			if($s_setahun) $tambah = ($tambah/$i) * 12;

       		$res[$bulan] = custom_format(view_report($tambah));
       	elseif($status_get):
       		$res[$bulan] = custom_format(view_report($data[$coa][$bulan]));
       	elseif($s_no_data):
       		$res[$bulan] = '';
		endif;
	}
	return $res;
}

function cabang_not_show(){
	return ['G001'];
}

function checkFormulaAkt($where,$data,$bulan){
	$key = multidimensional_search($data, $where);
	$res = ['status' => false];
	if(strlen($key)>0):
		$changed = json_decode($data[$key]['changed']);
		if(in_array($bulan, $changed)):
			$res['status'] 	= true;
			$res['data']	= $data[$key];
		endif;
	endif;
	return $res;
}

function checkFormulaAkt2($where,$data){
	$key = multidimensional_search($data, $where);
	$res = ['status' => false];
	if(strlen($key)>0):
		$res['status'] 	= true;
		$res['data']	= $data[$key];
	endif;
	return $res;
}

function checkSavedFormulaAkt($data,$anggaran){
	foreach ($data as $k => $v) {
		$dt 		= explode('-', $k);
		$coa 		= $dt[0];
		$tahun_core = $dt[1];
		$cabang 	= $dt[2];

		$record = insert_view_report_arr($v);
		
		$ck = get_data('tbl_formula_akt',[
			'select' => 'id',
			'where'	 => "glwnco = '$coa' and kode_anggaran = '$anggaran->kode_anggaran' and tahun_core = '$tahun_core' and kode_cabang = '$cabang'"
		])->row_array();
		if($ck):
			$ID = $ck['id'];
			update_data('tbl_formula_akt',$record,['id' => $ID]);
		else:
			$record['kode_cabang'] 		= $cabang;
			$record['kode_anggaran'] 	= $anggaran->kode_anggaran;
			$record['tahun_core']	 	= $tahun_core;
			$record['glwnco']			= $coa;
			if($tahun_core != $anggaran->tahun_anggaran):
				$record['parent_id'] = $cabang;
			else:
				$record['parent_id'] = "0";
			endif;
			insert_data('tbl_formula_akt',$record);
		endif;
	}
}

function checkFomulaAktSewa($data,$bulan,$tahun){
	$res = 0;
	foreach ($data as $k => $v) {
		if($v['tahun'] == $tahun && $v['bulan'] == $bulan){
			$res += $v['harga'];
		}
	}
	return $res;
}

function searchPersentase($where,$data){
	$key = multidimensional_search($data, $where);
	$res = 0;
	if(strlen($key)>0):
		$dt = $data[$key];
		$val = (float) $dt['persen'];
		$res = $val/100;
	endif;
	return $res;
}

function cabang_divisi($access=""){
	 $segment = $cur_segment = uri_segment(2) ? uri_segment(2) : uri_segment(1);
    if($access) {
        $cur_segment        = $access;
    }
    $dt_access    = get_access($cur_segment);
    $cabang_user  = get_data('tbl_user',[
        'where' => [
            'is_active' => 1,
            'id_group'  => id_group_access($cur_segment),
            ''
        ]
    ])->result();

    $kode_cabang          = [];
    foreach($cabang_user as $c) $kode_cabang[] = $c->kode_cabang;

    $id = user('kode_cabang');
    $cab = get_data('tbl_m_cabang','kode_cabang',$id)->row_array();

    $x = '';
    if(isset($cab['id'])){ 
        for ($i = 1; $i <= 4; $i++) { 
            $field = 'level' . $i ;

            if($cab['id'] == $cab[$field]) {
                $x = $field ; 
            }    
        }    
    }
    $query = [
	    'select'    => 'distinct a.id,a.kode_cabang,a.nama_cabang',
	    'where'     => [
	        'a.is_active' => 1,
	        'a.'.$x => $cab['id'],
	        'a.kode_cabang' => $kode_cabang,
	        'a.kode_cabang != ' => 'G001'
    	]
    ];
    $data['status_group'] 		= $cab['status_group'];
    $data['access_additional']  = $dt_access['access_additional'];
    if($dt_access['access_additional']):
    	unset($query['where']['a.'.$x]);
    	$data['status_group'] 		= 1;
    endif;
    $data['cabang']            	= get_data('tbl_m_cabang a',$query)->result_array();


    if($data['status_group'] == 1):
    	$option_induk = '<label class="">Cabang Induk &nbsp</label>';
    	$option_induk .= '<select class="select2 custom-select" id="filter_cabang_induk" data-type="divisi">';
		foreach($data['cabang'] as $b){
			$selected = '';
			if($b['kode_cabang'] == user('kode_cabang')) $selected = ' selected';
			$nama_cabang 	= $b['nama_cabang'];
			$option_induk 	.= '<option value="'.$b['id'].'"'.$selected.'>'.$nama_cabang.'</option>';
		}
		$option_induk .= '</select>';

		$option_induk .= '<label class="">&nbsp '.lang('cabang').'  &nbsp</label>';
		$option_induk .= '<select class="select2 custom-select" id="filter_cabang">';
		$option_induk .= '</select>&nbsp';

		$option_induk .= '<style>';
		$option_induk .= '.content-header{ height: auto !important; }';
		$option_induk .= '.content-header .float-right{ margin-top: 1rem !important; }';
		$option_induk .= '.content-header .header-info{ position: relative !important; }';
		$option_induk .= '.mt-6{ margin-top: 4em;}';
		$option_induk .= '</style>';
		$data['option'] = $option_induk;
    else:
    	$item = '<label class="">'.lang('cabang').'  &nbsp</label>';
		$item .= '<select class="select2 custom-select" id="filter_cabang">';
		foreach($data['cabang'] as $b){
			$selected = '';
			if($b['kode_cabang'] == user('kode_cabang')) $selected = ' selected';
			$item .= '<option value="'.$b['kode_cabang'].'"'.$selected.'>'.$b['nama_cabang'].'</option>';
		}
		$item .= '</select>';
		$data['option'] = $item;
    endif;

    $data['tahun'] = get_data('tbl_tahun_anggaran','kode_anggaran',user('kode_anggaran'))->result();
    return $data;
}

// clone table rate dan prosentase dpk
function clone_rate($kode_anggaran,$table){
	$anggaran = get_data('tbl_tahun_anggaran','kode_anggaran',$kode_anggaran)->row();
	$last_anggaran = get_data('tbl_tahun_anggaran',[
		'select' 		=> 'kode_anggaran',
		'where' 		=> "kode_anggaran != '$kode_anggaran' and is_active = '1' ",
		'order_by' 		=> 'id',
		'sort'			=> 'DESC',
	])->row();

	if($last_anggaran):
		$rate = get_data($table,[
			'where' => [
				'kode_anggaran' => $last_anggaran->kode_anggaran,
				'is_active'		=> 1,
			]
		])->result();
		foreach ($rate as $k => $v) {
			unset($v->id);
			$v->kode_anggaran 		= $anggaran->kode_anggaran;
			$v->id_anggaran 		= $anggaran->id;
			$v->keterangan_anggaran = $anggaran->keterangan;
			$v->create_by 			= user('username');
			$v->create_at 			= date("Y-m-d H:i:s");
		}
		if(count($rate)>0):
			insert_batch($table,$rate);
		endif;
	endif;
}

function clone_value_table($table,$last_anggaran,$anggaran,$additional = array()){
	if($last_anggaran):
		$d = get_data($table,[
			'where' => [
				'kode_anggaran' => $last_anggaran->kode_anggaran,
				'is_active'		=> 1,
			]
		])->result_array();
		$data = [];
		foreach ($d as $k => $v) {
			unset($v['id']);
			$v['kode_anggaran'] 		= $anggaran->kode_anggaran;
			foreach ($additional as $k2 => $v2) {
				if(isset($v[$k2])):
					$v[$k2] = $v2;
				endif;
			}
			$data[] = $v;
		}
		if(count($data)>0):
			// render($data,'json');
			insert_batch($table,$data);
		endif;
	endif;
}

function clone_table($table,$table_last_anggaran){
	$CI         			= get_instance();
    $status 				= $CI->db->table_exists($table);
    $status_last_anggaran 	= $CI->db->table_exists($table_last_anggaran);
    if(!$status && $status_last_anggaran):
    	$CI->db->query("CREATE TABLE ".$table." AS SELECT * FROM ".$table_last_anggaran);
    endif;
}

function coa_neraca($coa){
	$data = [];
    foreach ($coa as $k => $v) {
        
        // center
        if(!$v->level0 && !$v->level1 && !$v->level2 && !$v->level3 && !$v->level4 && !$v->level5):
            $h = $v;
            $data['coa'][] = $h;
        endif;

        // level 0
        if($v->level0 && !$v->level1 && !$v->level2 && !$v->level3 && !$v->level4 && !$v->level5):
            $h = $v;
            $data['coa0'][$v->level0][] = $h;
        endif;

        // level 1
        if(!$v->level0 && $v->level1 && !$v->level2 && !$v->level3 && !$v->level4 && !$v->level5):
            $h = $v;
            $data['coa1'][$v->level1][] = $h;
        endif;

        // level 2
        if(!$v->level0 && !$v->level1 && $v->level2 && !$v->level3 && !$v->level4 && !$v->level5):
            $h = $v;
            $data['coa2'][$v->level2][] = $h;
        endif;

        // level 3
        if(!$v->level0 && !$v->level1 && !$v->level2 && $v->level3 && !$v->level4 && !$v->level5):
            $h = $v;
            $data['coa3'][$v->level3][] = $h;
        endif;

        // level 4
        if(!$v->level0 && !$v->level1 && !$v->level2 && !$v->level3 && $v->level4 && !$v->level5):
            $h = $v;
            $data['coa4'][$v->level4][] = $h;
        endif;

        // level 5
        if(!$v->level0 && !$v->level1 && !$v->level2 && !$v->level3 && !$v->level4 && $v->level5):
            $h = $v;
            $data['coa5'][$v->level5][] = $h;
        endif;
    }

    if(!isset($data['coa'])):
    	$data['coa'] = [];
    endif;

    return $data;
}

function coa_labarugi($coa){
    $data = [];
    foreach ($coa as $k => $v) {

        // center
        if(!$v->level1 && !$v->level2 && !$v->level3 && !$v->level4 && !$v->level5):
            $h = $v;
            $data['coa'][] = $h;
        endif;

        // level 1
        if($v->level1 && !$v->level2 && !$v->level3 && !$v->level4 && !$v->level5):
            $h = $v;
            $data['coa0'][$v->level1][] = $h;
        endif;

        // level 2
        if(!$v->level1 && $v->level2 && !$v->level3 && !$v->level4 && !$v->level5):
            $h = $v;
            $data['coa1'][$v->level2][] = $h;
        endif;

        // level 3
        if(!$v->level1 && !$v->level2 && $v->level3 && !$v->level4 && !$v->level5):
            $h = $v;
            $data['coa2'][$v->level3][] = $h;
        endif;

        // level 4
        if(!$v->level1 && !$v->level2 && !$v->level3 && $v->level4 && !$v->level5):
            $h = $v;
            $data['coa3'][$v->level4][] = $h;
        endif;

        // level 5
        if(!$v->level1 && !$v->level2 && !$v->level3 && !$v->level4 && $v->level5):
            $h = $v;
            $data['coa4'][$v->level5][] = $h;
        endif;
    }
    if(!isset($data['coa'])):
    	$data['coa'] = [];
    endif;
    return $data;
}

function create_autorun($kode_anggaran,$kode_cabang,$page){
	$where = [
		'kode_anggaran' => $kode_anggaran,
		'kode_cabang'	=> $kode_cabang,
		'page'			=> $page,
		'status'		=> 1,
	];
	$ck = get_data('tbl_autorun',['select' => 'id','where' => $where])->result_array();
	if(count($ck)<=0):
		save_data('tbl_autorun',$where);
	endif;
}
function call_autorun($kode_anggaran,$kode_cabang,$page){
	$where = [
		'kode_anggaran' => $kode_anggaran,
		'kode_cabang'	=> $kode_cabang,
		'page'			=> $page,
		'status'		=> 1,
	];
	$ck = get_data('tbl_autorun',['select' => 'id','where' => $where])->result_array();
	$count = count($ck);
	foreach ($ck as $k => $v) {
		$data['id'] 	= $v['id'];
		$data['status']	= 0;
		save_data('tbl_autorun',$data);
	}
	return $count;
}

function recalculate_sales($tahun="",$product="",$sector="") {
	ini_set('memory_limit', '-1');

	$table1 = 'tbl_budget_pricelist_' . $tahun ;
	$table2 = 'tbl_budget_qtysales_' . $tahun ;
	$table3 = 'tbl_budget_grsales_' . $tahun ;

	$table4 = 'tbl_budget_discount_' . $tahun ;
	$table5 = 'tbl_budget_netsales_' . $tahun ;

	$table6 = 'tbl_budget_unitcogs_' . $tahun ;
	$table7 = 'tbl_budget_cogs_' . $tahun ;

	$arrq = [
		'select' => 'a.budget_product_code, 
					(a.EST_01 * b.EST_01) as EST_01, (a.EST_02 * b.EST_02) as EST_02, (a.EST_03 * b.EST_03) as EST_03, (a.EST_04 * b.EST_04) as EST_04,
					(a.EST_05 * b.EST_05) as EST_05, (a.EST_06 * b.EST_06) as EST_06, (a.EST_07 * b.EST_07) as EST_07, (a.EST_08 * b.EST_08) as EST_08,
					(a.EST_09 * b.EST_09) as EST_09, (a.EST_10 * b.EST_10) as EST_10, (a.EST_11 * b.EST_11) as EST_11, (a.EST_12 * b.EST_12) as EST_12,
					(a.B_01 * b.B_01) as B_01, (a.B_02 * b.B_02) as B_02, (a.B_03 * b.B_03) as B_03, (a.B_04 * b.B_04) as B_04,
					(a.B_05 * b.B_05) as B_05, (a.B_06 * b.B_06) as B_06, (a.B_07 * b.B_07) as B_07, (a.B_08 * b.B_08) as B_08,
					(a.B_09 * b.B_09) as B_09, (a.B_10 * b.B_10) as B_10, (a.B_11 * b.B_11) as B_11, (a.B_12 * b.B_12) as B_12,
					(a.THN_01 * b.THN_01) as THN_01, (a.THN_02 * b.THN_02) as THN_02, (a.THN_03 * b.THN_03) as THN_03, (a.THN_04 * b.THN_04) as THN_04,
					(a.THN_05 * b.THN_05) as THN_05, (a.THN_06 * b.THN_06) as THN_06, (a.THN_07 * b.THN_07) as THN_07, (a.THN_08 * b.THN_08) as THN_08,
					(a.THN_09 * b.THN_09) as THN_09, (a.THN_10 * b.THN_10) as THN_10,

					(a.EST_01 * b.EST_01) + c.EST_01 as nEST_01, (a.EST_02 * b.EST_02) + c.EST_02 as nEST_02, (a.EST_03 * b.EST_03) + c.EST_03 as nEST_03, (a.EST_04 * b.EST_04) + c.EST_04 as nEST_04,
					(a.EST_05 * b.EST_05) + c.EST_05 as nEST_05, (a.EST_06 * b.EST_06) + c.EST_06 as nEST_06, (a.EST_07 * b.EST_07) + c.EST_07 as nEST_07, (a.EST_08 * b.EST_08) + c.EST_08 as nEST_08,
					(a.EST_09 * b.EST_09) + c.EST_09 as nEST_09, (a.EST_10 * b.EST_10) + c.EST_10 as nEST_10, (a.EST_11 * b.EST_11) + c.EST_11 as nEST_11, (a.EST_12 * b.EST_12) + c.EST_12 as nEST_12,
					
					(a.B_01 * b.B_01) + c.B_01 as nB_01, (a.B_02 * b.B_02) + c.B_02 as nB_02, (a.B_03 * b.B_03) + c.B_03 as nB_03, (a.B_04 * b.B_04) + c.B_04 as nB_04,
					(a.B_05 * b.B_05) + c.B_05 as nB_05, (a.B_06 * b.B_06) + c.B_06 as nB_06, (a.B_07 * b.B_07) + c.B_07 as nB_07, (a.B_08 * b.B_08) + c.B_08 as nB_08,
					(a.B_09 * b.B_09) + c.B_09 as nB_09, (a.B_10 * b.B_10) + c.B_10 as nB_10, (a.B_11 * b.B_11) + c.B_11 as nB_11, (a.B_12 * b.B_12) + c.B_12 as nB_12,
				   
					(a.THN_01 * b.THN_01) + c.THN_01 as nTHN_01, (a.THN_02 * b.THN_02) + c.THN_02 as nTHN_02, (a.THN_03 * b.THN_03) + c.THN_03 as nTHN_03, (a.THN_04 * b.THN_04) + c.THN_04 as nTHN_04,
					(a.THN_05 * b.THN_05) + c.THN_05 as nTHN_05, (a.THN_06 * b.THN_06) + c.THN_06 as nTHN_06, (a.THN_07 * b.THN_07) + c.THN_07 as nTHN_07, (a.THN_08 * b.THN_08) + c.THN_08 as nTHN_08,
					(a.THN_09 * b.THN_09) + c.THN_09 as nTHN_09, (a.THN_10 * b.THN_10) + c.THN_10 as nTHN_10,

					
					(b.EST_01 * e.EST_01) as CEST_01, (b.EST_02 * e.EST_02) as CEST_02, (b.EST_03 * e.EST_03) as CEST_03, (b.EST_04 * e.EST_04) as CEST_04,
					(b.EST_05 * e.EST_05) as CEST_05, (b.EST_06 * e.EST_06) as CEST_06, (b.EST_07 * e.EST_07) as CEST_07, (b.EST_08 * e.EST_08) as CEST_08,
					(b.EST_09 * e.EST_09) as CEST_09, (b.EST_10 * e.EST_10) as CEST_10, (b.EST_11 * e.EST_11) as CEST_11, (b.EST_12 * e.EST_12) as CEST_12,
					
					(b.B_01 * e.B_01) as CB_01, (b.B_02 * e.B_02) as CB_02, (b.B_03 * e.B_03) as CB_03, (b.B_04 * e.B_04) as CB_04,
					(b.B_05 * e.B_05) as CB_05, (b.B_06 * e.B_06) as CB_06, (b.B_07 * e.B_07) as CB_07, (b.B_08 * e.B_08) as CB_08,
					(b.B_09 * e.B_09) as CB_09, (b.B_10 * e.B_10) as CB_10, (b.B_11 * e.B_11) as CB_11, (b.B_12 * e.B_12) as CB_12,
					
					(b.THN_01 * e.THN_01) as CTHN_01, (b.THN_02 * e.THN_02) as CTHN_02, (b.THN_03 * e.THN_03) as CTHN_03, (b.THN_04 * e.THN_04) as CTHN_04,
					(b.THN_05 * e.THN_05) as CTHN_05, (b.THN_06 * e.THN_06) as CTHN_06, (b.THN_07 * e.THN_07) as CTHN_07, (b.THN_08 * e.THN_08) as CTHN_08,
					(b.THN_09 * e.THN_09) as CTHN_09, (b.THN_10 * e.THN_10) as CTHN_10',        
					  
		'join'   => [$table2 . ' b on a.budget_product_code = b.budget_product_code and a.budget_product_sector = b.budget_product_sector',
					 $table4 . ' c on a.budget_product_code = c.budget_product_code and a.budget_product_sector = c.budget_product_sector',
					 $table6 . ' e on a.budget_product_code = e.budget_product_code and a.budget_product_sector = e.budget_product_sector',
					],
		'where'  => [
			'a.tahun' => $tahun,
			// 'a.budget_product_code' => $product,
			// 'a.budget_product_sector' => $sector, 
		],
	];

	if($product) $arrq['where']['a.budget_product_code'] = $product;
	if($sector) $arrq['where']['a.budget_product_sector'] = $sector;

	$sales_amount = get_data($table1 . ' a',$arrq)->row();


	if(isset($sales_amount->budget_product_code)) {

		 update_data($table3,
			['EST_01' => $sales_amount->EST_01,'EST_02' => $sales_amount->EST_02,'EST_03' => $sales_amount->EST_03,'EST_04' => $sales_amount->EST_04,
			'EST_05' => $sales_amount->EST_05,'EST_06' => $sales_amount->EST_06,'EST_07' => $sales_amount->EST_07,'EST_08' => $sales_amount->EST_08,
			'EST_09' => $sales_amount->EST_09,'EST_10' => $sales_amount->EST_10,'EST_11' => $sales_amount->EST_11,'EST_12' => $sales_amount->EST_12,
			'B_01' => $sales_amount->B_01,'B_02' => $sales_amount->B_02,'B_03' => $sales_amount->B_03,'B_04' => $sales_amount->B_04,
			'B_05' => $sales_amount->B_05,'B_06' => $sales_amount->B_06,'B_07' => $sales_amount->B_07,'B_08' => $sales_amount->B_08,
			'B_09' => $sales_amount->B_09,'B_10' => $sales_amount->B_10,'B_11' => $sales_amount->B_11,'B_12' => $sales_amount->B_12,
			'THN_01' => $sales_amount->THN_01,'THN_02' => $sales_amount->THN_02,'THN_03' => $sales_amount->THN_03,'THN_04' => $sales_amount->THN_04,
			'THN_05' => $sales_amount->THN_05,'THN_06' => $sales_amount->THN_06,'THN_07' => $sales_amount->THN_07,'THN_08' => $sales_amount->THN_08,
			'THN_09' => $sales_amount->THN_09,'THN_10' => $sales_amount->THN_10,
			'total_est' => $sales_amount->EST_01+$sales_amount->EST_02+$sales_amount->EST_03+$sales_amount->EST_04+$sales_amount->EST_05+$sales_amount->EST_06+
			$sales_amount->EST_07+$sales_amount->EST_08+$sales_amount->EST_09+$sales_amount->EST_10+$sales_amount->EST_11+$sales_amount->EST_12,
			'total_budget' => $sales_amount->B_01+$sales_amount->B_02+$sales_amount->B_03+$sales_amount->B_04+$sales_amount->B_05+$sales_amount->B_06+
			$sales_amount->B_07+$sales_amount->B_08+$sales_amount->B_09+$sales_amount->B_10+$sales_amount->B_11+$sales_amount->B_12
			],
			['budget_product_code'=>$product,'tahun'=>$tahun,'budget_product_sector'=>$sector]);
			
		update_data($table5,
			['EST_01' => $sales_amount->nEST_01,'EST_02' => $sales_amount->nEST_02,'EST_03' => $sales_amount->nEST_03,'EST_04' => $sales_amount->nEST_04,
			'EST_05' => $sales_amount->nEST_05,'EST_06' => $sales_amount->nEST_06,'EST_07' => $sales_amount->nEST_07,'EST_08' => $sales_amount->nEST_08,
			'EST_09' => $sales_amount->nEST_09,'EST_10' => $sales_amount->nEST_10,'EST_11' => $sales_amount->nEST_11,'EST_12' => $sales_amount->nEST_12,
			'B_01' => $sales_amount->nB_01,'B_02' => $sales_amount->nB_02,'B_03' => $sales_amount->nB_03,'B_04' => $sales_amount->nB_04,
			'B_05' => $sales_amount->nB_05,'B_06' => $sales_amount->nB_06,'B_07' => $sales_amount->nB_07,'B_08' => $sales_amount->nB_08,
			'B_09' => $sales_amount->nB_09,'B_10' => $sales_amount->nB_10,'B_11' => $sales_amount->nB_11,'B_12' => $sales_amount->nB_12,
			'THN_01' => $sales_amount->nTHN_01,'THN_02' => $sales_amount->nTHN_02,'THN_03' => $sales_amount->nTHN_03,'THN_04' => $sales_amount->nTHN_04,
			'THN_05' => $sales_amount->nTHN_05,'THN_06' => $sales_amount->nTHN_06,'THN_07' => $sales_amount->nTHN_07,'THN_08' => $sales_amount->nTHN_08,
			'THN_09' => $sales_amount->nTHN_09,'THN_10' => $sales_amount->nTHN_10,
			'total_est' => $sales_amount->nEST_01+$sales_amount->nEST_02+$sales_amount->nEST_03+$sales_amount->nEST_04+$sales_amount->nEST_05+$sales_amount->nEST_06+
			$sales_amount->nEST_07+$sales_amount->nEST_08+$sales_amount->nEST_09+$sales_amount->nEST_10+$sales_amount->nEST_11+$sales_amount->nEST_12,
			'total_budget' => $sales_amount->nB_01+$sales_amount->nB_02+$sales_amount->nB_03+$sales_amount->nB_04+$sales_amount->nB_05+$sales_amount->nB_06+
			$sales_amount->nB_07+$sales_amount->nB_08+$sales_amount->nB_09+$sales_amount->nB_10+$sales_amount->nB_11+$sales_amount->nB_12
			],
			['budget_product_code'=>$product,'tahun'=>$tahun,'budget_product_sector'=>$sector]);

			
			$fest_cogs = '';
			$fbud_cogs = '';
			$fthn_cogs = '';

			$fest_cogs1 = '';
			$fbud_cogs1 = '';
			$fthn_cogs1 = '';
			$i_start= setting('actual_budget') + 1;


			for ($i = $i_start ; $i <= 12; $i++) { 
				$fest_cogs = 'EST_' . sprintf('%02d', $i);
				$fbud_cogs = 'B_' . sprintf('%02d', $i);


				$fest_cogs1 = 'CEST_' . sprintf('%02d', $i);
				$fbud_cogs1 = 'CB_' . sprintf('%02d', $i);

				if($i <= 10) {
					$fthn_cogs = 'THN_' . sprintf('%02d', $i);
					$fthn_cogs1 = 'CTHN_' . sprintf('%02d', $i);
				}


				update_data($table7,[$fest_cogs=>$sales_amount->$fest_cogs1, $fbud_cogs => $sales_amount->$fbud_cogs1, $fthn_cogs => $sales_amount->$fthn_cogs1],
				['budget_product_code'=>$product,'tahun'=>$tahun,'budget_product_sector'=>$sector]); 

			}

		// update_data($table7,
		// 	['EST_01' => $sales_amount->CEST_01,'EST_02' => $sales_amount->CEST_02,'EST_03' => $sales_amount->CEST_03,'EST_04' => $sales_amount->CEST_04,
		// 	'EST_05' => $sales_amount->CEST_05,'EST_06' => $sales_amount->CEST_06,'EST_07' => $sales_amount->CEST_07,'EST_08' => $sales_amount->CEST_08,
		// 	'EST_09' => $sales_amount->CEST_09,'EST_10' => $sales_amount->CEST_10,'EST_11' => $sales_amount->CEST_11,'EST_12' => $sales_amount->CEST_12,
		// 	'B_01' => $sales_amount->CB_01,'B_02' => $sales_amount->CB_02,'B_03' => $sales_amount->CB_03,'B_04' => $sales_amount->CB_04,
		// 	'B_05' => $sales_amount->CB_05,'B_06' => $sales_amount->CB_06,'B_07' => $sales_amount->CB_07,'B_08' => $sales_amount->CB_08,
		// 	'B_09' => $sales_amount->CB_09,'B_10' => $sales_amount->CB_10,'B_11' => $sales_amount->CB_11,'B_12' => $sales_amount->CB_12,
		// 	'THN_01' => $sales_amount->CTHN_01,'THN_02' => $sales_amount->CTHN_02,'THN_03' => $sales_amount->CTHN_03,'THN_04' => $sales_amount->CTHN_04,
		// 	'THN_05' => $sales_amount->CTHN_05,'THN_06' => $sales_amount->CTHN_06,'THN_07' => $sales_amount->CTHN_07,'THN_08' => $sales_amount->CTHN_08,
		// 	'THN_09' => $sales_amount->CTHN_09,'THN_10' => $sales_amount->CTHN_10
		// 	],
		// 	['budget_product_code'=>$product,'tahun'=>$tahun,'budget_product_sector'=>$sector]);

		}

		if($sector == 1) {
			$budget_product_sector = "REGULER";
		}elseif($sector == 2){
			$budget_product_sector = "E-CATALOG";
		}elseif($sector == 3) {
			$budget_product_sector = "IN-HEALTH";
		}elseif($sector == 4) {
			$budget_product_sector = "ASKES";
		}elseif($sector == 5) {
			$budget_product_sector = "HARGA KHUSUS";
		}else{
			$budget_product_sector = "";
		}
		
		$actual = get_data('tbl_actual_gross_profit',[
			'select' => 'product_code,bulan,sector,sum(qty_sales) as qty_sales, sum(sales_amount) as sales_amount,
						 sum(discount) as discount, sum(cogs) as cogs, sum(cogs_idle) as cogs_idle, sum(cogs_loss) as cogs_loss',
			'where' => [
				'tahun' => $tahun,
				'product_code' => 'product',
				'sector' => $budget_product_sector,
			],
			'group_by' => 'product_code,bulan,sector',
			])->result();

	   foreach($actual as $a) {

			switch ($a->bulan) {
				case "01"; 
					$qB_01 = ($a->qty_sales != 0 ? $a->qty_sales : 0);
					$pB_01 = ($a->qty_sales != 0 ? $a->sales_amount / $a->qty_sales : 0);
					$dB_01 = ($a->discount != 0 ? $a->discount : 0);
					$cB_01 = ($a->cogs != 0 ? $a->cogs : 0);
					$uB_01 = (($a->qty_sales != 0 && $a->cogs != 0) ? $a->cogs / $a->qty_sales : 0);
					$gB_01 = $a->sales_amount ;
					$nB_01 = ($a->sales_amount + $a->discount);
					break;
				case "02":
					$qB_02 = ($a->qty_sales != 0 ? $a->qty_sales : 0);
					$pB_02 = ($a->qty_sales != 0 ? $a->sales_amount / $a->qty_sales : 0);
					$dB_02 = ($a->discount != 0 ? $a->discount : 0);
					$cB_02 = ($a->cogs != 0 ? $a->cogs : 0);
					$uB_02 = (($a->qty_sales != 0 && $a->cogs != 0) ? $a->cogs / $a->qty_sales : 0);
					$gB_02 = $a->sales_amount ;
					$nB_02 = ($a->sales_amount + $a->discount);
					break;
				case "03":
					$qB_03 = ($a->qty_sales != 0 ? $a->qty_sales : 0);
					$pB_03 = ($a->qty_sales != 0 ? $a->sales_amount / $a->qty_sales : 0);
					$dB_03 = ($a->discount != 0 ? $a->discount : 0);
					$cB_03 = ($a->cogs != 0 ? $a->cogs : 0);
					$uB_03 = (($a->qty_sales != 0 && $a->cogs != 0) ? $a->cogs / $a->qty_sales : 0);
					$gB_03 = $a->sales_amount ;
					$nB_03 = ($a->sales_amount + $a->discount);
					break;
				case "04";
					$qB_04 = ($a->qty_sales != 0 ? $a->qty_sales : 0);
					$pB_04 = ($a->qty_sales != 0 ? $a->sales_amount / $a->qty_sales : 0);
					$dB_04 = ($a->discount != 0 ? $a->discount  : 0);
					$cB_04 = ($a->cogs != 0 ? $a->cogs : 0);
					$uB_04 = (($a->qty_sales != 0 && $a->cogs != 0) ? $a->cogs / $a->qty_sales : 0);
					$gB_04 = $a->sales_amount ;
					$nB_04 = ($a->sales_amount + $a->discount);
					break;
				case "05":
					$qB_05 = ($a->qty_sales != 0 ? $a->qty_sales : 0);
					$pB_05 = ($a->qty_sales != 0 ? $a->sales_amount / $a->qty_sales : 0);
					$dB_05 = ($a->discount != 0 ? $a->discount : 0);
					$cB_05 = ($a->cogs != 0 ? $a->cogs : 0);
					$uB_05 = (($a->qty_sales != 0 && $a->cogs != 0) ? $a->cogs / $a->qty_sales : 0);
					$gB_05 = $a->sales_amount ;
					$nB_05 = ($a->sales_amount + $a->discount);
					break;
				case "06":
					$qB_06 = ($a->qty_sales != 0 ? $a->qty_sales : 0);
					$pB_06 = ($a->qty_sales != 0 ? $a->sales_amount / $a->qty_sales : 0);
					$dB_06 = ($a->discount != 0 ? $a->discount  : 0);
					$cB_06 = ($a->cogs != 0 ? $a->cogs : 0);
					$uB_06 = (($a->qty_sales != 0 && $a->cogs != 0) ? $a->cogs / $a->qty_sales : 0);
					$gB_06 = $a->sales_amount ;
					$nB_06 = ($a->sales_amount + $a->discount);
					break;
				case "07";
					$qB_07 = ($a->qty_sales != 0 ? $a->qty_sales : 0);
					$pB_07 = ($a->qty_sales != 0 ? $a->sales_amount / $a->qty_sales : 0);
					$dB_07 = ($a->discount != 0 ? $a->discount : 0);
					$cB_07 = ($a->cogs != 0 ? $a->cogs : 0);
					$uB_07 = (($a->qty_sales != 0 && $a->cogs != 0) ? $a->cogs / $a->qty_sales : 0);
					$gB_07 = $a->sales_amount ;
					$nB_07 = ($a->sales_amount + $a->discount);
					break;
				case "08":
					$qB_08 = ($a->qty_sales != 0 ? $a->qty_sales : 0);
					$pB_08 = ($a->qty_sales != 0 ? $a->sales_amount / $a->qty_sales : 0);
					$dB_08 = ($a->discount != 0 ? $a->discount : 0);
					$cB_08 = ($a->cogs != 0 ? $a->cogs : 0);
					$uB_08 = (($a->qty_sales != 0 && $a->cogs != 0) ? $a->cogs / $a->qty_sales : 0);
					$gB_08 = $a->sales_amount ;
					$nB_08 = ($a->sales_amount + $a->discount);
					break;
				case "09":
					$qB_09 = ($a->qty_sales != 0 ? $a->qty_sales : 0);
					$pB_09 = ($a->qty_sales != 0 ? $a->sales_amount / $a->qty_sales : 0);
					$dB_09 = ($a->discount != 0 ? $a->discount : 0);
					$cB_09 = ($a->cogs != 0 ? $a->cogs : 0);
					$uB_09 = (($a->qty_sales != 0 && $a->cogs != 0) ? $a->cogs / $a->qty_sales : 0);
					$gB_09 = $a->sales_amount ;
					$nB_09 = ($a->sales_amount + $a->discount);
					break;
				case "10":
					$qB_10 = ($a->qty_sales != 0 ? $a->qty_sales : 0);
					$pB_10 = ($a->qty_sales != 0 ? $a->sales_amount / $a->qty_sales : 0);
					$dB_10 = ($a->discount != 0 ? $a->discount  : 0);
					$cB_10 = ($a->cogs != 0 ? $a->cogs : 0);
					$uB_10 = (($a->qty_sales != 0 && $a->cogs != 0) ? $a->cogs / $a->qty_sales : 0);
					$gB_10 = $a->sales_amount ;
					$nB_10 = ($a->sales_amount + $a->discount);
					break;
				case "11":
					$qB_11 = ($a->qty_sales != 0 ? $a->qty_sales : 0);
					$pB_11 = ($a->qty_sales != 0 ? $a->sales_amount / $a->qty_sales : 0);
					$dB_11 = ($a->discount != 0 ? $a->discount : 0);
					$cB_11 = ($a->cogs != 0 ? $a->cogs : 0);
					$uB_11 = (($a->qty_sales != 0 && $a->cogs != 0) ? $a->cogs / $a->qty_sales : 0);
					$gB_11 = $a->sales_amount ;
					$nB_11 = ($a->sales_amount + $a->discount);
				case "12":
					$qB_12 = ($a->qty_sales != 0 ? $a->qty_sales : 0);
					$pB_12 = ($a->qty_sales != 0 ? $a->sales_amount / $a->qty_sales : 0);
					$dB_12 = ($a->discount != 0 ? $a->discount  : 0);
					$cB_12 = ($a->cogs != 0 ? $a->cogs : 0);
					$uB_12 = (($a->qty_sales != 0 && $a->cogs != 0) ? $a->cogs / $a->qty_sales : 0);
					$gB_12 = $a->sales_amount ;
					$nB_12 = ($a->sales_amount + $a->discount);
					break;
				default:
					echo "The color is neither red, blue, nor green!";
			}

			$arr            = [
				'select'    => 'a.*',
				'where'     => [
					'a.budget_product_code' => $a->product_code,
					'a.budget_product_sector' => $budget_product_sector,
				],
			];

			$cek1 = get_data($table3 . ' a',$arr)->row();
			$cek2 = get_data($table5 . ' a',$arr)->row();

			if(isset($cek1->budget_product_code)) {
				$field1 = "" ;
				$field = "";
				$field = 'EST_' . sprintf('%02d', $a->bulan);
				$field1 = 'gB_' . sprintf('%02d', $a->bulan);   

				update_data($table3,[$field=>$$field1],['id'=>$cek1->id]); 
			} 

			if(isset($cek2->budget_product_code)) {
				$field2 = "" ;
				$field = "";
				$field = 'EST_' . sprintf('%02d', $a->bulan);
				$field2 = 'nB_' . sprintf('%02d', $a->bulan);   

				update_data($table5,[$field=>$$field2],['id'=>$cek2->id]); 
			} 
	   }
}

function isi_budget_acaount($tahun="",$cc="") {
	ini_set('memory_limit', '-1');
	ini_set('max_execution_time', 0);

	$table = 'tbl_fact_cost_centre';
	$table2 = 'tbl_fact_lstbudget_' . $tahun;

	// $cc1 = $cc;


	if($cc == "ALL" || empty($cc)) {
		$lstcc = get_data($table,[
			'select' => 'kode as cost_centre',
			'where' => [
				'kode !=' => '0000',
			],
			'group_by' => 'kode',
		])->result();
	}else{
		$lstcc = get_data($table,[
			'select' => 'kode as cost_centre',
			'where' => [
				'kode' => $cc,
			],
			'group_by' => 'kode',
		])->result();
	}

	$jum = 0;


	foreach($lstcc as $c) {
		$cc = $c->cost_centre;
		$cost_centre = get_data('tbl_fact_cost_centre','kode',$c->cost_centre)->row();
		$sub_account = json_decode($cost_centre->id_sub_account);


		foreach($sub_account as $s) {
			$sa = get_data('tbl_fact_sub_account',[
				'where' => [
					'id' => $s
				],
				'sort_by' => 'kode',
				'sort' => 'DESC',
			])->row();


			$acc = get_data('tbl_fact_account_cc a',[
				'select' => 'a.*,b.id as id_cost_centre, c.id as id_account',
				'join' => ['tbl_fact_cost_centre b on a.cost_centre = b.kode type LEFT',
						'tbl_fact_account c on a.account_code = c.account_code'
						],
				'where' => [
					'a.cost_centre' => $c->cost_centre,
					'a.sub_account' => $sa->kode,
					'a.is_active' => 1,
					// 'a.account_code' => '721111-2',
				],
				])->result();

			
			$acc_akses = [];
			foreach($acc as $a) {
				$acc_akses[] = $a->account_code;
				$cek = get_data($table2,[
					'where' => [
						'cost_centre' => $c->cost_centre,
						'sub_account' => $sa->kode,
						'account_code' => $a->account_code
					]
	
				])->row();
				$data2 = [];
				if(!isset($cek->id)) {
					$data2['tahun'] = $tahun;
					$data2['id_cost_centre'] = $a->id_cost_centre;
					$data2['cost_centre'] = $a->cost_centre;
					$data2['id_account'] = $a->id_account;
					$data2['account_code'] = $a->account_code;
					$data2['account_name'] = $a->account_name;
					$data2['sub_account'] = $a->sub_account;

					insert_data($table2,$data2);
				}
			}		
			

			// if(count($acc_akses))
      
			// delete_data($table2,['account_code not'=> $acc_akses, 'cost_centre'=>$cc,'sub_account'=>$sa->kode]); 

		}
	}

	
	render([
		'status'	=> 'success',
		'message'	=> 'Recalculate Sukses'
	],'json');	

	// echo 'success update ' .$jum . ' data' ;

}