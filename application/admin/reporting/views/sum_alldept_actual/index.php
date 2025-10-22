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
			<label for="periode"><?php echo lang('bulan'); ?></label>
			<select class="select2 infinity custom-select" style = "width : 100px" name="bulan" id="bulan">
				<?php for($i = 1; $i <= 12; $i++) { $j = sprintf('%02d',$i); ?>
				<option value="<?php echo $j; ?>"<?php if($j == setting('actual_budget')) echo ' selected'; ?>><?php echo bulan($j); ?></option>
				<?php } ?>
			</select>

    		<?php 

			$arr = [];
			$arr = [
				// ['btn-save','Save Data','fa-save'],
				['btn-export','Export Data','fa-upload'],
				['btn-act-import','Import Data','fa-download'],
				// ['btn-act-template','Template Import','fa-reg-file-alt']
			];
		
		
			echo access_button('',$arr); 

			?>
    	</div>
		<div class="clearfix"></div>
	</div>

<div class="content-body mt-6">
	<div class="main-container mt-6">
		<div class="card-body tab-content">
			<div class="card">
				<div class="card-body">
					<div class="table-responsive tab-pane fade active show height-window" id="result2">
						<?php
						table_open('table table-bordered table-app table-hover table-2');
							thead();
								tr();
								th(lang('account'),'','class="text-center align-middle headcol" style="min-width:250px; color:#fff !important;"');
								foreach($production as $p) { 
									th($p->abbreviation,'','class="text-center" style="min-width:60px; color:#fff !important;"');		
								}
								th(lang('total'),'','class="text-center align-middle headcol" style="min-width:60px; color:#fff !important;"');
								th(lang('total_le'),'','class="text-center align-middle headcol" style="min-width:60px; color:#fff !important;"');
								th(lang('increase'),'','class="text-center align-middle headcol" style="min-width:40px; color:#fff !important;"');
							tbody();
						table_close();
						?>
					</div>
				</div>
			</div>		
		</div>


		
		<!-- <div class="overlay-wrap hidden">
			<div class="overlay-shadow"></div>
			<div class="overlay-content">
				<div class="spinner"></div>
				<p class="text-center">Please wait ... </p>
			</div>
		</div> -->
	</div>
</div>

<style>
/* Freeze header CSS - sama seperti production planning */
.headcol {
    position: sticky !important;
    position: -webkit-sticky !important;
    left: 0 !important;
    z-index: 10 !important;
    background-color: #f8f9fa !important;
    border-right: 2px solid #dee2e6 !important;
}

/* Maksimalkan container table height seperti production planning */
.height-window {
    height: calc(100vh - 140px) !important;
    max-height: calc(100vh - 140px) !important;
    overflow: auto !important;
    position: relative !important;
    width: 100% !important;
}

/* Maksimalkan table width dan optimasi layout */
.table-2 {
    border-collapse: collapse !important;
    width: 100% !important;
    min-width: 100% !important;
    table-layout: auto !important;
}

.table-2 th {
    position: sticky !important;
    top: 0 !important;
    z-index: 5 !important;
    background-color: #4a5569 !important;
    color: #fff !important;
    border: 1px solid #fff !important;
    font-weight: bold !important;
}

.table-2 th.headcol {
    z-index: 15 !important;
    background-color: #4a5569 !important;
    color: #fff !important;
    font-weight: bold !important;
}

/* Memastikan cell table tidak terpotong */
.table-2 th,
.table-2 td {
    white-space: nowrap !important;
    min-width: 60px !important;
}

/* Style untuk scrollbar agar lebih mudah dilihat dan digunakan */
.height-window::-webkit-scrollbar {
    width: 8px !important;
    height: 8px !important;
}

.height-window::-webkit-scrollbar-track {
    background: #f1f1f1 !important;
}

.height-window::-webkit-scrollbar-thumb {
    background: #888 !important;
}

.height-window::-webkit-scrollbar-thumb:hover {
    background: #555 !important;
}








</style>

<?php
modal_open('modal-import',lang('impor'));
	modal_body();
		form_open(base_url('reporting/sum_alldept_actual/import'),'post','form-import');
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

    $('#filter_cost_centre').trigger('change')
	$(document).on('keyup', '.budget', function (e) {
    	calculate();
    });
    
	// Inisialisasi freeze table setelah data loaded
	setTimeout(function() {
		freeze_table();
		fix_total_background();
		// Double check dengan delay
		setTimeout(function() {
			fix_total_background();
		}, 500);
	}, 1000);});

// Fungsi freeze table - sama seperti production planning
function freeze_table() {
	// Pastikan semua header kolom pertama dan terakhir memiliki class headcol
	$('.table-2 th:first-child, .table-2 td:first-child').addClass('headcol');
	$('.table-2 th:nth-last-child(-n+3), .table-2 td:nth-last-child(-n+3)').addClass('headcol');
	
	// Set z-index untuk header yang benar
	$('.table-2 th.headcol').css({
		'position': 'sticky',
		'left': '0',
		'z-index': '15',
		'background-color': '#4a5569',
		'color': '#fff'
	});
	
	// Set z-index untuk body cells
	$('.table-2 td.headcol').css({
		'position': 'sticky',
		'left': '0',
		'z-index': '10',
		'background-color': '#f8f9fa',
		'border-right': '2px solid #dee2e6',
		'font-weight': 'bold'
	});
	
	// Fix background untuk baris total - pastikan kolom freeze mengikuti background row
	fix_total_background();
}

// Fungsi untuk memastikan background total rows konsisten - manual HTML saja
function fix_total_background() {
	// Cari semua baris dengan class bg-grey
	$('.table-2 tbody tr[class*="bg-grey"]').each(function() {
		var row = $(this);
		var bgColor = '';
		
		// Tentukan warna background berdasarkan class
		if (row.hasClass('bg-grey-1')) {
			bgColor = '#f4f4f4';
		} else if (row.hasClass('bg-grey-2')) {
			bgColor = '#dddddd';
		} else if (row.hasClass('bg-grey-2-1')) {
			bgColor = '#b4b4b4';
		} else if (row.hasClass('bg-grey-2-2')) {
			bgColor = '#aaaaaa';
		} else if (row.hasClass('bg-grey-3')) {
			bgColor = '#888888';
		}
		
		// Manual setting HTML inline style untuk background full row
		if (bgColor) {
			// Set background untuk row itu sendiri
			row.attr('style', 'background-color: ' + bgColor + ' !important;');
			
			// Set background untuk SEMUA td dalam row ini - manual HTML
			row.find('td').each(function() {
				var currentStyle = $(this).attr('style') || '';
				var newStyle = 'background-color: ' + bgColor + ' !important;';
				
				// Jika td memiliki class headcol, pertahankan properties freeze tapi override background
				if ($(this).hasClass('headcol')) {
					// Pertahankan properties freeze, tapi paksa background
					var existingStyle = currentStyle.replace(/background-color:[^;]*;?/gi, '');
					newStyle = 'background-color: ' + bgColor + ' !important; ' + existingStyle;
					if (!existingStyle.includes('position: sticky')) {
						newStyle += ' position: sticky !important; left: 0 !important; z-index: 10 !important; border-right: 2px solid #dee2e6 !important; font-weight: bold !important;';
					}
				}
				
				// Set style manual ke HTML
				$(this).attr('style', newStyle);
			});
		}
	});
}	

$('#filter_tahun').change(function(){
	getData();
});

$('#bulan').change(function(){
	getData();
});


function getData() {

		cLoader.open(lang.memuat_data + '...');
		// $('.overlay-wrap').removeClass('hidden');
		var page = base_url + 'reporting/sum_alldept_actual/data';
			page 	+= '/'+$('#filter_tahun').val();
			page 	+= '/'+$('#bulan').val();
			// page    += '/'+$('#filter_allocated').val();

		$.ajax({
			url 	: page,
			data 	: {},
			type	: 'get',
			dataType: 'json',
			success	: function(response) {
				$('.table-1 tbody').html(response.table);
				$('.table-2 tbody').html(response.table2);
				
				// Apply freeze header setelah data loaded
				setTimeout(function() {
					freeze_table();
					// Fix background total setelah freeze dengan delay tambahan
					setTimeout(function() {
						fix_total_background();
						// Delay ekstra untuk memastikan
						setTimeout(function() {
							fix_total_background();
						}, 100);
					}, 100);
				}, 200);
				
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
	
			let B_01 = moneyToNumber($(this).find('.B_01').text().replace(/\,/g,''))
			let B_02 = moneyToNumber($(this).find('.B_02').text().replace(/\,/g,''))
			let B_03 = moneyToNumber($(this).find('.B_03').text().replace(/\,/g,''))
			let B_04 = moneyToNumber($(this).find('.B_04').text().replace(/\,/g,''))
			let B_05 = moneyToNumber($(this).find('.B_05').text().replace(/\,/g,''))
			let B_06 = moneyToNumber($(this).find('.B_06').text().replace(/\,/g,''))
			let B_07 = moneyToNumber($(this).find('.B_07').text().replace(/\,/g,''))
			let B_08 = moneyToNumber($(this).find('.B_08').text().replace(/\,/g,''))
			let B_09 = moneyToNumber($(this).find('.B_09').text().replace(/\,/g,''))
			let B_10 = moneyToNumber($(this).find('.B_10').text().replace(/\,/g,''))
			let B_11 = moneyToNumber($(this).find('.B_11').text().replace(/\,/g,''))
			let B_12 = moneyToNumber($(this).find('.B_12').text().replace(/\,/g,''))

	
			let total_budget = 0
	
			total_budget = B_01+B_02+B_03+B_04+B_05+B_06+B_07+B_08+B_09+B_10+B_11+B_12
			

			$(this).find('.total_budget').text(customFormat(total_budget))
		}
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
		url : base_url + 'reporting/sum_alldept_actual/save_perubahan',
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
	table += '<tr><td colspan="1"> Summary Budget ALL Department </td><td colspan="25">: '+$('#filter_tahun option:selected').text()+'</td></tr>';
	table += '<tr><td colspan="1"> Cost centre </td><td colspan="25">: '+$('#filter_cost_centre option:selected').text()+'</td></tr>';
	table += '<tr><td colspan="1"> Print date </td><td colspan="25">: '+datetime+'</td></tr>';
	table += '</table><br />';
	table += '<table border="1">';
	table += '</table>';

	table += $('#result2').html();
	
	var target = table;
	window.open('data:application/vnd.ms-excel,' + encodeURIComponent(target));
	$('.bg-grey-1,.bg-grey-2.bg-grey-2-1,.bg-grey-2-2,.bg-grey-3').each(function(){
		$(this).removeAttr('bgcolor');
	});
});

</script>