<style>
:root {
	--qty-col1-width: 260px;
	--qty-col2-width: 120px;
	--qty-col3-width: 150px;
}

/* Wrapper scroll untuk masing-masing tab */
#result,
#result2,
#result3 {
	position: relative !important;
	overflow: auto !important;
	height: calc(100vh - 150px) !important;
	width: 100% !important;
}

/* Sticky header berbasis CSS murni */
.table-1 thead th,
.table-2 thead th,
.table-3 thead th {
	position: sticky !important;
	top: 0 !important;
	z-index: 5 !important;
	background-color: #4a5569 !important;
	color: #fff !important;
	font-weight: 600 !important;
}

/* Kolom Product */
.table-1 thead th:nth-child(1),
.table-2 thead th:nth-child(1),
.table-3 thead th:nth-child(1),
.table-1 tbody td:nth-child(1),
.table-2 tbody td:nth-child(1),
.table-3 tbody td:nth-child(1) {
	position: sticky !important;
	left: 0 !important;
	z-index: 6 !important;
	min-width: var(--qty-col1-width) !important;
}

/* Kolom Code */
.table-1 thead th:nth-child(2),
.table-2 thead th:nth-child(2),
.table-3 thead th:nth-child(2),
.table-1 tbody td:nth-child(2),
.table-2 tbody td:nth-child(2),
.table-3 tbody td:nth-child(2) {
	position: sticky !important;
	left: var(--qty-col1-width) !important;
	z-index: 6 !important;
	min-width: var(--qty-col2-width) !important;
}

/* Kolom Sector */
.table-1 thead th:nth-child(3),
.table-2 thead th:nth-child(3),
.table-3 thead th:nth-child(3),
.table-1 tbody td:nth-child(3),
.table-2 tbody td:nth-child(3),
.table-3 tbody td:nth-child(3) {
	position: sticky !important;
	left: calc(var(--qty-col1-width) + var(--qty-col2-width)) !important;
	z-index: 6 !important;
	min-width: var(--qty-col3-width) !important;
}

/* Pastikan header kolom beku selalu di atas */
.table-1 thead th:nth-child(-n+3),
.table-2 thead th:nth-child(-n+3),
.table-3 thead th:nth-child(-n+3) {
	z-index: 8 !important;
}

/* Warna latar untuk kolom yang dibekukan */
.table-1 tbody td:nth-child(-n+3),
.table-2 tbody td:nth-child(-n+3),
.table-3 tbody td:nth-child(-n+3) {
	background-color: #f8f9fa !important;
	font-weight: 600 !important;
}

.table-1 th,
.table-1 td,
.table-2 th,
.table-2 td,
.table-3 th,
.table-3 td {
	white-space: nowrap !important;
	min-width: 60px !important;
}
</style>

<div class="content-header page-data" data-additional="<?= $access_additional ?>">
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

			<label class=""><?php echo lang('divisi'); ?> &nbsp</label>
			<select class="select2 infinity custom-select" style="width: 180px;" id="filter_divisi">
				<?php if(user('id_group')!=30) { ?>
				<option value="ALL">ALL</option>
				<?php }?>
				<?php foreach ($divisi as $div) { ?>
					<option value="<?php echo $div->bisunit; ?>"><?php echo $div->divisi; ?></option>
				<?php } ?>
			</select>

			<label class=""><?php echo lang('sa'); ?>  &nbsp</label>
			<select class="select2 infinity custom-select" style="width: 180px;" id="filter_sub_account" disabled>
			</select>

			<label class="">Sector &nbsp</label>
			<select class="select2 infinity custom-select" style="width: 180px;" id="filter_sector">
				<option value="ALL">ALL</option>
				<?php foreach ($sector as $sec) { ?>
					<option value="<?php echo $sec['id'] ?>"><?php echo $sec['sector']; ?></option>
				<?php } ?>
			</select>

			<?php
			$arr = [];
			$arr = [
				// ['btn-save','Save Data','fa-save'],
				['btn-export','Export Data','fa-upload'],
				// ['btn-act-import','Import Data','fa-download'],
				// ['btn-template','Template Import','fa-reg-file-alt']
			];
		
			echo access_button('',$arr); 
			?>
		</div>
		<div class="clearfix"></div>

	</div>
</div>

<div class="content-body">
	<div class="card">
		<div class="card-body pb-2">
			<ul class="nav nav-pills card-header-pills">
				<li class="nav-item">
					<a class="nav-link active" id="overall-tab" data-toggle="pill" href="#overall" role="tab" aria-controls="overall" aria-selected="true">Actual & Estimate</a>
				</li>
				<li class="nav-item">
					<a class="nav-link" id="budget-tab" data-toggle="pill" href="#budget" role="tab" aria-controls="budget" aria-selected="false">Monthly Budget</a>
				</li>
				<li class="nav-item">
					<a class="nav-link" id="detail-tab" data-toggle="pill" href="#detail" role="tab" aria-controls="detail" aria-selected="false">Yearly</a>
				</li>
			</ul>
		</div>

		<div class="card-body tab-content pt-0" id="pills-tabContent">
			<div class="tab-pane fade show active" id="overall" role="tabpanel" aria-labelledby="overall-tab">
				<div class="table-responsive height-window" id="result">
							<?php
							table_open('table table-bordered table-app table-hover table-1');
							thead();
							tr();
							th('Product', '', 'class="text-center align-middle headcol"');
							th('Code', '', 'class="text-center align-middle headcol"');
							th('Sector', '', 'class="text-center align-middle headcol"');
							// for ($i = setting('actual_budget'); $i <= 12; $i++) {
							for ($i = 1; $i <= 12; $i++) {
								$actual = "";
								if($i <= setting('actual_budget')) {
									$actual = "ACT";
								}else{
									$actual = "EST";
								}

								th($actual . ' ' . month_lang($i), '', 'class="text-center" style="min-width:60px"');
							}
							th('Total', '', 'class="text-center align-middle headcol"style="min-width:60px"');
							tbody();
							table_close();
							?>
				</div>
			</div>

			<div class="tab-pane fade" id="budget" role="tabpanel" aria-labelledby="budget-tab">
				<div class="table-responsive height-window" id="result2">
							<?php
							table_open('table table-bordered table-app table-hover table-2');
							thead();
							tr();
							th('Product', '', 'class="text-center align-middle headcol"');
							th('Code', '', 'class="text-center align-middle headcol"');
							th('Sector', '', 'class="text-center align-middle headcol"');
							for ($i = 1; $i <= 12; $i++) {
	
								th(month_lang($i), '', 'class="text-center" style="min-width:60px"');
							}
							th('Total', '', 'class="text-center align-middle headcol"style="min-width:60px"');
							tbody();
							table_close();
							?>
				</div>
			</div>

			<div class="tab-pane fade" id="detail" role="tabpanel" aria-labelledby="detail-tab">
				<div class="table-responsive height-window" id="result3">
							<?php
							table_open('table table-bordered table-app table-hover table-3');
							thead();
							tr();
							th('Product', '', 'class="text-center align-middle headcol"');
							th('Code', '', 'class="text-center align-middle headcol"');
							th('Sector', '', 'class="text-center align-middle headcol"');
							th('Actual ' . (user('tahun_budget') -1), '', 'class="text-center" style="min-width:60px"');

							for ($i = user('tahun_budget'); $i <= user('tahun_budget')+10; $i++) {
	
								th($i, '', 'class="text-center" style="min-width:60px"');
							}
							// th('Total', '', 'class="text-center align-middle headcol"style="min-width:60px"');
							tbody();
							table_close();
							?>
				</div>
			</div>
		</div>
	</div>
</div>

<?php
modal_open('modal-import',lang('impor'));
modal_body();
	form_open(base_url('budget_sales/gross_profit/import'),'post','form-import');
		col_init(3,9);
		input('text',lang('tahun'),'tahun','','','readonly');
		input('text',lang('divisi'),'divisi','','','readonly');
		input('text',lang('sector'),'sector','','','readonly');
		input('hidden',lang('tab'),'tab','','','readonly');
		input('text',lang('import_data'),'judul','','','readonly');
		
		fileupload('File Excel','fileimport','required','data-accept="xls|xlsx"');

		// echo '<br><button onclick="window.open(\''.base_url('budget_sales/gross_profit/template').'\', \'_blank\')" type="button" class="btn btn-success btn-block" id="btn-download-template">Download Template Import</button>';
		echo '<br><button onclick="download_template()" type="button" class="btn btn-success btn-block" id="btn-download-template">Download Template Import</button>';

		echo '<button onClick="do_import()" class="btn btn-primary btn-block" id="btn-import-excel">Import</button>';
	form_close();
modal_close();
?>

<script type="text/javascript">
	$(document).ready(function() {
		$('#filter_divisi').trigger('change')
		$(document).on('keyup', '.budget', function(e) {
			calculate();
			// calculateTotal();
			// console.log('y');
		});

		getData();
	});

	$('#filter_tahun').change(function() {
		getData()
	});

	$('#filter_divisi').change(function() {
		// getCategory();
		// getSector();
		$('#filter_sub_account').removeAttr('disabled');
		$('#filter_sector').removeAttr('disabled');

		if ($('#filter_divisi').val() == 'ALL') {
			$('#filter_sub_account').prop('disabled', true);
			$('#filter_sector').prop('disabled', true);
		}
        getSubaccount()
		
	});

	$('#filter_sub_account').change(function() {
		getData();
	});

	$('#filter_sector').change(function() {
		getData();
	});

    function getSubaccount() {
        console.log('ok');
        $('#filter_sub_account').html('');
        $.ajax({
                url : base_url + 'budget_sales/gross_profit/get_subaccount',
                data : {divisi : $('#filter_divisi').val()},
                type : 'post',
                dataType : 'json',
                success : function(response) {
                    var konten = '<option value="ALL">ALL</option>';
                    $.each(response,function(k,v){
                        konten += '<option value="'+v.subaccount_code+'">'+v.subaccount_desc+'</option>';
                    });
                    $('#filter_sub_account').html(konten);
					$('#filter_sub_account').trigger('change');
                }
        });
    }

	function getSector() {
		$('#filter_sector').html('');
		$.ajax({
			url: base_url + 'budget_sales/gross_profit/get_sector',
			data: {
				divisi: $('#filter_divisi').val(),
				category: $('#filter_category').val()
			},
			type: 'post',
			dataType: 'json',
			success: function(response) {
				// console.log(response);
				var konten = '';
				konten += '<option value="ALL">ALL</option>';
				$.each(response, function(k, v) {
					konten += '<option value="' + v.sector + '">' + v.sector + '</option>';
				});
				$('#filter_sector').html(konten);
				$('#filter_sector').trigger('change');
			}
		});
	}

    function getData() {

        cLoader.open(lang.memuat_data + '...');
        $('.overlay-wrap').removeClass('hidden');
        var page = base_url + 'budget_sales/gross_profit/data';
            page 	+= '/'+$('#filter_tahun').val();
            page 	+= '/'+$('#filter_divisi').val();
            page 	+= '/'+$('#filter_sub_account').val();
			page 	+= '/'+$('#filter_sector').val();

        $.ajax({
            url 	: page,
            data 	: {},
            type	: 'get',
            dataType: 'json',
            success	: function(response) {
                $('.table-1 tbody').html(response.table);
				$('.table-2 tbody').html(response.table2);
				$('.table-3 tbody').html(response.table3);
                cLoader.close();
                $('.overlay-wrap').addClass('hidden');	
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
		$('.table-2 tbody tr').each(function() {
			let totalMonthly = [];
			var grandTotal = 0;

			if ($(this).find('.budget').text() != '') {

				let B_01 = moneyToNumber($(this).find('.B_01').text().replace(/\,/g, ''))
				let B_02 = moneyToNumber($(this).find('.B_02').text().replace(/\,/g, ''))
				let B_03 = moneyToNumber($(this).find('.B_03').text().replace(/\,/g, ''))
				let B_04 = moneyToNumber($(this).find('.B_04').text().replace(/\,/g, ''))
				let B_05 = moneyToNumber($(this).find('.B_05').text().replace(/\,/g, ''))
				let B_06 = moneyToNumber($(this).find('.B_06').text().replace(/\,/g, ''))
				let B_07 = moneyToNumber($(this).find('.B_07').text().replace(/\,/g, ''))
				let B_08 = moneyToNumber($(this).find('.B_08').text().replace(/\,/g, ''))
				let B_09 = moneyToNumber($(this).find('.B_09').text().replace(/\,/g, ''))
				let B_10 = moneyToNumber($(this).find('.B_10').text().replace(/\,/g, ''))
				let B_11 = moneyToNumber($(this).find('.B_11').text().replace(/\,/g, ''))
				let B_12 = moneyToNumber($(this).find('.B_12').text().replace(/\,/g, ''))

				let total_budget = 0

				total_budget = B_01 + B_02 + B_03 + B_04 + B_05 + B_06 + B_07 + B_08 + B_09 + B_10 + B_11 + B_12

				$(this).find('.total_budget').text(customFormat(total_budget))

			}

			for (let i = 1; i <= 12; i++) {
				let total = 0;

				$('.B_' + ('0' + i).slice(-2)).each(function() {
					let value = moneyToNumber($(this).text().replace(/\,/g, ''));
					total += value;
				});

				totalMonthly.push(total);
				grandTotal += total;
			}

			for (let i = 0; i < totalMonthly.length; i++) {
				$('#totalB' + ('0' + (i + 1)).slice(-2)).text(customFormat(totalMonthly[i]));
			}

			$('#grand_total').text(customFormat(grandTotal))

		});

		$('.table-1 tbody tr').each(function(){
			if ($(this).find('.budget').text() != '') {
				let EST_01 = moneyToNumber($(this).find('.EST_01').text().replace(/\,/g, ''))
				let EST_02 = moneyToNumber($(this).find('.EST_02').text().replace(/\,/g, ''))
				let EST_03 = moneyToNumber($(this).find('.EST_03').text().replace(/\,/g, ''))
				let EST_04 = moneyToNumber($(this).find('.EST_04').text().replace(/\,/g, ''))
				let EST_05 = moneyToNumber($(this).find('.EST_05').text().replace(/\,/g, ''))
				let EST_06 = moneyToNumber($(this).find('.EST_06').text().replace(/\,/g, ''))
				let EST_07 = moneyToNumber($(this).find('.EST_07').text().replace(/\,/g, ''))
				let EST_08 = moneyToNumber($(this).find('.EST_08').text().replace(/\,/g, ''))
				let EST_09 = moneyToNumber($(this).find('.EST_09').text().replace(/\,/g, ''))
				let EST_10 = moneyToNumber($(this).find('.EST_10').text().replace(/\,/g, ''))
				let EST_11 = moneyToNumber($(this).find('.EST_11').text().replace(/\,/g, ''))
				let EST_12 = moneyToNumber($(this).find('.EST_12').text().replace(/\,/g, ''))

				let total_est = 0

				total_est = EST_01 + EST_02 + EST_03 + EST_04 + EST_05 + EST_06 + EST_07 + EST_08 + EST_09 + EST_10 + EST_11 + EST_12

				$(this).find('.total_est').text(customFormat(total_est))
			}
		});
	}

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

	function save_perubahan() {
		var data_edit = {};
		var i = 0;

		$('.edited').each(function() {
			var content = $(this).children('div');
			if (typeof data_edit[$(this).attr('data-id')] == 'undefined') {
				data_edit[$(this).attr('data-id')] = {};
			}
			data_edit[$(this).attr('data-id')][$(this).attr('data-name')] = $(this).text().replace(/[^0-9\-]/g, '');
			i++;
		});

		var jsonString = JSON.stringify(data_edit);
		// var jsonString = JSON.stringify(data_edit, null, 2); // 2 spaces indentation

		console.log(jsonString);
		$.ajax({
			url: base_url + 'budget_sales/gross_profit/save_perubahan',
			data: {
				'json': jsonString,
				'tahun': $('#filter_tahun').val(),
				verifikasi: i
			},
			type: 'post',
			success: function(response) {
				cAlert.open(response, 'success', 'refreshData');
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
		table += '<tr><td colspan="1">' + judul + ' GROSS PROFIT </td></tr>';
		table += '<tr><td colspan="1"> Divisi </td><td>: ' + $('#filter_divisi option:selected').text() + '</td></tr>';
		table += '<tr><td colspan="1"> Sector </td><td>: ' + $('#filter_sector option:selected').text() + '</td></tr>';
		table += '<tr><td colspan="1"> Group Product </td><td>: ' + $('#filter_sub_account option:selected').text() + '</td></tr>';
		table += '<tr><td colspan="1"> Print date </td><td>: ' + datetime + '</td></tr>';
		table += '</table><br><br>';

		// Add content body
		table += $(activeTable).html();

		var target = table;
		// window.open('data:application/vnd.ms-excel,' + encodeURIComponent(target));

        htmlToExcel(target)
	});

	function download_template(){
		let tahun = $('#tahun').val();
		window.open(base_url + 'budget_sales/gross_profit/template?tahun='+tahun)
	}

	$(document).on('click','#btn-import',function(){
		$("#modal-import").modal()
		$('#form-import')[0].reset();
		$('#tahun').val($('#filter_tahun').val())
		$('#divisi').val($('#filter_divisi').val())
		$('#sector').val($('#filter_sector').val())
		$('#tab').val(activeTable)
		$('#judul').val(judul)
	});



	function do_import(){
		$.ajax({
			url: base_url + 'budget_sales/gross_profit/import',
			data: {
				tahun: $('#tahun').val(),
				fileimport: $('#fileimport').val(),
			},
			type: 'post',
			dataType: 'json',
			success: function(response) {
				if (response.status == 'success') {            
                    cAlert.open(response.message, response.status, refresh_page());
                } else {
                    cAlert.open(response.message, response.status);
                }
				// console.log(response);
			}
		});
	}

	function refresh_page() {
        $(document).on('click', '.swal-button--confirm', function(){
            setTimeout(function () {
                window.location.href = '<?php echo site_url('budget_sales/gross_profit') ?>';
            }, 1000);
        })
    }

</script>