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

			<label class=""><?php echo lang('users'); ?> &nbsp</label>
			<select class="select2 custom-select" style="width: 280px;" id="filter_user">
				<option value="ALL">ALL</option>
				<?php foreach ($user as $p) { ?>
					<option value="<?php echo $p['id']; ?>"><?php echo $p['nama']; ?></option>
				<?php } ?>
			</select>

			<?php

			if($access['access_input']==1)
			echo '<button class="btn btn-success btn-save" href="javascript:;" ><i class="fa-save"></i> Save</button>';

			$arr = [];
			$arr = [
				// ['btn-save','Save Data','fa-save'],
				['btn-export','Export Data','fa-upload'],
				($submit == 0 ? ['btn-submit','Submit Budget','fa-submit'] :''),
				($access['access_input'] ? ['btn-act-import','Import Data','fa-download'] :''),
				// ['btn-template','Template Import','fa-reg-file-alt']
			];
			echo access_button('',$arr); 
			?>
		</div>
		<div class="clearfix"></div>

	</div>
</div>

<style>
/* Freeze Header Table for Material Price Report */
.table-responsive {
    position: relative;
    overflow: auto;
    max-height: calc(100vh - 130px);
    height: calc(100vh - 130px);
}

.table-1 {
    border-collapse: separate !important;
    border-spacing: 0 !important;
}

/* Header freeze */
.table-1 thead th {
    position: sticky !important;
    top: 0px !important;
    z-index: 999 !important;
    background-color: #4a5569 !important;
    color: white !important;
    border-bottom: 2px solid #333 !important;
    padding: 6px 8px !important;
    height: 36px !important;
    vertical-align: middle !important;
    box-shadow: 0 2px 4px rgba(0,0,0,0.1) !important;
}

/* Subtotal and total row styling */
.bg-grey-2 {
    background-color: #D2691E !important;
}

.bg-grey-3 {
    background-color: #778899 !important;
}

/* Preserve form controls and editable elements */
.table-1 .form-control,
.table-1 .edit-value,
.table-1 input,
.table-1 select {
    background-color: white !important;
    position: relative !important;
    z-index: 20 !important;
}

/* Additional styling for better visibility */
.table-1 {
    font-size: 12px !important;
}

.table-1 th,
.table-1 td {
    white-space: nowrap !important;
    padding: 4px 6px !important;
    vertical-align: middle !important;
}

/* Hover effects */
.table-1 tbody tr:hover {
    background-color: rgba(0,123,255,0.1) !important;
}

/* Protect all buttons and interactive elements */
.btn,
.btn-save,
.btn-export,
.btn-submit,
.btn-act-import,
button {
    position: relative !important;
    z-index: 10000 !important;
    pointer-events: auto !important;
}

/* Modal content protection */
.modal,
.modal-dialog,
.modal-content {
    z-index: 99999 !important;
}

.modal .btn,
.modal button,
.modal input,
.modal select,
.modal textarea,
.modal label {
    position: relative !important;
    z-index: 100000 !important;
    pointer-events: auto !important;
}

/* Header buttons protection */
.content-header .btn,
.content-header button,
.float-right .btn,
.float-right button {
    position: relative !important;
    z-index: 10001 !important;
    pointer-events: auto !important;
}

/* Filter elements protection */
.custom-select,
.select2,
#filter_tahun,
#filter_user {
    position: relative !important;
    z-index: 1000 !important;
    pointer-events: auto !important;
}

/* Modal and dropdown z-index */
.modal,
.modal-backdrop,
.swal2-container {
    z-index: 9999 !important;
}

.select2-container,
.select2-dropdown {
    z-index: 9998 !important;
}

/* General interactive elements */
input, select, button, a, textarea {
    pointer-events: auto !important;
}
</style>

<div class="content-body mt-6">
	
	<div class="main-container mt-6">

		<div class="card">
			<div class="card-body">
				<div class="table-responsive tab-pane fade active show height-window" id="result">
					<?php
					table_open('table table-bordered table-app table-hover table-1');
					thead();
					tr();
					th('Year', '', 'class="text-center align-middle headcol"');
					th('MatCode', '', 'class="text-center align-middle headcol"');
					th('nama', '', 'class="text-center align-middle headcol"');
					th('Vcod', '', 'class="text-center align-middle headcol"');
					th('Loc', '', 'class="text-center align-middle headcol"');
					th('BM', '', 'class="text-center align-middle headcol"');
					th('CN', '', 'class="text-center align-middle headcol"');
					th('Kurs', '', 'class="text-center align-middle headcol"');
					th('price us', '', 'class="text-center align-middle headcol"');
					th('total price', '', 'class="text-center align-middle headcol"');
					th('Bm amt', '', 'class="text-center align-middle headcol"');
					th('pph', '', 'class="text-center align-middle headcol"');
					th('ppn', '', 'class="text-center align-middle headcol"');
					th('bank charge', '', 'class="text-center align-middle headcol"');
					th('handling charge', '', 'class="text-center align-middle headcol"');
					th('price for budget', '', 'class="text-center align-middle headcol"');
					th('fpo', '', 'class="text-center align-middle headcol"');
					th('upd', '', 'class="text-center align-middle headcol"');

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
	form_open(base_url('material_cost/material_price_report/import'),'post','form-import');
		col_init(3,9);
		input('text',lang('tahun'),'tahun','','','readonly');
		input('hidden',lang('tab'),'tab','','','readonly');
		input('hidden',lang('import_data'),'judul','','','readonly');
		
		fileupload('File Excel','fileimport','required','data-accept="xls|xlsx"');
        form_button(lang('impor'),lang('batal'));
		// echo '<br><button onclick="window.open(\''.base_url('transaction/price_list/template').'\', \'_blank\')" type="button" class="btn btn-success btn-block" id="btn-download-template">Download Template Import</button>';
		// echo '<br><button onclick="download_template()" type="button" class="btn btn-success btn-block" id="btn-download-template">Download Template Import</button>';

		// echo '<button class="btn btn-primary btn-block">Import</button>';
	form_close();
modal_close();
?>

<script type="text/javascript">
	$(document).ready(function() {
		getData();
	});

	$('#filter_tahun').change(function() {
		getData()
	});

	$('#filter_user').change(function() {
		getData()
	});

    function getData() {

        cLoader.open(lang.memuat_data + '...');
        $('.overlay-wrap').removeClass('hidden');
        var page = base_url + 'material_cost/material_price_report/data';
            page 	+= '/'+$('#filter_tahun').val();
			page 	+= '/'+$('#filter_user').val();

        $.ajax({
            url 	: page,
            data 	: {},
            type	: 'get',
            dataType: 'json',
            success	: function(response) {
                $('.table-1 tbody').html(response.table);
                cLoader.close();
                $('.overlay-wrap').addClass('hidden');	
            }
        });
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
		table += '<tr><td colspan="1">Material Price Report </td></tr>';
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

	var id_proses = '';
	var tahun = 0;
	$(document).on('click', '.btn-submit', function(e) {
		e.preventDefault();
		id_proses = 'proses';
		tahun = $('#filter_tahun').val();
		cConfirm.open(lang.apakah_anda_yakin + '?', 'lanjut');
	});

	function lanjut() {
		let result = $.ajax({
			url: base_url + 'material_cost/material_price_report/submit_report',
			data: {
				id: id_proses,
				tahun: tahun,
			},
			type: 'post',
			dataType: 'json',
			success: function(res) {
				cAlert.open(res.message, res.status, 'refreshData');
			}
		});
	}

</script>