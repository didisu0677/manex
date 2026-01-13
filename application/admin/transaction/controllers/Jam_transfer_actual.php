<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');

class Jam_transfer_actual extends BE_Controller {

	function __construct() {
		parent::__construct();
	}

	function index() {
		$data['tahun'] = get_data('tbl_fact_tahun_budget', [
            'where' => [
                'is_active' => 1,
                'tahun' => user('tahun_budget') - 1
            ]
        ])->result();   
		render($data);
	}

	function data() {
		$data = data_serverside();
		render($data,'json');
	}

	function get_data() {
		$data = get_data('tbl_jam_transfer_allocation_actual','id',post('id'))->row_array();
		render($data,'json');
	}

	function save() {
		$response = save_data('tbl_jam_transfer_allocation_actual',post(),post(':validation'));
		if($response['status'] == 'success') {
			// Get the saved data
			$saved_id = isset($response['id']) ? $response['id'] : post('id');
			$saved_data = get_data('tbl_jam_transfer_allocation_actual','id',$saved_id)->row_array();
			
			// If saved_data is empty, use post data
			if(empty($saved_data)) {
				$saved_data = post();
			}
			
			// Format bulan menjadi 2 digit (01-12)
			$bulan = sprintf('%02d', $saved_data['bulan']);
			$field_amount = 'amount_' . $bulan;
			
			// Check if cost_centre_asal and cost_centre_tujuan exist
			if(!empty($saved_data['cost_centre_asal']) && !empty($saved_data['cost_centre_tujuan']) && !empty($saved_data['account_code'])) {
				// Jika allocation_amount kosong atau 0, set menjadi 0
				$allocation_amount = !empty($saved_data['allocation_amount']) ? $saved_data['allocation_amount'] : 0;
				
				// Check and insert/update first row: cost_centre_asal with negative amount
				$cek_asal = get_data('tbl_jam_transfer', [
					'where' => [
						'account_code' => $saved_data['account_code'],
						'cost_centre' => $saved_data['cost_centre_asal'],
						'tahun' => $saved_data['tahun'],
					]
				])->row();
				
				$data_transfer_asal = [
					'account_code' => $saved_data['account_code'],
					'cost_centre' => $saved_data['cost_centre_asal'],
					$field_amount => -abs($allocation_amount), // Negative value (bisa 0 jika allocation_amount = 0)
					'tahun' => $saved_data['tahun'],
				];
				
				if(isset($cek_asal->id)) {
					// Update existing data
					$data_transfer_asal['update_at'] = date('Y-m-d H:i:s');
					$data_transfer_asal['update_by'] = user('nama');
					update_data('tbl_jam_transfer', $data_transfer_asal, 'id', $cek_asal->id);
				} else {
					// Insert new data (hanya jika allocation_amount > 0)
					if($allocation_amount > 0) {
						$data_transfer_asal['create_at'] = date('Y-m-d H:i:s');
						$data_transfer_asal['create_by'] = user('nama');
						insert_data('tbl_jam_transfer', $data_transfer_asal);
					}
				}
				
				// Check and insert/update second row: cost_centre_tujuan with positive amount
				$cek_tujuan = get_data('tbl_jam_transfer', [
					'where' => [
						'account_code' => $saved_data['account_code'],
						'cost_centre' => $saved_data['cost_centre_tujuan'],
						'tahun' => $saved_data['tahun'],
					]
				])->row();
				
				$data_transfer_tujuan = [
					'account_code' => $saved_data['account_code'],
					'cost_centre' => $saved_data['cost_centre_tujuan'],
					$field_amount => abs($allocation_amount), // Positive value (bisa 0 jika allocation_amount = 0)
					'tahun' => $saved_data['tahun'],
				];
				
				if(isset($cek_tujuan->id)) {
					// Update existing data
					$data_transfer_tujuan['update_at'] = date('Y-m-d H:i:s');
					$data_transfer_tujuan['update_by'] = user('nama');
					update_data('tbl_jam_transfer', $data_transfer_tujuan, 'id', $cek_tujuan->id);
				} else {
					// Insert new data (hanya jika allocation_amount > 0)
					if($allocation_amount > 0) {
						$data_transfer_tujuan['create_at'] = date('Y-m-d H:i:s');
						$data_transfer_tujuan['create_by'] = user('nama');
						insert_data('tbl_jam_transfer', $data_transfer_tujuan);
					}
				}
			}
			
			$cek = get_data('tbl_jam_transfer_allocation_actual',[
				'select' => 'account_code, sum(allocation_amount) as total',
				'where' => [
					'tahun' => post('tahun'),
					'bulan' => post('bulan'),
				],
				'group_by' => 'account_code',
			])->result_array();

		}
		render($response,'json');
	}

	function delete() {
		// Get data sebelum dihapus untuk menghapus data terkait di tbl_jam_transfer
		$data_to_delete = get_data('tbl_jam_transfer_allocation_actual','id',post('id'))->row_array();
		
		$response = destroy_data('tbl_jam_transfer_allocation_actual','id',post('id'));
		
		if($response['status'] == 'success' && !empty($data_to_delete)) {
			// Format bulan menjadi 2 digit (01-12)
			$bulan = sprintf('%02d', $data_to_delete['bulan']);
			$field_amount = 'amount_' . $bulan;
			
			// Hapus atau set menjadi 0 untuk cost_centre_asal
			if(!empty($data_to_delete['cost_centre_asal']) && !empty($data_to_delete['account_code'])) {
				$cek_asal = get_data('tbl_jam_transfer', [
					'where' => [
						'account_code' => $data_to_delete['account_code'],
						'cost_centre' => $data_to_delete['cost_centre_asal'],
						'tahun' => $data_to_delete['tahun'],
					]
				])->row();
				
				if(isset($cek_asal->id)) {
					// Set amount menjadi 0 untuk bulan tersebut
					$data_update_asal = [
						$field_amount => 0,
						'update_at' => date('Y-m-d H:i:s'),
						'update_by' => user('nama')
					];
					update_data('tbl_jam_transfer', $data_update_asal, 'id', $cek_asal->id);
				}
			}
			
			// Hapus atau set menjadi 0 untuk cost_centre_tujuan
			if(!empty($data_to_delete['cost_centre_tujuan']) && !empty($data_to_delete['account_code'])) {
				$cek_tujuan = get_data('tbl_jam_transfer', [
					'where' => [
						'account_code' => $data_to_delete['account_code'],
						'cost_centre' => $data_to_delete['cost_centre_tujuan'],
						'tahun' => $data_to_delete['tahun'],
					]
				])->row();
				
				if(isset($cek_tujuan->id)) {
					// Set amount menjadi 0 untuk bulan tersebut
					$data_update_tujuan = [
						$field_amount => 0,
						'update_at' => date('Y-m-d H:i:s'),
						'update_by' => user('nama')
					];
					update_data('tbl_jam_transfer', $data_update_tujuan, 'id', $cek_tujuan->id);
				}
			}
		}
		
		render($response,'json');
	}

	function template() {
		ini_set('memory_limit', '-1');
		$arr = ['tahun' => 'tahun','bulan' => 'bulan','account_code' => 'account_code','allocation_amount' => 'allocation_amount','cost_centre_asal' => 'cost_centre_asal','cost_centre_tujuan' => 'cost_centre_tujuan','is_active' => 'is_active'];
		$config[] = [
			'title' => 'template_import_jam_transfer_actual',
			'header' => $arr,
		];
		$this->load->library('simpleexcel',$config);
		$this->simpleexcel->export();
	}

	function import() {
		ini_set('memory_limit', '-1');
		$file = post('fileimport');
		$col = ['tahun','bulan','account_code','allocation_amount','cost_centre_asal','cost_centre_tujuan','is_active'];
		$this->load->library('simpleexcel');
		$this->simpleexcel->define_column($col);
		$jml = $this->simpleexcel->read($file);
		$c = 0;
		foreach($jml as $i => $k) {
			if($i==0) {
				for($j = 2; $j <= $k; $j++) {
					$data = $this->simpleexcel->parsing($i,$j);
					$data['create_at'] = date('Y-m-d H:i:s');
					$data['create_by'] = user('nama');
					$save = insert_data('tbl_jam_transfer_allocation_actual',$data);
					if($save) {
						$c++;
						// Insert data to tbl_jam_transfer if cost_centre_asal and cost_centre_tujuan exist
						if(!empty($data['cost_centre_asal']) && !empty($data['cost_centre_tujuan']) && !empty($data['account_code']) && !empty($data['allocation_amount'])) {
							$tahun = isset($data['tahun']) ? $data['tahun'] : (user('tahun_budget') - 1);
							$bulan = isset($data['bulan']) ? $data['bulan'] : setting('actual_budget');
							// Format bulan menjadi 2 digit (01-12)
							$bulan_formatted = sprintf('%02d', $bulan);
							$field_amount = 'amount_' . $bulan_formatted;
							
							// Check and insert/update first row: cost_centre_asal with negative amount
							$cek_asal = get_data('tbl_jam_transfer', [
								'where' => [
									'account_code' => $data['account_code'],
									'cost_centre' => $data['cost_centre_asal'],
									'tahun' => $tahun,
								]
							])->row();
							
							$data_transfer_asal = [
								'account_code' => $data['account_code'],
								'cost_centre' => $data['cost_centre_asal'],
								$field_amount => -abs($data['allocation_amount']), // Negative value
								'tahun' => $tahun,
							];
							
							if(isset($cek_asal->id)) {
								// Update existing data
								$data_transfer_asal['update_at'] = date('Y-m-d H:i:s');
								$data_transfer_asal['update_by'] = user('nama');
								update_data('tbl_jam_transfer', $data_transfer_asal, 'id', $cek_asal->id);
							} else {
								// Insert new data
								$data_transfer_asal['create_at'] = date('Y-m-d H:i:s');
								$data_transfer_asal['create_by'] = user('nama');
								insert_data('tbl_jam_transfer', $data_transfer_asal);
							}
							
							// Check and insert/update second row: cost_centre_tujuan with positive amount
							$cek_tujuan = get_data('tbl_jam_transfer', [
								'where' => [
									'account_code' => $data['account_code'],
									'cost_centre' => $data['cost_centre_tujuan'],
									'tahun' => $tahun,
								]
							])->row();
							
							$data_transfer_tujuan = [
								'account_code' => $data['account_code'],
								'cost_centre' => $data['cost_centre_tujuan'],
								$field_amount => abs($data['allocation_amount']), // Positive value
								'tahun' => $tahun,
							];
							
							if(isset($cek_tujuan->id)) {
								// Update existing data
								$data_transfer_tujuan['update_at'] = date('Y-m-d H:i:s');
								$data_transfer_tujuan['update_by'] = user('nama');
								update_data('tbl_jam_transfer', $data_transfer_tujuan, 'id', $cek_tujuan->id);
							} else {
								// Insert new data
								$data_transfer_tujuan['create_at'] = date('Y-m-d H:i:s');
								$data_transfer_tujuan['create_by'] = user('nama');
								insert_data('tbl_jam_transfer', $data_transfer_tujuan);
							}
						}
					}
				}
			}
		}
		$response = [
			'status' => 'success',
			'message' => $c.' '.lang('data_berhasil_disimpan').'.'
		];
		@unlink($file);
		render($response,'json');
	}

	function export() {
		ini_set('memory_limit', '-1');
		$arr = ['tahun' => 'Tahun','bulan' => 'Bulan','account_code' => 'Account Code','allocation_amount' => 'Allocation Amount','cost_centre_asal' => 'Cost Centre Asal','cost_centre_tujuan' => 'Cost Centre Tujuan','is_active' => 'Aktif'];
		$data = get_data('tbl_jam_transfer_allocation_actual')->result_array();
		$config = [
			'title' => 'data_jam_transfer_actual',
			'data' => $data,
			'header' => $arr,
		];
		$this->load->library('simpleexcel',$config);
		$this->simpleexcel->export();
	}

}