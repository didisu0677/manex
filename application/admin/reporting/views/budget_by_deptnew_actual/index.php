<div class="content-header page-data" data-additional="<?= $access_additional ?>">
	<div class="main-container position-relative">
		<div class="header-info">
			<div class="content-title"><?php echo $title; ?></div>
			<?php echo breadcrumb(); ?>
		</div>
		
		<div class="float-right">
			<label class=""><?php echo lang('tahun'); ?>  &nbsp</label>
			<select class="select2 infinity custom-select" style="width: 80px;" id="filter_tahun">
				<?php foreach ($tahun as $tahun) { ?>
                <option value="<?php echo $tahun->tahun; ?>"<?php if($tahun->tahun == user('tahun_budget')) echo ' selected'; ?>><?php echo $tahun->tahun; ?></option>
                <?php } ?>
			</select>

			<label class=""><?php echo lang('cc'); ?>  &nbsp</label>
			<select class="select2 infinity custom-select" style="width: 180px;" id="filter_cost_centre">
				<option value="ALL">ALL</option>
				<?php foreach ($cc as $c) { ?>
                <option value="<?php echo $c->kode; ?>"><?php echo $c->cost_centre; ?></option>
                <?php } ?>
			</select>
   		
    		<?php 

			if($access['access_input'])
			echo '<button class="btn btn-success btn-save" href="javascript:;" ><i class="fa-save"></i> Save</button>';

			$arr = [];
			$arr = [
				// ['btn-save','Save Data','fa-save'],
				['btn-export','Export Data','fa-upload'],
				($access['access_input'] ? ['btn-act-import','Import Data','fa-download']:''),
				// ['btn-act-template','Template Import','fa-reg-file-alt']
			];
		
		
			echo access_button('',$arr); 

			?>
    		</div>
			<div class="clearfix"></div>
			
		</div>
	</div>

<div class="content-body">
	<div class="card">
		<div class="card-header"><b>ACTUAL VS BUDGET</div>
		<div class="card-body">
			<div class="table-responsive height-window" id="result">
					<?php
					table_open('table table-bordered table-app table-hover table-1');
						thead();
							tr();
							th(lang('account'),'','class="text-center align-middle headcol" style="min-width:250px"');
							for ($i = 1; $i <= 12; $i++) {
								$actual = "ACT";

								th($actual . ' ' . month_lang($i), '', 'class="text-center align-middle" style="min-width:60px"');
							}
							th(lang('total_actual'),'','class="text-center align-middle headcol"style="min-width:60px"');
							th(lang('total_budget'),'','class="text-center align-middle headcol"style="min-width:60px"');
							th(lang('budget_remaining'),'','class="text-center align-middle headcol"style="min-width:60px"');

						tbody();
						table_close();
						?>
			</div>
		</div>
	</div>
</div>

<style>
.table-1 thead th {
	position: sticky !important;
	top: 0 !important;
	z-index: 5 !important;
	background-color: #4a5569 !important;
	color: #fff !important;
	font-weight: bold !important;
}

.table-1 thead th:first-child {
	left: 0 !important;
	z-index: 6 !important;
}

.table-1 tbody td:first-child {
	position: sticky;
	left: 0;
	z-index: 4;
	background-color: #f8f9fa;
	font-weight: bold;
}

.table-1 tbody td:nth-last-child(-n+3) {
	background-color: #f8f9fa;
	font-weight: bold;
}

.height-window {
	height: calc(100vh - 140px);
	overflow: auto;
}

.table-1 th,
.table-1 td {
	white-space: nowrap;
	min-width: 60px;
}

.btn,
.select2,
.custom-select,
input,
select,
button,
textarea {
	pointer-events: auto !important;
	z-index: 1000 !important;
}
</style>
<?php
modal_open('modal-import',lang('impor'));
	modal_body();
		form_open(base_url('reporting/budget_by_deptnew_actual/import'),'post','form-import');
			col_init(3,9);
			input('text',lang('tahun'),'tahun','','','readonly');
			input('text',lang('cost_centre'),'cost_centre','','','readonly');
			
			input('text',lang('tab'),'tab','','','readonly');
			input('text',lang('import_data'),'judul','','','readonly');

			fileupload('File Excel','fileimport','required','data-accept="xls|xlsx"');
			form_button(lang('impor'),lang('batal'));
		form_close();
modal_close();
?>
<script type="text/javascript">

$(document).ready(function () {
	getData();

	$('#filter_cost_centre').trigger('change');
	$(document).on('keyup', '.budget', function (e) {
    	calculate();
    });

	$('#result').on('click', function(){
		activeTable = '#result';
		judul = 'Actual and Estimate';
	});
});	

$('#filter_tahun').change(function(){
	getData();
});

$('#filter_cost_centre').change(function(){
	getData();
});

$('#filter_allocated').change(function(){
	getData();
});

function getData() {

		cLoader.open(lang.memuat_data + '...');
		// $('.overlay-wrap').removeClass('hidden');
		var page = base_url + 'reporting/budget_by_deptnew_actual/data';
			page 	+= '/'+$('#filter_tahun').val();
			page 	+= '/'+$('#filter_cost_centre').val();
			// page    += '/'+$('#filter_allocated').val();

		$.ajax({
			url 	: page,
			data 	: {},
			type	: 'get',
			dataType: 'json',
			success	: function(response) {
				$('.table-1 tbody').html(response.table);
				updateSummaryTotals();
				cLoader.close();

			// $('.overlay-wrap').addClass('hidden');	
			}
		});
}


$(document).on('focus','.edit-value',function(){
	$(this).parent().removeClass('edited');
});
$(document).on('blur','.edit-value',function(){
	var tr = $(this).closest('tr');
	if($(this).text() != $(this).attr('data-value')) {
		$(this).addClass('edited');
	}
	if(tr.find('td.edited').length > 0) {
		tr.addClass('edited-row');
	} else {
		tr.removeClass('edited-row');
	}
});
$(document).on('keyup','.edit-value',function(e){
	var wh 			= e.which;
	if((48 <= wh && wh <= 57) || (96 <= wh && wh <= 105) || wh == 8) {
		if($(this).text() == '') {
			$(this).text('');
		} else {
			var n = parseInt($(this).text().replace(/[^0-9\-]/g,''),10);
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
$(document).on('keypress','.edit-value',function(e){
	var wh 			= e.which;
	if (e.shiftKey) {
		if(wh == 0) return true;
	}
	if(e.metaKey || e.ctrlKey) {
		if(wh == 86 || wh == 118) {
			$(this)[0].onchange = function(){
				$(this)[0].innerHTML = $(this)[0].innerHTML.replace(/[^0-9\-]/g, '');
			}
		}
		return true;
	}
	if(wh == 0 || wh == 8 || wh == 45 || (48 <= wh && wh <= 57) || (96 <= wh && wh <= 105)) 
		return true;
	return false;
});

function calculate() {

	$('#result .table-1 tbody tr').each(function(){
		if($(this).find('.budget').text() != '') {
	
			let B_01 = safeMoneyToNumber($(this).find('.B_01').text())
			let B_02 = safeMoneyToNumber($(this).find('.B_02').text())
			let B_03 = safeMoneyToNumber($(this).find('.B_03').text())
			let B_04 = safeMoneyToNumber($(this).find('.B_04').text())
			let B_05 = safeMoneyToNumber($(this).find('.B_05').text())
			let B_06 = safeMoneyToNumber($(this).find('.B_06').text())
			let B_07 = safeMoneyToNumber($(this).find('.B_07').text())
			let B_08 = safeMoneyToNumber($(this).find('.B_08').text())
			let B_09 = safeMoneyToNumber($(this).find('.B_09').text())
			let B_10 = safeMoneyToNumber($(this).find('.B_10').text())
			let B_11 = safeMoneyToNumber($(this).find('.B_11').text())
			let B_12 = safeMoneyToNumber($(this).find('.B_12').text())

	
			let total_budget = 0
	
			total_budget = B_01+B_02+B_03+B_04+B_05+B_06+B_07+B_08+B_09+B_10+B_11+B_12
			
			var totalActualValue = safeMoneyToNumber($(this).find('.total_le').text());
			var remainingValue = total_budget - totalActualValue;
			setCellValue($(this).find('.total_budget'), total_budget);
			setCellValue($(this).find('.bva_analysis'), remainingValue);
		}
	});

	updateSummaryTotals();
}

function parseMoneyValue(value) {
	var text = (value || '').toString().trim();
	if (text === '' || text === '-' || text === '--') {
		return 0;
	}
	if (typeof moneyToNumber === 'function') {
		return moneyToNumber(text.replace(/,/g, ''));
	}
	var sanitized = text.replace(/[^0-9\-]/g, '');
	return sanitized === '' || sanitized === '-' ? 0 : parseInt(sanitized, 10);
}

function safeMoneyToNumber(text) {
	var value = (text || '').toString().replace(/,/g, '');
	if (typeof moneyToNumber === 'function') {
		return moneyToNumber(value);
	}
	var numeric = value.replace(/[^0-9\-]/g, '');
	if (numeric === '' || numeric === '-') {
		return 0;
	}
	return parseInt(numeric, 10);
}

function formatMoneyValue(value) {
	if (typeof customFormat === 'function') {
		return customFormat(value);
	}
	var n = Math.round(value || 0);
	return n.toLocaleString('en-US');
}

function setCellValue($cell, value) {
	var formatted = formatMoneyValue(value);
	if ($cell.children().length) {
		$cell.children().first().text(formatted).attr('data-value', value);
	} else {
		$cell.text(formatted);
	}
	$cell.attr('data-value', value);
}

function updateSummaryTotals() {
	var subtotalActual = 0;
	var subtotalBudget = 0;
	var grandActual = 0;
	var grandBudget = 0;

	$('#result .table-1 tbody tr').each(function(){
		var $row = $(this);
		var $labelCell = $row.find('td:first, th:first');
		var label = $.trim($labelCell.text()).toUpperCase();

		if (label.indexOf('SUB TOTAL') === 0) {
			var $summaryCells = $row.find('td').slice(-3);
			setCellValue($summaryCells.eq(0), subtotalActual);
			setCellValue($summaryCells.eq(1), subtotalBudget);
			setCellValue($summaryCells.eq(2), subtotalBudget - subtotalActual);
			subtotalActual = 0;
			subtotalBudget = 0;
			return;
		}

		if (label.indexOf('GRAND TOTAL') === 0) {
			var $grandCells = $row.find('td').slice(-3);
			setCellValue($grandCells.eq(0), grandActual);
			setCellValue($grandCells.eq(1), grandBudget);
			setCellValue($grandCells.eq(2), grandBudget - grandActual);
			return;
		}

		var actual = parseMoneyValue($row.find('.total_le').text());
		var budget = parseMoneyValue($row.find('.total_budget').text());

		subtotalActual += actual;
		subtotalBudget += budget;
		grandActual += actual;
		grandBudget += budget;
	});
}
$(document).on('click','.btn-save',function(){
	var i = 0;
	$('.edited').each(function(){
		i++;
	});
	if(i == 0) {
		cAlert.open('tidak ada data yang di ubah');
	} else {
		var msg 	= lang.anda_yakin_menyetujui;
		if( i == 0) msg = lang.anda_yakin_menolak;
		cConfirm.open(msg,'save_perubahan');        
	}

});

function save_perubahan() {
	var data_edit = {};
	var i = 0;
	
	$('.edited').each(function(){
		var content = $(this).children('div');
		if(typeof data_edit[$(this).attr('data-id')] == 'undefined') {
			data_edit[$(this).attr('data-id')] = {};
		}
		data_edit[$(this).attr('data-id')][$(this).attr('data-name')] = $(this).text().replace(/[^0-9\-]/g,'');
		i++;
	});
	
	var jsonString = JSON.stringify(data_edit);		
	$.ajax({
		url : base_url + 'reporting/budget_by_deptnew_actual/save_perubahan',
		data 	: {
			'json' : jsonString,
			verifikasi : i
		},
		type : 'post',
		success : function(response) {
			cAlert.open(response,'success','refreshData');
		}
	})
}

let activeTable = '#result';
let judul = 'Actual and Estimate';

$('.btn-act-import').click(function(){
		$("#modal-import").modal()
		$('#form-import')[0].reset();
		$('#tahun').val($('#filter_tahun').val())
		$('#cost_centre').val($('#filter_cost_centre').val())
		$('#tab').val(activeTable)
		$('#judul').val(judul)
});


$(document).on('click','.btn-export',function(){
	var currentdate = new Date(); 
	var datetime = currentdate.getDate() + "/"
	                + (currentdate.getMonth()+1)  + "/" 
	                + currentdate.getFullYear() + " @ "  
	                + currentdate.getHours() + ":"  
	                + currentdate.getMinutes() + ":" 
	                + currentdate.getSeconds();
	
	$('.bg-grey-2').each(function(){
		$(this).attr('bgcolor','#f4f4f4');
	});
	$('.bg-grey-2').each(function(){
		$(this).attr('bgcolor','#dddddd');
	});
	$('.bg-grey-2-1').each(function(){
		$(this).attr('bgcolor','#b4b4b4');
	});
	$('.bg-grey-2-2').each(function(){
		$(this).attr('bgcolor','#aaaaaa');
	});
	$('.bg-grey-2').each(function(){
		$(this).attr('bgcolor','#888888');
	});
	var table	= '<table>';
	table += '<tr><td colspan="1">PT Otsuka Indonesia</td></tr>';
	table += '<tr><td colspan="1"> Budget ALL Department </td><td colspan="25">: '+$('#filter_tahun option:selected').text()+'</td></tr>';
	table += '<tr><td colspan="1"> Cost centre </td><td colspan="25">: '+$('#filter_cost_centre option:selected').text()+'</td></tr>';
	table += '<tr><td colspan="1"> Print date </td><td colspan="25">: '+datetime+'</td></tr>';
	table += '</table><br />';
	table += '<table border="1">';
	table += '</table>';

	table += $(activeTable).html();
	
	var target = table;
	window.open('data:application/vnd.ms-excel,' + encodeURIComponent(target));
	$('.bg-grey-1,.bg-grey-2.bg-grey-2-1,.bg-grey-2-2,.bg-grey-3').each(function(){
		$(this).removeAttr('bgcolor');
	});
});

</script>