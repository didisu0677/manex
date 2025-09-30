<div class="content-header">
	<div class="main-container position-relative">
		<div class="header-info">
			<div cl			// Load ERQ (Edited Requirement Quantity) data jika ada
			if(response.erq) {
				$.each(response.erq, function(k, v) {
					if(v) {
						for (let i = 1; i <= 12; i++) {
							let field0 = `xproduksi_${String(i).padStart(2, '0')}`;
							let field1 = `P_${String(i).padStart(2, '0')}`;
							if(v[field1] != undefined && v[field1] != '') {
								$('#' + field0 + v.id).text(numberFormat(v[field1], 0));  
								$('#' + field0 + v.id).attr('data-value', v[field1]); // Update data-value
								$('#' + field0 + v.id).addClass('edited'); // Mark as edited
							}
						}
					}
				});
			}tle"><?php echo $title; ?></div>
			<?php echo breadcrumb(); ?>
		</div>
		<div class="float-right">
            <label class=""><?php echo lang('tahun'); ?> &nbsp</label>
			<select class="select2 infinity custom-select" style="width: 80px;" id="filter_tahun">
				<?php foreach ($tahun as $tahun) { ?>
					<option value="<?php echo $tahun->tahun; ?>" <?php if ($tahun->tahun == user('tahun_budget')) echo ' selected'; ?>><?php echo $tahun->tahun; ?></option>
				<?php } ?>
			</select>

			<label class=""><?php echo lang('supplier'); ?> &nbsp</label>
			<select class="select2 custom-select" style="width: 280px;" id="filter_supplier">
				<option value="ALL">ALL</option>
				<?php foreach ($supplier as $p) { ?>
					<option value="<?php echo $p->code; ?>"><?php echo $p->code . ' | ' . $p->nama; ?></option>
				<?php } ?>
			</select>

			<!-- <label class=""><?php echo lang('factory'); ?>  &nbsp</label>
			<select class="select2 infinity custom-select" style="width: 180px;" id="filter_cost_centre">
				<option value="ALL">ALL FACTORY</option>
				<?php //foreach ($cc as $c) { ?>
                <option value="<?php //echo $c->kode; ?>"><?php echo $c->cost_centre; ?></option>
                <?php //} ?>
			</select> -->

			<?php  

			echo '<button class="btn btn-info btn-proses" href="javascript:;" ><i class="fa-process"></i> Running MRP</button>';
            echo '<button class="btn btn-success btn-save" href="javascript:;" > Save <span class="fa-save"></span></button>';

            $arr = [];
                $arr = [
                    // ['btn-save','Save Data','fa-save'],
                    ['btn-export','Export Data','fa-upload'],
                    // ['btn-import','Import Data Begining Stock','fa-download'],
                    // ['btn-template','Template Import','fa-reg-file-alt']
                ];
            echo access_button('',$arr); 
            ?>
	
        </div>
		<div class="clearfix"></div>
	</div>
</div>



<div class="content-body mt-6">
	<div class="main-container mt-2">
		<div class="row">
			<div class="col-sm-12">
				<div class="card">
	    			<!-- <div class="card-body"> -->
	    				<div class="table-responsive tab-pane fade active show height-window">
	    				<?php
						table_open('table table-bordered table-app table-hover table-1');
							thead();
								tr();
									th('PT Otsuka Indonesia ','','width="60" colspan="13" class="text-left"');
								tr();
									th('Supply Chain Department ','','width="60" colspan="13" class="text-left"');
								tr();
									th('Inventory Analysis ','','width="60" colspan="13" class="text-left"');

                                    tr();
									th('Inventory unit analysis','','width="360" rowspan="2" class="text-center align-middle headcol"');
  									for ($i = 1; $i <= 12; $i++) { 
										th(month_lang($i),'','class="text-center" style="min-width:80px"');		
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
$(document).ready(function() {
	getData();
});

$('#filter_tahun').change(function() {
	getData();
});

$('#filter_supplier').change(function() {
	getData();
});

function getData() {
	cLoader.open(lang.memuat_data + '...');
	$('.overlay-wrap').removeClass('hidden');
	var page = base_url + 'material_cost/material_planning/data';
		page += '/' + $('#filter_tahun').val();
		page += '/' + $('#filter_supplier').val();

	$.ajax({
		url: page,
		data: {},
		type: 'get',
		dataType: 'json',
		success: function(response) {
			$('.table-1 tbody').html(response.table);
			
			// Data ERQ sudah ditampilkan langsung dari server-side di table.php
			
			// Call calculate after data loaded
			calculate();
			
			cLoader.close();
			$('.overlay-wrap').addClass('hidden');
		}
	});
}

var id_proses = '';
var tahun = 0;
$(document).on('click', '.btn-proses', function(e) {
	e.preventDefault();
	id_proses = 'proses';
	tahun = $('#filter_tahun').val();
	supplier = $('#filter_supplier').val();
	cConfirm.open(lang.apakah_anda_yakin + '?', 'lanjut');
});

function lanjut() {
	$.ajax({
		url: base_url + 'material_cost/material_planning/proses',
		data: {id: id_proses, tahun: tahun, supplier: supplier},
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

// Event handler untuk xproduksi - sama seperti production planning
$(document).on('keyup', '[class*="xproduksi_"]', function(e) {
	var wh = e.which;
	if ((48 <= wh && wh <= 57) || (96 <= wh && wh <= 105) || wh == 8) {
		// Mark as user edited
		$(this).addClass('user-edited');
		
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

$(document).on('keypress', '[class*="xproduksi_"]', function(e) {
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

// Trigger calculate - sama seperti production planning
$(document).on('keyup', '[class*="xproduksi_"]', function(e) {
	if (e.keyCode === 13 || e.key === 'Enter' || e.keyCode === 9 || e.key === 'Tab') {
		calculate();
	}
});

// Mark element as user-edited when focus
$(document).on('focus', '[class*="xproduksi_"]', function() {
	$(this).addClass('user-edited');
});

// Allow auto-calculation after user finished editing (blur event)
$(document).on('blur', '[class*="xproduksi_"]', function() {
	let val = parseFloat($(this).text().replace(/,/g, '')) || 0;
	let originalValue = parseFloat($(this).attr('data-value') || 0);
	
	$(this).text(numberFormat(val));
	
	// Mark as edited jika value berubah dari original (suggestion)
	if (val !== originalValue) {
		$(this).addClass('edited');
	} else {
		$(this).removeClass('edited');
	}
	
	// Remove user-edited class after a delay to allow auto-calculation again
	let element = $(this);
	setTimeout(function() {
		element.removeClass('user-edited');
	}, 1000); // Wait 1 second after blur before allowing auto-calculation
	
	calculate();
});

$(document).on('click', '.btn-save', function() {
	var i = 0;
	$('.edited').each(function() {
		i++;
	});
	if (i == 0) {
		cAlert.open('Tidak ada data yang diubah');
	} else {
		var msg = lang.anda_yakin_menyetujui;
		cConfirm.open(msg, 'save_perubahan');
	}
});

function save_perubahan() {
	var i = 0;
	
	// Collect edited data
	let edited_data = [];
	
	$('.edited').each(function() {
		if($(this).hasClass('xproduksi_01') || $(this).hasClass('xproduksi_02') || $(this).hasClass('xproduksi_03') || 
		   $(this).hasClass('xproduksi_04') || $(this).hasClass('xproduksi_05') || $(this).hasClass('xproduksi_06') || 
		   $(this).hasClass('xproduksi_07') || $(this).hasClass('xproduksi_08') || $(this).hasClass('xproduksi_09') || 
		   $(this).hasClass('xproduksi_10') || $(this).hasClass('xproduksi_11') || $(this).hasClass('xproduksi_12')) {
			
			edited_data.push({
				material_code: $(this).data('material-code'),
				month: $(this).data('month'),
				value: $(this).text().replace(/,/g, '')
			});
			i++;
		}
	});

	if (i == 0) {
		cAlert.open('Tidak ada data yang diubah');
		return;
	}

	$.ajax({
		url: base_url + 'material_cost/material_planning/save_perubahan',
		data: {
			tahun: $('#filter_tahun').val(),
			edited_data: JSON.stringify(edited_data),
		},
		type: 'post',
		dataType: 'json',
		success: function(response) {
			cAlert.open(response.message, response.status, 'refreshData');
		}
	});
}

function refreshData() {
	getData();
}

function calculate() {
	// Objek untuk menyimpan data per kolom - sama seperti production planning
	$('.table-1 tbody tr').each(function() {
		let columnData = {
			B_01: 0, B_02: 0, B_03: 0, B_04: 0, B_05: 0, B_06: 0,
			B_07: 0, B_08: 0, B_09: 0, B_10: 0, B_11: 0, B_12: 0
		};

		let columnData1 = {
			prod_01: 0, prod_02: 0, prod_03: 0, prod_04: 0, prod_05: 0, prod_06: 0,
			prod_07: 0, prod_08: 0, prod_09: 0, prod_10: 0, prod_11: 0, prod_12: 0
		};

		// Proses setiap bulan secara berurutan
		for (let i = 1; i <= 12; i++) {
			let key = `B_${String(i).padStart(2, '0')}`;
			let key1 = `prod_${String(i).padStart(2, '0')}`;
			let key_begining_stock = `begining_stock_${String(i).padStart(2, '0')}`;
			let key_sales = `sales_${String(i).padStart(2, '0')}`;
			let key_end_stock = `end_stock_${String(i).padStart(2, '0')}`;
			let key_m_cov = `m_cov_${String(i).padStart(2, '0')}`;
			
			let budget = moneyToNumber($(this).find(`.xproduksi_${String(i).padStart(2, '0')}`).text().replace(/\,/g, ''));
			let value_xproduction = $(this).find(`.xproduksi_${String(i).padStart(2, '0')}`).text().replace(/\,/g, '').trim();
			let nilai = $(this).find(`.xproduksi_${String(i).padStart(2, '0')}`).data('nilai');
			let idx = $(this).find(`.xproduksi_${String(i).padStart(2, '0')}`).data('id');
			
			if (!isNaN(idx) && idx) {
				let total = budget * nilai;
				
				// Biarkan user edit termasuk nilai 0, cek jika tidak undefined/null/empty string
				if (value_xproduction !== '' && value_xproduction !== undefined && value_xproduction !== null) {
					columnData[key] += budget * nilai;
					columnData1[key1] += budget * nilai;
					
					let value_sales = parseInt($('#' + key_sales + idx).text().replace(/\,/g, '')) || 0;
					let value_begining_stock = parseInt($('#' + key_begining_stock + idx).text().replace(/\,/g, '')) || 0;
					
					// Ambil nilai m_cov dan moq dari data attribute
					let max_coverage = parseFloat($(this).find(`.xproduksi_${String(i).padStart(2, '0')}`).data('m-cov')) || 0;
					let moq = parseInt($(this).find(`.xproduksi_${String(i).padStart(2, '0')}`).data('moq')) || 0;
					
					// Calculate average sales untuk 4 bulan ke depan
					let value_total_sales = 0;
					let divide_number = 0;
					for (let j = 0; j < 4; j++) {
						if (j + i > 12) {
							continue;
						}
						let value_sales_future = parseInt($(`#sales_${String(j + i).padStart(2, '0')}${idx}`).text().replace(/\,/g, '')) || 0;
						value_total_sales += value_sales_future;
						divide_number++;
					}

					let average_sales = divide_number > 0 ? value_total_sales / divide_number : 0;
					
					// Logic seperti production planning: jangan override yang sudah edited
					let arrivalElement = $(this).find(`.xproduksi_${String(i).padStart(2, '0')}`);
					let isUserManualInput = arrivalElement.is(':focus') || arrivalElement.hasClass('user-edited');
					let isEdited = arrivalElement.hasClass('edited');
					
					let arrival_qty = parseFloat(arrivalElement.text().replace(/,/g, '')) || 0;
					let unitAvailableForUse = value_begining_stock + arrival_qty;
					let new_end_stock = unitAvailableForUse - value_sales;
					let coverage = average_sales > 0 ? new_end_stock / average_sales : 0;
					
					// Auto-calculate HANYA jika belum edited dan coverage kurang
					if (!isEdited && coverage < max_coverage && max_coverage > 0 && moq > 0 && average_sales > 0 && !isUserManualInput) {
						// Hitung arrival_qty minimum untuk mencapai target coverage
						let required_end_stock = max_coverage * average_sales;
						let required_arrival = required_end_stock + value_sales - value_begining_stock;
						
						// Bulatkan ke MOQ terdekat (ke atas), tapi jika required_arrival <= 0, set ke 0
						if (required_arrival > 0) {
							arrival_qty = Math.ceil(required_arrival / moq) * moq;
						} else {
							arrival_qty = 0;
						}
						
						// Recalculate dengan arrival_qty yang baru
						unitAvailableForUse = value_begining_stock + arrival_qty;
						new_end_stock = unitAvailableForUse - value_sales;
						coverage = average_sales > 0 ? new_end_stock / average_sales : 0;
						
						// Update arrival qty di display (jika tidak sedang di-edit)
						if (!arrivalElement.is(':focus')) {
							arrivalElement.text(numberFormat(arrival_qty));
						}
					}
					
					// Update displays
					$('#unit_available_' + String(i).padStart(2, '0') + idx).text(numberFormat(unitAvailableForUse));
					
					let txt_new_end_stock = new_end_stock < 0 ? '-' + (numberFormat(Math.abs(new_end_stock), 0)) : numberFormat(new_end_stock, 0);
					$('#' + key_end_stock + idx).text(txt_new_end_stock);
					
					if (!Number.isFinite(coverage) || isNaN(coverage)) {
						$('#' + key_m_cov + idx).text(numberFormat(0, 2));
					} else {
						$('#' + key_m_cov + idx).text(numberFormat(coverage, 2));
					}

					// Update beginning stock untuk bulan berikutnya
					// Beginning stock bulan februari dan seterusnya adalah end stock bulan sebelumnya
					for (let j = i; j <= 12; j++) {
						let value_end_stock_current = $(`#end_stock_${String(j).padStart(2, '0')}${idx}`).text();
						$(`#begining_stock_${String(j + 1).padStart(2, '0')}${idx}`).text(value_end_stock_current);
					}
				}
			}
		}
	});
}

// Helper function untuk mengkonversi formatted number ke number - sesuai production planning
function moneyToNumber(value) {
	if (typeof value === 'string') {
		let cleaned = value.replace(/[^0-9\-]/g, '');
		if (cleaned === '') return 0;
		return parseInt(cleaned) || 0;
	}
	if (value === null || value === undefined) return 0;
	return Number(value) || 0;
}

function parseNumber(str) {
	return parseInt(str.replace(/\,/g,'').trim()) || 0;
}

// Helper function untuk format number
function numberFormat(number, decimals = 0) {
	if (isNaN(number)) return '0';
	return Number(number).toLocaleString('en-US', {
		minimumFractionDigits: decimals,
		maximumFractionDigits: decimals
	});
}
</script>