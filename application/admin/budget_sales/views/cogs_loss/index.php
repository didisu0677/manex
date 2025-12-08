<style>
/* Container utama untuk freeze header */
#result, #result2, #result3 {
	position: relative !important;
	overflow: auto !important;
	height: 70vh !important;
	width: 100% !important;
}

/* Header sticky untuk semua table */
#result .table thead th,
#result2 .table thead th,
#result3 .table thead th {
	position: sticky !important;
	top: 0px !important;
	z-index: 1000 !important;
	background-color: #495057 !important;
	color: #ffffff !important;
}

/* Frozen column untuk semua table */
#result .headcol,
#result2 .headcol,
#result3 .headcol {
	position: sticky !important;
	left: 0px !important;
	z-index: 999 !important;
	background-color: #495057 !important;
	color: #ffffff !important;
}

/* Kombinasi header + frozen column */
#result .table thead th.headcol,
#result2 .table thead th.headcol,
#result3 .table thead th.headcol {
	z-index: 1001 !important;
	position: sticky !important;
	top: 0px !important;
	left: 0px !important;
	background-color: #495057 !important;
	color: #ffffff !important;
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
			if(in_array(user('id_group'),[GROUP_SALES_MARKETING,ADMIN_UTAMA])) {
				echo '<button class="btn btn-success btn-save" href="javascript:;" ><i class="fa-save"></i> Save</button>';
				$arr = [];
				$arr = [
					// ['btn-save','Save Data','fa-save'],
					['btn-export','Export Data','fa-upload'],
					['btn-act-import','Import Data','fa-download'],
				];
			}else{
				$arr = [];
				$arr = [
					['btn-export','Export Data','fa-upload'],
				];
			}
		
			echo access_button('',$arr); 
			?>
		</div>
		<div class="clearfix"></div>

	</div>
</div>

<div class="content-body mt-6">
	
	<div class="main-container mt-6">

		<div class="card-header pl-3 pr-3">
			<ul class="nav nav-pills card-header-pills">
				<li class="nav-item">
					<a class="nav-link active" href="#overall" data-toggle="pill" role="tab" aria-controls="pills-overall" aria-selected="true">Actual & Estimate</a>				</li>
				<li class="nav-item">
					<a class="nav-link" href="#budget" data-toggle="pill" role="tab" aria-controls="pills-budget" aria-selected="true">Monthly Budget</a>
				</li>

				<li class="nav-item">
					<a class="nav-link" href="#detail" data-toggle="pill" role="tab" aria-controls="pills-detail" aria-selected="true">Yearly</a>
				</li>
			</ul>
		</div>
		
		<div class="card-body tab-content">
		<div class="tab-pane fade active show" id="overall">
				<div class="table-responsive" id="result">
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

			<div class="tab-pane fade" id="budget">
				<div class="table-responsive" id="result2">
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
			</div>			<div class="tab-pane fade" id="detail">
				<div class="table-responsive" id="result3">
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
	form_open(base_url('budget_sales/cogs_loss/import'),'post','form-import');
		col_init(3,9);
		input('text',lang('tahun'),'tahun','','','readonly');
		input('text',lang('divisi'),'divisi','','','readonly');
		input('text',lang('sector'),'sector','','','readonly');
		input('text',lang('tab'),'tab','','','readonly');
		input('text',lang('import_data'),'judul','','','readonly');
		
		fileupload('File Excel','fileimport','required','data-accept="xls|xlsx"');

        form_button(lang('impor'),lang('batal'));
		// echo '<br><button onclick="window.open(\''.base_url('budget_sales/price_list/template').'\', \'_blank\')" type="button" class="btn btn-success btn-block" id="btn-download-template">Download Template Import</button>';
		// echo '<br><button onclick="download_template()" type="button" class="btn btn-success btn-block" id="btn-download-template">Download Template Import</button>';

		// echo '<button class="btn btn-primary btn-block">Import</button>';
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
                url : base_url + 'budget_sales/cogs_loss/get_subaccount',
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
			url: base_url + 'budget_sales/cogs_loss/get_sector',
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
        var page = base_url + 'budget_sales/cogs_loss/data';
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
			url: base_url + 'budget_sales/cogs_loss/save_perubahan',
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
		table += '<tr><td colspan="1">' + judul + ' COGS INVENTORY LOSS </td></tr>';
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
		window.open(base_url + 'budget_sales/cogs_loss/template?tahun='+tahun)
	}

	$('.btn-act-import').click(function(){
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
			url: base_url + 'budget_sales/cogs_loss/import',
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
                window.location.href = '<?php echo site_url('budget_sales/cogs_loss') ?>';
            }, 1000);
        })
    }

</script>