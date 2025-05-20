<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');

class Actual_sales extends BE_Controller {
	var $controller = 'Actual_sales';
	function __construct() {
		parent::__construct();
	}

	function index() {
		$data['tahun'] = get_data('tbl_fact_tahun_budget', [
            'where' => [
                'is_active' => 1,
                'tahun' => user('tahun_budget') -1 
            ]
        ])->result();     

		$access         = get_access($this->controller);
        $data['access'] = $access ;
		render($data);
	}

	function data($tahun="",$bulan="") {

		$config =[
	        'access_edit'	=> false,
	        'access_delete'	=> false,
	        'access_view'	=> false,
	    ];
		
		if($tahun) {
	    	$config['where']['tahun']	= $tahun;	
	    }

		if($bulan) {
	    	$config['where']['bulan']	= $bulan;	
	    }

		$data = data_serverside($config);
		render($data,'json');
	}

	function get_data() {
		$data = get_data('tbl_actual_gross_profit','id',post('id'))->row_array();
		render($data,'json');
	}

	function save() {
		$response = save_data('tbl_actual_gross_profit',post(),post(':validation'));
		render($response,'json');
	}

	function delete() {
		$response = destroy_data('tbl_actual_gross_profit','id',post('id'));
		render($response,'json');
	}

	function template() {
		ini_set('memory_limit', '-1');
		$arr = ['tahun' => 'tahun','bulan' => 'bulan','product_code' => 'product_code','description' => 'description','pl_code' => 'pl_code','desc_pl' => 'desc_pl','factory' => 'factory','address' => 'address','sector' => 'sector','group_sector' => 'group_sector','qty_sales' => 'qty_sales','sales_amount' => 'sales_amount','discount' => 'discount','cogs' => 'cogs','unit_cogs' => 'unit_cogs','cogs_idle' => 'cogs_idle','cogs_loss' => 'cogs_loss','gross_prpofit' => 'gross_prpofit','customer' => 'customer','is_active' => 'is_active'];
		$config[] = [
			'title' => 'template_import_actual_sales',
			'header' => $arr,
		];
		$this->load->library('simpleexcel',$config);
		$this->simpleexcel->export();
	}

	function import() {
		ini_set('memory_limit', '-1');
        ini_set('max_execution_time', -1);
		$file = post('fileimport');

		$col = ['tahun','bulan','product_code','description','pl_code','desc_pl','factory','address','sector','group_sector','qty_sales','sales_amount','discount','cogs','unit_cogs','cogs_idle','cogs_loss','gross_prpofit','customer','is_active'];
		$this->load->library('simpleexcel');
		$this->simpleexcel->define_column($col);
		$jml = $this->simpleexcel->read($file);
		$c = 0;

		delete_data('tbl_actual_gross_profit',[
			'tahun' => post('tahun_import'),
			'bulan' => post('bulan_import')
		]);

		foreach($jml as $i => $k) {
			if($i==0) {
				for($j = 2; $j <= $k; $j++) {
					$data = $this->simpleexcel->parsing($i,$j);
					$data['qty_sales'] = str_replace(['.',','],'',$data['qty_sales']);
					$data['sales_amount'] = str_replace(['.',','],'',$data['sales_amount']);
					$data['discount'] = str_replace(['.',','],'',$data['discount']);
					$data['cogs'] = str_replace(['.',','],'',$data['cogs']);
					$data['unit_cogs'] = str_replace(['.',','],'',$data['unit_cogs']);
					$data['cogs_idle'] = str_replace(['.',','],'',$data['cogs_idle']);
					$data['cogs_loss'] = str_replace(['.',','],'',$data['cogs_loss']);
					$data['gross_prpofit'] = str_replace(['.',','],'',$data['gross_prpofit']);
					$data['create_at'] = date('Y-m-d H:i:s');
					$data['create_by'] = user('nama');
					$save = insert_data('tbl_actual_gross_profit',$data);
					if($save) $c++;
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
		$arr = ['tahun'=>'Tahun','bulan' => 'Bulan','product_code' => 'Product Code','description' => 'Description','pl_code' => 'Pl Code','desc_pl' => 'Desc Pl','factory' => 'Factory','address' => 'Address','sector' => 'Sector','group_sector' => 'Group Sector','qty_sales' => 'Qty Sales','sales_amount' => 'Sales Amount','discount' => 'Discount','cogs' => 'Cogs','unit_cogs' => 'Unit Cogs','cogs_idle' => 'Cogs Idle','cogs_loss' => 'Cogs Loss','gross_prpofit' => 'Gross Prpofit','customer' => 'Customer','is_active' => 'Aktif'];
		$data = get_data('tbl_actual_gross_profit')->result_array();
		$config = [
			'title' => 'data_actual_sales',
			'data' => $data,
			'header' => $arr,
		];
		$this->load->library('simpleexcel',$config);
		$this->simpleexcel->export();
	}

	function proses(){
		ini_set('memory_limit', '-1');
		ini_set('max_execution_time', 0);

        $tahun = user('tahun_budget');
		$tahun_actual = post('tahun');
		$bulan = post('bulan');


        $table1 = 'tbl_budget_qtysales_' .$tahun;
        $table2 = 'tbl_budget_discount_' . $tahun;  // discount
        $table3 = 'tbl_budget_cogs_' . $tahun;  // cogs
        $table4 = 'tbl_budget_unitcogs_' . $tahun;  // unitcogs
		$table5 = 'tbl_budget_pricelist_' . $tahun;  // pricelist
		$table6 = 'tbl_budget_grsales_' . $tahun;  // gross_sales
		$table7 = 'tbl_budget_netsales_' . $tahun;  // net_sales

		$table8 = 'tbl_budget_cogsidle_' . $tahun;  // net_sales
		$table9 = 'tbl_budget_cogsloss_' . $tahun;  // net_sales


		$cproduk = get_data('tbl_fact_product a',[
			'where' => [	
				'is_active' => 1,
				// 'code' => 'TMTRX30NDM'
			],
		])->result();

		// debug($cproduk);die;

		foreach($cproduk as $p) {   
			$sector = get_data('tbl_sector_price','is_active',1)->result();
			foreach($sector as $s) {
				$cek1 = get_data($table1 . ' a',[
					'select' => 'a.*',
					'where' => [
						'a.tahun' => $tahun,
						'a.divisi' => $p->divisi,
						'a.id_budget_product' => $p->id,
						'a.budget_product_code' => $p->code,
						'a.product_line' => $p->product_line,
						'a.budget_product_sector' => $s->id,
					]
				])->row();

				$cek2 = get_data($table2 . ' a',[
					'select' => 'a.*',
					'where' => [
						'a.tahun' => $tahun,
						'a.divisi' => $p->divisi,
						'a.id_budget_product' => $p->id,
						'a.budget_product_code' => $p->code,
						'a.product_line' => $p->product_line,
						'a.budget_product_sector' => $s->id,
					]
				])->row();

				$cek3 = get_data($table3 . ' a',[
					'select' => 'a.*',
					'where' => [
						'a.tahun' => $tahun,
						'a.divisi' => $p->divisi,
						'a.id_budget_product' => $p->id,
						'a.budget_product_code' => $p->code,
						'a.product_line' => $p->product_line,
						'a.budget_product_sector' => $s->id,
					]
				])->row();

				$cek4 = get_data($table4 . ' a',[
					'select' => 'a.*',
					'where' => [
						'a.tahun' => $tahun,
						'a.divisi' => $p->divisi,
						'a.id_budget_product' => $p->id,
						'a.budget_product_code' => $p->code,
						'a.product_line' => $p->product_line,
						'a.budget_product_sector' => $s->id,
					]
				])->row();

				$cek5 = get_data($table5 . ' a',[
					'select' => 'a.*',
					'where' => [
						'a.tahun' => $tahun,
						'a.divisi' => $p->divisi,
						'a.id_budget_product' => $p->id,
						'a.budget_product_code' => $p->code,
						'a.product_line' => $p->product_line,
						'a.budget_product_sector' => $s->id,
					]
				])->row();

				$cek6 = get_data($table6 . ' a',[
					'select' => 'a.*',
					'where' => [
						'a.tahun' => $tahun,
						'a.divisi' => $p->divisi,
						'a.id_budget_product' => $p->id,
						'a.budget_product_code' => $p->code,
						'a.product_line' => $p->product_line,
						'a.budget_product_sector' => $s->id,
					]
				])->row();


				$cek7 = get_data($table7 . ' a',[
					'select' => 'a.*',
					'where' => [
						'a.tahun' => $tahun,
						'a.divisi' => $p->divisi,
						'a.id_budget_product' => $p->id,
						'a.budget_product_code' => $p->code,
						'a.product_line' => $p->product_line,
						'a.budget_product_sector' => $s->id,
					]
				])->row();

				
				$cek8 = get_data($table8 . ' a',[
					'select' => 'a.*',
					'where' => [
						'a.tahun' => $tahun,
						'a.divisi' => $p->divisi,
						'a.id_budget_product' => $p->id,
						'a.budget_product_code' => $p->code,
						'a.product_line' => $p->product_line,
						'a.budget_product_sector' => $s->id,
					]
				])->row();

				$cek9 = get_data($table9 . ' a',[
					'select' => 'a.*',
					'where' => [
						'a.tahun' => $tahun,
						'a.divisi' => $p->divisi,
						'a.id_budget_product' => $p->id,
						'a.budget_product_code' => $p->code,
						'a.product_line' => $p->product_line,
						'a.budget_product_sector' => $s->id,
					]
				])->row();

				if(!isset($cek1->id)){
					insert_data($table1,
					['tahun' => $tahun, 'divisi' => $p->divisi, 'product_line' => $p->product_line, 'id_budget_product'=>$p->id, 'budget_product_code'=>$p->code, 
					'budget_product_name' => $p->product_name, 'category' => $p->sub_product,'budget_product_sector'=>$s->id]
					);
				}


				if(!isset($cek2->id)){
					insert_data($table2,
					['tahun' => $tahun, 'divisi' => $p->divisi, 'product_line' => $p->product_line, 'id_budget_product'=>$p->id, 'budget_product_code'=>$p->code, 
					'budget_product_name' => $p->product_name, 'category' => $p->sub_product,'budget_product_sector'=>$s->id]
					);
				}

				if(!isset($cek3->id)){
					insert_data($table3,
					['tahun' => $tahun, 'divisi' => $p->divisi, 'product_line' => $p->product_line, 'id_budget_product'=>$p->id, 'budget_product_code'=>$p->code, 
					'budget_product_name' => $p->product_name, 'category' => $p->sub_product,'budget_product_sector'=>$s->id]
					);
				}

				if(!isset($cek4->id)){
					insert_data($table4,
					['tahun' => $tahun, 'divisi' => $p->divisi, 'product_line' => $p->product_line, 'id_budget_product'=>$p->id, 'budget_product_code'=>$p->code, 
					'budget_product_name' => $p->product_name, 'category' => $p->sub_product,'budget_product_sector'=>$s->id]
					);
				}

				if(!isset($cek5->id)){
					insert_data($table5,
					['tahun' => $tahun, 'divisi' => $p->divisi, 'product_line' => $p->product_line, 'id_budget_product'=>$p->id, 'budget_product_code'=>$p->code, 
					'budget_product_name' => $p->product_name, 'category' => $p->sub_product,'budget_product_sector'=>$s->id]
					);
				}

				if(!isset($cek6->id)){
					insert_data($table6,
					['tahun' => $tahun, 'divisi' => $p->divisi, 'product_line' => $p->product_line, 'id_budget_product'=>$p->id, 'budget_product_code'=>$p->code, 
					'budget_product_name' => $p->product_name, 'category' => $p->sub_product,'budget_product_sector'=>$s->id]
					);
				}

				if(!isset($cek7->id)){
					insert_data($table7,
					['tahun' => $tahun, 'divisi' => $p->divisi, 'product_line' => $p->product_line, 'id_budget_product'=>$p->id, 'budget_product_code'=>$p->code, 
					'budget_product_name' => $p->product_name, 'category' => $p->sub_product,'budget_product_sector'=>$s->id]
					);
				}

				if(!isset($cek8->id)){
					insert_data($table8,
					['tahun' => $tahun, 'divisi' => $p->divisi, 'product_line' => $p->product_line, 'id_budget_product'=>$p->id, 'budget_product_code'=>$p->code, 
					'budget_product_name' => $p->product_name, 'category' => $p->sub_product,'budget_product_sector'=>$s->id]
					);
				}

				if(!isset($cek9->id)){
					insert_data($table9,
					['tahun' => $tahun, 'divisi' => $p->divisi, 'product_line' => $p->product_line, 'id_budget_product'=>$p->id, 'budget_product_code'=>$p->code, 
					'budget_product_name' => $p->product_name, 'category' => $p->sub_product,'budget_product_sector'=>$s->id]
					);
				}

			}
		}

		$this->db->where('CONCAT(divisi,budget_product_code,product_line) not in (select CONCAT(divisi,code,product_line) from tbl_fact_product)');
		$this->db->delete($table1);

		$this->db->where('CONCAT(divisi,budget_product_code,product_line) not in (select CONCAT(divisi,code,product_line) from tbl_fact_product)');
		$this->db->delete($table2);

		$this->db->where('CONCAT(divisi,budget_product_code,product_line) not in (select CONCAT(divisi,code,product_line) from tbl_fact_product)');
		$this->db->delete($table3);
		
		$this->db->where('CONCAT(divisi,budget_product_code,product_line) not in (select CONCAT(divisi,code,product_line) from tbl_fact_product)');
		$this->db->delete($table4);
		
		$this->db->where('CONCAT(divisi,budget_product_code,product_line) not in (select CONCAT(divisi,code,product_line) from tbl_fact_product)');
		$this->db->delete($table5);
		
		$this->db->where('CONCAT(divisi,budget_product_code,product_line) not in (select CONCAT(divisi,code,product_line) from tbl_fact_product)');
		$this->db->delete($table6);
		
		$this->db->where('CONCAT(divisi,budget_product_code,product_line) not in (select CONCAT(divisi,code,product_line) from tbl_fact_product)');
		$this->db->delete($table7);
		
		$this->db->where('CONCAT(divisi,budget_product_code,product_line) not in (select CONCAT(divisi,code,product_line) from tbl_fact_product)');
		$this->db->delete($table8);

		$this->db->where('CONCAT(divisi,budget_product_code,product_line) not in (select CONCAT(divisi,code,product_line) from tbl_fact_product)');
		$this->db->delete($table9);

		$actual = get_data('tbl_actual_gross_profit',[
            'select' => 'pl_code,product_code,bulan,sector,sum(qty_sales) as qty_sales, sum(sales_amount) as sales_amount,
						 sum(discount) as discount, sum(cogs) as cogs, sum(cogs_idle) as cogs_idle, sum(cogs_loss) as cogs_loss',
            'where' => [
			    'tahun' => $tahun_actual,
				'bulan' => $bulan,
                // 'product_code' => 'CIKKN3BNDM',
            ],
            'group_by' => 'pl_code,product_code,bulan,sector',
		    ])->result();

        $field1 = "";
        $field2 = "";
        $field3 = "";
        $field4 = "";
		$field5 = "";
		$field6 = "";
		$field7 = "";
		$field8 = "";
		$field9 = "";
        for ($i = 1; $i <= 12; $i++) { 
            $field1 = 'qB_' . sprintf('%02d', $i);
            $$field1 = 0;

            $field2 = 'dB_' . sprintf('%02d', $i);
            $$field2 = 0;

            $field3 = 'cB_' . sprintf('%02d', $i);
            $$field3 = 0;

            $field4 = 'uB_' . sprintf('%02d', $i);
            $$field4 = 0;

			$field5 = 'pB_' . sprintf('%02d', $i);
            $$field5 = 0;

			$field6 = 'gB_' . sprintf('%02d', $i);
            $$field6 = 0;
            
			$field7 = 'nB_' . sprintf('%02d', $i);
            $$field7 = 0;

			$field8 = 'ciB_' . sprintf('%02d', $i);
            $$field8 = 0;

			$field9 = 'clB_' . sprintf('%02d', $i);
            $$field9 = 0;			
        }

		
				
		$field = "";
		$field = 'EST_' . sprintf('%02d', $bulan);

		update_data($table1,[$field=>0]);
		update_data($table2,[$field=>0]);
		update_data($table3,[$field=>0]);
		update_data($table4,[$field=>0]);
		update_data($table5,[$field=>0]);
		update_data($table6,[$field=>0]);
		update_data($table7,[$field=>0]);
		update_data($table8,[$field=>0]);
		update_data($table9,[$field=>0]);

		$this->db->set('total_est', '(EST_01+EST_02+EST_03+EST_04+EST_05+EST_06+EST_07+EST_08+EST_09+EST_10+EST_11+EST_12)', FALSE);
		$this->db->update($table6);

		$this->db->set('total_est', '(EST_01+EST_02+EST_03+EST_04+EST_05+EST_06+EST_07+EST_08+EST_09+EST_10+EST_11+EST_12)', FALSE);
		$this->db->update($table7);
	

		
		foreach ($actual as $a) {
			switch ($a->bulan) {
				case "01"; 
					$qB_01 = ($a->qty_sales != 0 ? $a->qty_sales : 0);
					$pB_01 = ($a->qty_sales != 0 ? $a->sales_amount / $a->qty_sales : 0);
					$dB_01 = ($a->discount != 0 ? $a->discount : 0);
					$cB_01 = ($a->cogs != 0 ? $a->cogs : 0);
					$uB_01 = (($a->qty_sales != 0 && $a->cogs != 0) ? $a->cogs / $a->qty_sales : 0);
					$gB_01 = $a->sales_amount ;
					$nB_01 = ($a->sales_amount + $a->discount);

					$ciB_01 = ($a->cogs_idle);
					$clB_01 = ($a->cogs_loss);
					break;
				case "02":
					$qB_02 = ($a->qty_sales != 0 ? $a->qty_sales : 0);
					$pB_02 = ($a->qty_sales != 0 ? $a->sales_amount / $a->qty_sales : 0);
					$dB_02 = ($a->discount != 0 ? $a->discount : 0);
					$cB_02 = ($a->cogs != 0 ? $a->cogs : 0);
					$uB_02 = (($a->qty_sales != 0 && $a->cogs != 0) ? $a->cogs / $a->qty_sales : 0);
					$gB_02 = $a->sales_amount ;
					$nB_02 = ($a->sales_amount + $a->discount);

					$ciB_02 = ($a->cogs_idle);
					$clB_02 = ($a->cogs_loss);
					break;
				case "03":
					$qB_03 = ($a->qty_sales != 0 ? $a->qty_sales : 0);
					$pB_03 = ($a->qty_sales != 0 ? $a->sales_amount / $a->qty_sales : 0);
					$dB_03 = ($a->discount != 0 ? $a->discount : 0);
					$cB_03 = ($a->cogs != 0 ? $a->cogs : 0);
					$uB_03 = (($a->qty_sales != 0 && $a->cogs != 0) ? $a->cogs / $a->qty_sales : 0);
					$gB_03 = $a->sales_amount ;
					$nB_03 = ($a->sales_amount + $a->discount);

					$ciB_03 = ($a->cogs_idle);
					$clB_03 = ($a->cogs_loss);
					break;
				case "04";
					$qB_04 = ($a->qty_sales != 0 ? $a->qty_sales : 0);
					$pB_04 = ($a->qty_sales != 0 ? $a->sales_amount / $a->qty_sales : 0);
					$dB_04 = ($a->discount != 0 ? $a->discount  : 0);
					$cB_04 = ($a->cogs != 0 ? $a->cogs : 0);
					$uB_04 = (($a->qty_sales != 0 && $a->cogs != 0) ? $a->cogs / $a->qty_sales : 0);
					$gB_04 = $a->sales_amount ;
					$nB_04 = ($a->sales_amount + $a->discount);

					$ciB_04 = ($a->cogs_idle);
					$clB_04 = ($a->cogs_loss);
					break;
				case "05":
					$qB_05 = ($a->qty_sales != 0 ? $a->qty_sales : 0);
					$pB_05 = ($a->qty_sales != 0 ? $a->sales_amount / $a->qty_sales : 0);
					$dB_05 = ($a->discount != 0 ? $a->discount : 0);
					$cB_05 = ($a->cogs != 0 ? $a->cogs : 0);
					$uB_05 = (($a->qty_sales != 0 && $a->cogs != 0) ? $a->cogs / $a->qty_sales : 0);
					$gB_05 = $a->sales_amount ;
					$nB_05 = ($a->sales_amount + $a->discount);

					$ciB_05 = ($a->cogs_idle);
					$clB_05 = ($a->cogs_loss);
					break;
				case "06":
					$qB_06 = ($a->qty_sales != 0 ? $a->qty_sales : 0);
					$pB_06 = ($a->qty_sales != 0 ? $a->sales_amount / $a->qty_sales : 0);
					$dB_06 = ($a->discount != 0 ? $a->discount  : 0);
					$cB_06 = ($a->cogs != 0 ? $a->cogs : 0);
					$uB_06 = (($a->qty_sales != 0 && $a->cogs != 0) ? $a->cogs / $a->qty_sales : 0);
					$gB_06 = $a->sales_amount ;
					$nB_06 = ($a->sales_amount + $a->discount);

					$ciB_06 = ($a->cogs_idle);
					$clB_06 = ($a->cogs_loss);
					break;
				case "07";
					$qB_07 = ($a->qty_sales != 0 ? $a->qty_sales : 0);
					$pB_07 = ($a->qty_sales != 0 ? $a->sales_amount / $a->qty_sales : 0);
					$dB_07 = ($a->discount != 0 ? $a->discount : 0);
					$cB_07 = ($a->cogs != 0 ? $a->cogs : 0);
					$uB_07 = (($a->qty_sales != 0 && $a->cogs != 0) ? $a->cogs / $a->qty_sales : 0);
					$gB_07 = $a->sales_amount ;
					$nB_07 = ($a->sales_amount + $a->discount);

					$ciB_07 = ($a->cogs_idle);
					$clB_07 = ($a->cogs_loss);
					break;
				case "08":
					$qB_08 = ($a->qty_sales != 0 ? $a->qty_sales : 0);
					$pB_08 = ($a->qty_sales != 0 ? $a->sales_amount / $a->qty_sales : 0);
					$dB_08 = ($a->discount != 0 ? $a->discount : 0);
					$cB_08 = ($a->cogs != 0 ? $a->cogs : 0);
					$uB_08 = (($a->qty_sales != 0 && $a->cogs != 0) ? $a->cogs / $a->qty_sales : 0);
					$gB_08 = $a->sales_amount ;
					$nB_08 = ($a->sales_amount + $a->discount);

					$ciB_08 = ($a->cogs_idle);
					$clB_08 = ($a->cogs_loss);
					break;
				case "09":
					$qB_09 = ($a->qty_sales != 0 ? $a->qty_sales : 0);
					$pB_09 = ($a->qty_sales != 0 ? $a->sales_amount / $a->qty_sales : 0);
					$dB_09 = ($a->discount != 0 ? $a->discount : 0);
					$cB_09 = ($a->cogs != 0 ? $a->cogs : 0);
					$uB_09 = (($a->qty_sales != 0 && $a->cogs != 0) ? $a->cogs / $a->qty_sales : 0);
					$gB_09 = $a->sales_amount ;
					$nB_09 = ($a->sales_amount + $a->discount);

					$ciB_09 = ($a->cogs_idle);
					$clB_09 = ($a->cogs_loss);
					break;
				case "10":
					$qB_10 = ($a->qty_sales != 0 ? $a->qty_sales : 0);
					$pB_10 = ($a->qty_sales != 0 ? $a->sales_amount / $a->qty_sales : 0);
					$dB_10 = ($a->discount != 0 ? $a->discount  : 0);
					$cB_10 = ($a->cogs != 0 ? $a->cogs : 0);
					$uB_10 = (($a->qty_sales != 0 && $a->cogs != 0) ? $a->cogs / $a->qty_sales : 0);
					$gB_10 = $a->sales_amount ;
					$nB_10 = ($a->sales_amount + $a->discount);

					$ciB_10 = ($a->cogs_idle);
					$clB_10 = ($a->cogs_loss);
					break;
				case "11":
					$qB_11 = ($a->qty_sales != 0 ? $a->qty_sales : 0);
					$pB_11 = ($a->qty_sales != 0 ? $a->sales_amount / $a->qty_sales : 0);
					$dB_11 = ($a->discount != 0 ? $a->discount : 0);
					$cB_11 = ($a->cogs != 0 ? $a->cogs : 0);
					$uB_11 = (($a->qty_sales != 0 && $a->cogs != 0) ? $a->cogs / $a->qty_sales : 0);
					$gB_11 = $a->sales_amount ;
					$nB_11 = ($a->sales_amount + $a->discount);

					$ciB_11 = ($a->cogs_idle);
					$clB_11 = ($a->cogs_loss);
				case "12":
					$qB_12 = ($a->qty_sales != 0 ? $a->qty_sales : 0);
					$pB_12 = ($a->qty_sales != 0 ? $a->sales_amount / $a->qty_sales : 0);
					$dB_12 = ($a->discount != 0 ? $a->discount  : 0);
					$cB_12 = ($a->cogs != 0 ? $a->cogs : 0);
					$uB_12 = (($a->qty_sales != 0 && $a->cogs != 0) ? $a->cogs / $a->qty_sales : 0);
					$gB_12 = $a->sales_amount ;
					$nB_12 = ($a->sales_amount + $a->discount);

					$ciB_12 = ($a->cogs_idle);
					$clB_12 = ($a->cogs_loss);
					break;
				default:
					echo "The color is neither red, blue, nor green!";
			}

			if($a->sector == "REGULER") {
				$budget_product_sector = 1;
				$is_regular = 1;
			}elseif($a->sector == "E-CATALOG"){
				$budget_product_sector = 2;
				$is_regular = 0;
			}elseif($a->sector == "IN-HEALTH") {
				$budget_product_sector = 3;
				$is_regular = 0;
			}elseif($a->sector == "ASKES") {
				$budget_product_sector = 4;
				$is_regular = 0;
			}elseif($a->sector == "HARGA KHUSUS") {
				$budget_product_sector = 5;
				$is_regular = 0;
			}else{
				$budget_product_sector = 0;
				$is_regular = 0;
			}



            $arr            = [
                'select'    => 'a.*',
                'where'     => [
                    'a.budget_product_code' => $a->product_code,
					// 'a.product_line' => $a->pl_code,
					'a.budget_product_sector' => $budget_product_sector,
                ],
            ];

			$cek1 = get_data($table1 . ' a',$arr)->row();
            $cek2 = get_data($table2 . ' a',$arr)->row();
            $cek3 = get_data($table3 . ' a',$arr)->row();
            $cek4 = get_data($table4 . ' a',$arr)->row();
			$cek5 = get_data($table5 . ' a',$arr)->row();
			$cek6 = get_data($table6 . ' a',$arr)->row();
			$cek7 = get_data($table7 . ' a',$arr)->row();

			$cek8 = get_data($table8 . ' a',$arr)->row();
			$cek9 = get_data($table9 . ' a',$arr)->row();


			if(isset($cek1->budget_product_code)) {	 
                $field1 = "" ;
                $field = "";
                $field = 'EST_' . sprintf('%02d', $a->bulan);
                $field1 = 'qB_' . sprintf('%02d', $a->bulan);                    
                update_data($table1,[$field=>$$field1],['id'=>$cek1->id]);
			}

            if(isset($cek2->budget_product_code)) {	 
                $field2 = "" ;
                $field = "";
                $field = 'EST_' . sprintf('%02d', $a->bulan);
                $field2 = 'dB_' . sprintf('%02d', $a->bulan);                    
                update_data($table2,[$field=>$$field2],['id'=>$cek2->id]);
			}

            if(isset($cek3->budget_product_code)) {	 
                $field3 = "" ;
                $field = "";
                $field = 'EST_' . sprintf('%02d', $a->bulan);
                $field3 = 'cB_' . sprintf('%02d', $a->bulan);                    
                update_data($table3,[$field=>$$field3],['id'=>$cek3->id]);
			}

            if(isset($cek4->budget_product_code)) {	 
                $field4 = "" ;
                $field = "";
                $field = 'EST_' . sprintf('%02d', $a->bulan);
                $field4 = 'uB_' . sprintf('%02d', $a->bulan);                    
                update_data($table4,[$field=>$$field4],['id'=>$cek4->id]);
			}
            if(isset($cek5->budget_product_code)) {	 
                $field5 = "" ;
                $field = "";
                $field = 'EST_' . sprintf('%02d', $a->bulan);
                $field5= 'pB_' . sprintf('%02d', $a->bulan);                    
                update_data($table5,[$field=>$$field5],['id'=>$cek5->id]);
			}
			if(isset($cek6->budget_product_code)) {	 
                $field6 = "" ;
                $field = "";
                $field = 'EST_' . sprintf('%02d', $a->bulan);
                $field6 = 'gB_' . sprintf('%02d', $a->bulan);                    
                // update_data($table6,[$field=>$$field6],['id'=>$cek6->id]);
				$this->db->set($field, $$field6);
				$this->db->set('total_est', '(EST_01+EST_02+EST_03+EST_04+EST_05+EST_06+EST_07+EST_08+EST_09+EST_10+EST_11+EST_12)', FALSE);
				$this->db->where('id', $cek6->id);
				$this->db->update($table6);
			}
			if(isset($cek7->budget_product_code)) {	 
                $field7 = "" ;
                $field = "";
                $field = 'EST_' . sprintf('%02d', $a->bulan);
                $field7 = 'nB_' . sprintf('%02d', $a->bulan);                    
                // update_data($table7,[$field=>$$field7,'total_est1'=> '(EST_01+EST_02+EST_03+EST_04+EST_05+EST_06+EST_07+EST_08+EST_09+EST_10+EST_11+EST_12)'],['id'=>$cek7->id]);
				$this->db->set($field, $$field7);
				$this->db->set('total_est', '(EST_01+EST_02+EST_03+EST_04+EST_05+EST_06+EST_07+EST_08+EST_09+EST_10+EST_11+EST_12)', FALSE);
				$this->db->where('id', $cek7->id);
				$this->db->update($table7);
			}

			if(isset($cek8->budget_product_code)) {	 
                $field8 = "" ;
                $field = "";
                $field = 'EST_' . sprintf('%02d', $a->bulan);
                $field8 = 'ciB_' . sprintf('%02d', $a->bulan);                    
                update_data($table8,[$field=>$$field8],['id'=>$cek8->id]);
			}

			if(isset($cek9->budget_product_code)) {	 
                $field9 = "" ;
                $field = "";
                $field = 'EST_' . sprintf('%02d', $a->bulan);
                $field9 = 'clB_' . sprintf('%02d', $a->bulan);                    
                update_data($table9,[$field=>$$field9],['id'=>$cek9->id]);
			}
		}
		render([
			'status'	=> 'success',
			'message'	=> 'Posting Actual Sales data has benn succesfuly'
		],'json');	
	}
}