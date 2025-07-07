<div class="content-header">
	<div class="main-container position-relative">
		<div class="header-info">
			<div class="content-title"><?php echo $title; ?></div>
			<?php echo breadcrumb(); ?>
		</div>
		<div class="float-right">
			<label class=""><?php echo lang('tahun'); ?> &nbsp</label>
			<select class="select2 infinity custom-select" style="width: 80px;" id="filter_tahun">
				<?php foreach ($tahun as $tahun) { ?>
					<option value="<?php echo $tahun->tahun; ?>" <?php if ($tahun->tahun == user('tahun_budget')) echo ' selected'; ?>><?php echo $tahun->tahun; ?></option>
				<?php } ?>
			</select>

			<label class=""><?php echo lang('factory'); ?> &nbsp</label>
			<select class="select2 infinity custom-select" style="width: 180px;" id="filter_cost_centre">
				<option value="ALL">ALL FACTORY</option>
				<?php foreach ($cc as $c) { ?>
					<option value="<?php echo $c->kode; ?>"><?php echo $c->cost_centre; ?></option>
				<?php } ?>
			</select>

			<?php
			if($submit==0) {
				echo '<button class="btn btn-info btn-proses" href="javascript:;" ><i class="fa-process"></i> Running MRP</button>';
				echo '<button class="btn btn-success btn-save" href="javascript:;" > Save <span class="fa-save"></span></button>';
				// echo '<button class="btn btn-secondary btn-submit-production" href="javascript:;" > Submit Production <span class="fa-save"></span></button>';
			}

			$arr = [];
			$arr = [
				// ['btn-save','Save Data','fa-save'],
				['btn-export', 'Export Data', 'fa-upload'],
				// ['btn-import', 'Import Data Begining Stock', 'fa-download'],
				// ['btn-template', 'Template Import', 'fa-reg-file-alt']
			];
			echo access_button('', $arr);
			?>

		</div>
		<div class="clearfix"></div>
	</div>
</div>

<div class="content-body mt-6">
	<div class="main-container mt-2">
		<div class="row">
			<div class="col-sm-12">
				<div class="card" id="result">
					<!-- <div class="card-body"> -->
					<div class="table-responsive tab-pane fade active show height-window">
						<?php
						table_open('table table-bordered table-app table-hover table-1');
						thead();
						tr();
						th('Revisi ke : ', '', 'width="60" colspan="5" class="text-left"');
						for ($i = 1; $i <= 12; $i++) {
							th('', '', 'class="text-center" style="min-width:80px"');
						}
						th(lang('total'), '', 'width="60" rowspan="2" class="text-center align-middle headcol"');

						tr();
						th(lang('description'), '', 'width="60" rowspan="2" class="text-center align-middle headcol"');
						th(lang('code'), '', 'width="60" rowspan="2" class="text-center align-middle headcol"');
						th(lang('dest'), '', 'width="60" rowspan="2" class="text-center align-middle headcol"');
						th(lang('batch'), '', 'width="60" rowspan="2" class="text-center align-middle headcol"');
						th(lang(''), '', 'width="60" rowspan="2" class="text-center align-middle headcol"');
						for ($i = 1; $i <= 12; $i++) {
							th(month_lang($i), '', 'class="text-center" style="min-width:80px"');
						}
						tbody();
						table_close();
						?>
					</div>
					<!-- </div> -->
				</div>
			</div>
		</div>
	</div>
</div>

<script>

	let typingTimer;
	let doneTypingInterval = 0;

	$(document).ready(function() {
		getData();
		// calculate()
		// $(document).on('keyup', '.xproduksi,.produksi', function(e) {
		// 	// calculate();
		// 	if (e.keyCode === 13 || e.key === 'Enter' || e.keyCode === 9 || e.key === 'Tab') {
		// 		calculate(); // Panggil fungsi calculate()
		// 	}
		// });
	});

	$('#filter_tahun').change(function() {
		getData()
	});

	$('#filter_cost_centre').change(function() {
		getData()
	});

	function getData() {
		cLoader.open(lang.memuat_data + '...');
		$('.overlay-wrap').removeClass('hidden');
		var page = base_url + 'material_cost/production_planning/data';
		page += '/' + $('#filter_tahun').val();
		page += '/' + $('#filter_cost_centre').val();

		$.ajax({
			url: page,
			data: {},
			type: 'get',
			dataType: 'json',
			success: function(response) {
				$('.table-1 tbody').html(response.table);
				// $('.xproduksi').text('1');
				$.each(response.epr,function(k,v){
					for (let i = 1; i <= 12; i++) {
						let field0 = `xproduksi_${String(i).padStart(2, '0')}`
						let field1 = `P_${String(i).padStart(2, '0')}`
						if(v[field1] != 0) {
							$('#' + field0 + v.id).text(numberFormat(v[field1],0));  
						}
					}
				});

				calculate();
				cLoader.close();
				$('.overlay-wrap').addClass('hidden');
				// money_init();
			}
		});
	}

	var id_proses = '';
	var tahun = 0;
	$(document).on('click', '.btn-proses', function(e) {
		e.preventDefault();
		id_proses = 'proses';
		tahun = $('#filter_tahun').val();
		factory = $('#filter_cost_centre').val();
		cConfirm.open(lang.apakah_anda_yakin + '?', 'lanjut');
	});

	function lanjut() {
		let result = $.ajax({
			url: base_url + 'material_cost/production_planning/proses',
			data: {
				id: id_proses,
				tahun: tahun,
				factory: factory
			},
			type: 'post',
			dataType: 'json',
			success: function(res) {
				cAlert.open(res.message, res.status, 'refreshData');
			}
		});
	}

	$(document).on('focus', '.edit-value', function() {
		$(this).parent().removeClass('edited');
	});

	$(document).on('blur', '.edit-value', function() {
		var tr = $(this).closest('tr');
		if ($(this).text() != $(this).attr('data-value')) {
			$(this).addClass('edited');
		}
		if (tr.find('td.edited').length > 0) {
			tr.addClass('edited-row');
		} else {
			tr.removeClass('edited-row');
		}
	});

	$(document).on('keyup', '.edit-value', function(e) {
		var wh = e.which;
		if ((48 <= wh && wh <= 57) || (96 <= wh && wh <= 105) || wh == 8) {
			if ($(this).text() == '') {
				$(this).text('');
			} else {
				var n = parseInt($(this).text().replace(/[^0-9\-]/g, ''), 10);
				$(this).text(n.toLocaleString());
				var selection = window.getSelection();
				var range = document.createRange();
				selection.removeAllRanges();
				range.selectNodeContents($(this)[0]);
				range.collapse(false);
				selection.addRange(range);
				$(this)[0].focus();
			}
		}
	});

	$(document).on('keypress', '.edit-value', function(e) {
		var wh = e.which;
		if (e.shiftKey) {
			if (wh == 0) return true;
		}
		if (e.metaKey || e.ctrlKey) {
			if (wh == 86 || wh == 118) {
				$(this)[0].onchange = function() {
					$(this)[0].innerHTML = $(this)[0].innerHTML.replace(/[^0-9\-]/g, '');
				}
			}
			return true;
		}
		if (wh == 0 || wh == 8 || wh == 45 || (48 <= wh && wh <= 57) || (96 <= wh && wh <= 105))
			return true;
		return false;
	});

	$(document).on('click', '.btn-save', function() {
		var i = 0;
		$('.edited').each(function() {
			i++;
		});
		if (i == 0) {
			cAlert.open('tidak ada data yang di ubah');
		} else {
			var msg = lang.anda_yakin_menyetujui;
			if (i == 0) msg = lang.anda_yakin_menolak;
			cConfirm.open(msg, 'save_perubahan');
		}

	});

	$(document).on('click','.btn-submit-production',function(){
		// prepare data production sebelum dipost
		let $production = $('[data-type="production"]');
		let list_production_value = []
		let list_production_product_code = []
		let list_production_month = []
		let list_production_edit = []
		$.each($production, function(i, v){
			list_production_value.push($(v).text().replace(/\,/g,''))
			list_production_product_code.push($(v).data('product-code'))
			list_production_month.push($(v).data('month'))
			list_production_edit.push($(v).data('edit'))
		})

		// submit data untuk disimpan
		$.ajax({
			url: base_url + 'material_cost/production_planning/submit_production',
			data: {
				tahun: $('#filter_tahun').val(),
				production_value: list_production_value,
				production_product: list_production_product_code,
				production_month: list_production_month,
				production_edit: list_production_edit
			},
			type: 'post',
			success: function(response) {
				cAlert.open(response, 'success', 'refreshData');
			}
		})
	})

	function save_perubahan() {
		var data_edit = {};
		var i = 0;

		$('.edited').each(function() {
			if($(this).attr('data-type') == 'x-production'){
				var content = $(this).children('div');
				if (typeof data_edit[$(this).attr('data-id')] == 'undefined') {
					data_edit[$(this).attr('data-id')] = {};
				}
				data_edit[$(this).attr('data-id')][$(this).attr('data-name')] = $(this).text().replace(/[^0-9\-]/g, '');
				i++;
			}
			// if (typeof data_type[$(this).attr('data-id')] == 'undefined') {
			// 	data_type[$(this).attr('data-id')] = {};
			// }
			// data_type[$(this).attr('data-id')][$(this).attr('data-name')] = $(this).attr('data-type');
		});

		// prepare data x production sebelum submit
		let $xproduction = $('[data-type="x-production"]')
		let list_xproduction_value = []
		let list_xproduction_product_code = []
		let list_xproduction_month = []
		$.each($xproduction, function(i, v){
			list_xproduction_value.push($(v).text().replace(/\,/g,''))
			list_xproduction_product_code.push($(v).data('product-code'))
			list_xproduction_month.push($(v).data('month'))
		})

		// prepare data production sebelum dipost
		let $production = $('[data-type="production"]');
		let list_production_value = []
		let list_production_product_code = []
		let list_production_month = []
		let list_production_edit = []
		$.each($production, function(i, v){
			list_production_value.push($(v).text().replace(/\,/g,''))
			list_production_product_code.push($(v).data('product-code'))
			list_production_month.push($(v).data('month'))
			list_production_edit.push($(v).data('edit'))
		})

		var jsonString = JSON.stringify(data_edit);
		$.ajax({
			url: base_url + 'material_cost/production_planning/save_perubahan',
			data: {
				'json': jsonString,
				verifikasi: i,
				tahun: $('#filter_tahun').val(),
				production_value: list_production_value,
				production_product: list_production_product_code,
				production_month: list_production_month,
				production_edit: list_production_edit,
				xproduction_value: list_xproduction_value,
				xproduction_product: list_xproduction_product_code,
				xproduction_month: list_xproduction_month,
			},
			type: 'post',
			success: function(response) {
				cAlert.open(response, 'success', 'refreshData');
			}
		})
	}

	$(document).on('keyup', '.xproduksi', function(e) {
		var wh = e.which;
		if ((48 <= wh && wh <= 57) || (96 <= wh && wh <= 105) || wh == 8) {
			if ($(this).text() == '') {
				$(this).text('');
			} else {
				var n = parseInt($(this).text().replace(/[^0-9\-]/g, ''), 10);
				$(this).text(n.toLocaleString());
				var selection = window.getSelection();
				var range = document.createRange();
				selection.removeAllRanges();
				range.selectNodeContents($(this)[0]);
				range.collapse(false);
				selection.addRange(range);
				$(this)[0].focus();
			}
		}
	});

	$(document).on('keypress', '.xproduksi', function(e) {
		var wh = e.which;
		if (e.shiftKey) {
			if (wh == 0) return true;
		}
		if (e.metaKey || e.ctrlKey) {
			if (wh == 86 || wh == 118) {
				$(this)[0].onchange = function() {
					$(this)[0].innerHTML = $(this)[0].innerHTML.replace(/[^0-9\-]/g, '');
				}
			}
			return true;
		}
		if (wh == 0 || wh == 8 || wh == 45 || (48 <= wh && wh <= 57) || (96 <= wh && wh <= 105))
			return true;
		return false;
	});

	function calculate() {
		// Objek untuk menyimpan data per kolom
		$('.table-1 tbody tr').each(function() {
			let columnData = {
				B_01: 0,
				B_02: 0,
				B_03: 0,
				B_04: 0,
				B_05: 0,
				B_06: 0,
				B_07: 0,
				B_08: 0,
				B_09: 0,
				B_10: 0,
				B_11: 0,
				B_12: 0
			};

			let columnData1 = {
				prod_01: 0,
				prod_02: 0,
				prod_03: 0,
				prod_04: 0,
				prod_05: 0,
				prod_06: 0,
				prod_07: 0,
				prod_08: 0,
				prod_09: 0,
				prod_10: 0,
				prod_11: 0,
				prod_12: 0
			};
			// if ($(this).find('.xproduksi').text() !== '') {
				for (let i = 1; i <= 12; i++) {
					let key = `B_${String(i).padStart(2, '0')}`; // Membuat key seperti B_01, B_02, dst.
					let key1 = `prod_${String(i).padStart(2, '0')}`;
					let key_begining_stock = `begining_stock_${String(i).padStart(2, '0')}`;
					let key_sales = `sales_${String(i).padStart(2, '0')}`;
					let key_end_stock = `end_stock_${String(i).padStart(2, '0')}`;
					let key_m_cov = `m_cov_${String(i).padStart(2, '0')}`;
					let budget = moneyToNumber($(this).find(`.xproduksi_${String(i).padStart(2, '0')}`).text().replace(/\,/g, ''));
					let value_xproduction = $(this).find(`.xproduksi_${String(i).padStart(2, '0')}`).text().replace(/\,/g, '')
					let nilai = $(this).find(`.xproduksi_${String(i).padStart(2, '0')}`).data('nilai');
					let idx = $(this).find(`.xproduksi_${String(i).padStart(2, '0')}`).data('id');
					if(!isNaN(idx)){
						let cost_center = $(this).find(`.xproduksi_${String(i).padStart(2, '0')}`).data('cost-center');
						let total = budget * nilai;
						if (value_xproduction != '') {
							columnData[key] += budget * nilai;
							columnData1[key1] += budget * nilai;
								
							let value_sales = $('#' + key_sales + idx).text().replace(/\,/g, '');
							let value_end_stock = $('#' + key_end_stock + idx).text().replace(/\,/g, '');
							let value_begining_stock = $('#' + key_begining_stock + idx).text().replace(/\,/g, '');
							let value_production = columnData1[key1];

							// merubah value produksi
							if($('#' + key1 + idx).attr('data-edit') === '0'){
								$('#' + key1 + idx).text(numberFormat(total,0));
							} else {
								value_production = $('#' + key1 + idx).text().replace(/\,/g,'')
							}

							let new_end_stock = parseInt(value_begining_stock) + parseInt(value_production) - parseInt(value_sales)
							let txt_new_end_stock = new_end_stock < 0 ? '-' + (numberFormat(new_end_stock, 0).replace(/[()]/g, '')) : numberFormat(new_end_stock, 0)
							$('#' + key_end_stock + idx).text(txt_new_end_stock);
							let value_total_sales = 0
							let divide_number = 0
							for (let j = 0; j < 4; j++) {
								if (j + i > 12) {
									continue;
								}
								let value_sales = parseInt($(`#sales_${String(j+i).padStart(2, '0')}${idx}`).text().replace(/\,/g, ''))
								value_total_sales += !isNaN(value_sales) ? value_sales : 0
								divide_number++;
							}

							let coverage = new_end_stock / (value_total_sales / divide_number);
							if (!Number.isFinite(coverage) || isNaN(coverage)) {
								$('#' + key_m_cov + idx).text(numberFormat(0, 2));
							} else {
								$('#' + key_m_cov + idx).text(numberFormat(coverage, 2));
							}


							// update begining stock
							for (let j = i; j <= 12; j++) {
								let value_end_stock = $(`#end_stock_${String(j).padStart(2, '0')}${idx}`).text();
								$(`#begining_stock_${String(j+1).padStart(2, '0')}${idx}`).text(value_end_stock);
							}
						}
					}
				}
			// }
		});
		calculate_grand_total_by_cost_center()
	}

	$(document).on('keyup','[data-type="production"]',function(){
		let value_produksi = $(this).text().replace(/\,/g,'')
		let cost_center = $(this).data('cost-center')
		let month = $(this).data('month')
		let product_code = $(this).data('product-code')
		$(this).attr('data-edit', '1')
		calculate_by_cost_center(cost_center, month, product_code, value_produksi)
	})

	$(document).on('keyup','[data-type="x-production"]',function(){
		let cost_center = $(this).data('cost-center')
		let month = $(this).data('month')
		let product_code = $(this).data('product-code')
		calculate_by_cost_center(cost_center, month, product_code)
	})

	function calculate_by_cost_center(val_cc, month, product_code, value_produksi = 0){
		clearTimeout(typingTimer);
		for (let i = month; i <= 12; i++) {
			let key = `B_${String(i).padStart(2, '0')}`; // Membuat key seperti B_01, B_02, dst.
			let key1 = `prod_${String(i).padStart(2, '0')}`;
			let key_begining_stock = `begining_stock_${String(i).padStart(2, '0')}`;
			let key_sales = `sales_${String(i).padStart(2, '0')}`;
			let key_end_stock = `end_stock_${String(i).padStart(2, '0')}`;
			let key_m_cov = `m_cov_${String(i).padStart(2, '0')}`;
			let budget = $(`[data-type="x-production"][data-cost-center="${val_cc}"][data-month="${i}"][data-product-code="${product_code}"]`).data('nilai');
			let value_xproduction = $(`[data-type="x-production"][data-cost-center="${val_cc}"][data-month="${i}"][data-product-code="${product_code}"]`).text().replace(/\,/g,'')
			let nilai = $(`[data-type="x-production"][data-cost-center="${val_cc}"][data-month="${i}"][data-product-code="${product_code}"]`).text().replace(/\,/g,'')
			let idx = $(`[data-type="x-production"][data-cost-center="${val_cc}"][data-month="${i}"][data-product-code="${product_code}"]`).data('id')
			let cost_center = $(`[data-type="x-production"][data-cost-center="${val_cc}"][data-month="${i}"][data-product-code="${product_code}"]`).data('cost-center')

			let total = budget * nilai;
			if (value_xproduction != '') {
				let value_sales = $(`[data-type="sales"][data-cost-center="${val_cc}"][data-month="${i}"][data-product-code="${product_code}"]`).text().replace(/\,/g, '');
				let value_end_stock = $(`[data-type="end-stock"][data-cost-center="${val_cc}"][data-month="${i}"][data-product-code="${product_code}"]`).text().replace(/\,/g, '');
				let value_begining_stock = $(`[data-type="begining-stock"][data-cost-center="${val_cc}"][data-month="${i}"][data-product-code="${product_code}"]`).text().replace(/\,/g, '');
				let value_production = total;
				// merubah value produksi
				if(val_cc == cost_center && month == i && value_produksi != 0){
					value_production = value_produksi;
				} else {
					if($(`[data-type="production"][data-cost-center="${val_cc}"][data-month="${i}"][data-product-code="${product_code}"]`).attr('data-edit') == 0){
						$(`[data-type="production"][data-cost-center="${val_cc}"][data-month="${i}"][data-product-code="${product_code}"]`).text(numberFormat(total, 0));
					} else {
						value_production = $('#' + key1 + idx).text().replace(/\,/g,'')
					}
				}

				value_production = $(`[data-type="production"][data-cost-center="${val_cc}"][data-month="${i}"][data-product-code="${product_code}"]`).text().replace(/\,/g,'')
				console.log(value_production)
				let new_end_stock = parseInt(value_begining_stock) + parseInt(value_production) - parseInt(value_sales)
				
				let txt_new_end_stock = new_end_stock < 0 ? '-' + (numberFormat(new_end_stock, 0).replace(/[()]/g, '')) : numberFormat(new_end_stock, 0)
				$(`[data-type="end-stock"][data-cost-center="${val_cc}"][data-month="${i}"][data-product-code="${product_code}"]`).text(txt_new_end_stock);
				let value_total_sales = 0
				let divide_number = 0
				for (let j = 0; j < 4; j++) {
					if (j + i > 12) {
						continue;
					}
					let value_sales = parseInt($(`#sales_${String(j+i).padStart(2, '0')}${idx}`).text().replace(/\,/g, ''))
					value_total_sales += !isNaN(value_sales) ? value_sales : 0
					divide_number++;
				}
				let coverage = new_end_stock / (value_total_sales / divide_number);
				if (!Number.isFinite(coverage) || isNaN(coverage)) {
					$(`[data-type="m_cov"][data-cost-center="${val_cc}"][data-month="${i}"][data-product-code="${product_code}"]`).text(numberFormat(0, 2));
				} else {
					$(`[data-type="m_cov"][data-cost-center="${val_cc}"][data-month="${i}"][data-product-code="${product_code}"]`).text(numberFormat(coverage, 2));
				}

				// update begining stock
				for (let j = i; j <= 12; j++) {
					let value_end_stock = $(`#end_stock_${String(j).padStart(2, '0')}${idx}`).text().replace(/\,/g,'');
					let value_prod = $(`#prod_${String(j+1).padStart(2, '0')}${idx}`).text().replace(/\,/g,'')
					let value_sales = $(`#sales_${String(j+1).padStart(2, '0')}${idx}`).text().replace(/\,/g,'')
					$(`#begining_stock_${String(j+1).padStart(2, '0')}${idx}`).text(value_end_stock);
					$(`#end_stock_${String(j+1).padStart(2, '0')}${idx}`).text(value_end_stock + value_prod - value_sales);
				}
			}
		}
		calculate_grand_total_by_cost_center(val_cc)
	}

	let activeTable = '#result';
	let judul = 'Actual and Estimate' 

	$('a[data-toggle="pill"]').on('shown.bs.tab', function (e) {
        var activeTab = $(e.target).attr('href'); // Get the current active tab href attribute
        if(activeTab == '#overall'){
			activeTable = '#result'
			judul = 'Actual and Estimate'
		}else if(activeTab == '#budget'){
			activeTable = '#result2'
			judul = "Budget by Month"
		}else if(activeTab == '#detail'){
			activeTable = '#result3'
			judul = 'Yearly Budget'
		}
    });

	$(document).on('click', '.btn-export', function() {
		var currentdate = new Date();
		var datetime = currentdate.getDate() + "/" +
			(currentdate.getMonth() + 1) + "/" +
			currentdate.getFullYear() + " @ " +
			currentdate.getHours() + ":" +
			currentdate.getMinutes() + ":" +
			currentdate.getSeconds();

		// Set background colors
		// $('.bg-grey-2').attr('bgcolor','#f4f4f4');
		// $('.bg-grey-2').attr('bgcolor','#dddddd');
		// $('.bg-grey-2-1').attr('bgcolor','#b4b4b4');
		// $('.bg-grey-2-2').attr('bgcolor','#aaaaaa');
		// $('.bg-grey-2').attr('bgcolor','#888888');

		var table = '';
		table += '<table>'; // Add border style here

		// Add table rows
		table += '<tr><td colspan="1">PT Otsuka Indonesia</td></tr>';
		table += '<tr><td colspan="1">Production Planning </td></tr>';
		table += '<tr><td colspan="1"> Print date </td><td>: ' + datetime + '</td></tr>';
		table += '</table><br><br>';

		// Add content body
		table += $(activeTable).html();

		var target = table;
		// window.open('data:application/vnd.ms-excel,' + encodeURIComponent(target));

		htmlToExcel(target)
		
		// $('.bg-grey-1,.bg-grey-2.bg-grey-2-1,.bg-grey-2-2,.bg-grey-3').each(function(){
		// 	$(this).removeAttr('bgcolor');
		// });
	});
	
	function parseNumber(str) {
		return parseInt(str.replace(/\,/g,'').trim()) || 0;
	}

	function calculate_grand_total_by_cost_center(selectedCostCenter = "") {
		const parse = parseNumber;
		const format = numberFormat;

		const typeMap = {
			"production": "production",
			"begining-stock": "begining",
			"sales": "sales",
			"end-stock": "end"
		};

		const allItems = {
			production: {},
			begining: {},
			sales: {},
			end: {}
		};

		const outputElements = {};

		// 🔁 Cache semua elemen masuk dan keluar sekali aja
		$('[data-type]').each(function () {
			const el = this; // native element, lebih cepat dari $(this)
			const $el = $(el);
			const type = $el.data('type');
			const month = $el.data('month');
			const costCenter = $el.data('cost-center');

			// Index input data
			const key = typeMap[type];
			if (key && month !== undefined && costCenter !== undefined) {
				if (!selectedCostCenter || selectedCostCenter === costCenter) {
					if (!allItems[key][month]) allItems[key][month] = {};
					if (!allItems[key][month][costCenter]) allItems[key][month][costCenter] = [];
					allItems[key][month][costCenter].push(el);
				}
			}

			// Index output target
			if (
				type &&
				["grand-total-produksi", "total-produksi", "grand-total-begining-stock", "total-begining-stock",
				"grand-total-sales", "total-sales", "grand-total-end-stock", "total-end-stock",
				"grand-production", "grand-begining-stock", "grand-sales", "grand-end-stock"].includes(type)
			) {
				const key = `${type}-${month}-${costCenter ?? "global"}`;
				outputElements[key] = el;
			}
		});

		for (let i = 1; i <= 12; i++) {
			let totalMonth = {
				production: 0,
				begining: 0,
				sales: 0,
				end: 0
			};

			const monthCostCenters = allItems.production?.[i]
				? Object.keys(allItems.production[i])
				: [];

			const costCenters = selectedCostCenter ? [selectedCostCenter] : monthCostCenters;

			costCenters.forEach(costCenter => {
				const items = {
					production: allItems.production?.[i]?.[costCenter] || [],
					begining: allItems.begining?.[i]?.[costCenter] || [],
					sales: allItems.sales?.[i]?.[costCenter] || [],
					end: allItems.end?.[i]?.[costCenter] || []
				};

				let sum = {
					production: 0,
					begining: 0,
					sales: 0,
					end: 0
				};

				items.production.forEach(el => sum.production += parse(el.textContent));
				items.begining.forEach(el => sum.begining += parse(el.textContent));
				items.sales.forEach(el => sum.sales += parse(el.textContent));
				items.end.forEach(el => sum.end += parse(el.textContent));

				totalMonth.production += sum.production;
				totalMonth.begining += sum.begining;
				totalMonth.sales += sum.sales;
				totalMonth.end += sum.end;

				// Simpan update ke DOM output cache
				const update = (type, value) => {
					const key = `${type}-${i}-${costCenter}`;
					if (outputElements[key]) outputElements[key].textContent = format(value, 0);
				};

				update("grand-total-produksi", sum.production);
				update("total-produksi", sum.production);
				update("grand-total-begining-stock", sum.begining);
				update("total-begining-stock", sum.begining);
				update("grand-total-sales", sum.sales);
				update("total-sales", sum.sales);
				update("grand-total-end-stock", sum.end);
				update("total-end-stock", sum.end);
			});

			// Update grand total per bulan
			const updateGlobal = (type, value) => {
				const key = `${type}-${i}-global`;
				if (outputElements[key]) outputElements[key].textContent = format(value, 0);
			};

			updateGlobal("grand-production", totalMonth.production);
			updateGlobal("grand-begining-stock", totalMonth.begining);
			updateGlobal("grand-sales", totalMonth.sales);
			updateGlobal("grand-end-stock", totalMonth.end);
		}
		calculate_left_total()
	}

	function calculate_left_total(){
		$('[data-type="left-total"]').each(function () {
			let total = 0;
			let $cellsBefore = $(this).prevAll('td, th').slice(0, 12).get().reverse();

			$cellsBefore.forEach(function (cell) {
				let val = parseFloat($(cell).text().replace(/[^0-9.-]+/g, "")) || 0;
				total += val;
			});

			$(this).text(numberFormat(total, 0));
		});
	}


</script>