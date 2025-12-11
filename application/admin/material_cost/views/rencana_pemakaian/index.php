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

			<label class=""><?php echo lang('supplier'); ?> &nbsp</label>
			<select class="select2 custom-select" style="width: 280px;" id="filter_supplier">
				<option value="ALL">ALL</option>
				<?php foreach ($supplier as $p) { ?>
					<option value="<?php echo $p->code; ?>"><?php echo $p->code . ' | ' . $p->nama; ?></option>
				<?php } ?>
			</select>

			<?php

			if($access['access_input']==1)
			// echo '<button class="btn btn-success btn-save" href="javascript:;" ><i class="fa-save"></i> Save</button>';
			// echo '<button class="btn btn-warning btn-export" href="javascript:;" >Export</button>';
			// echo '<button class="btn btn-primary btn-import" id="btn-import">Import</button>';
			$arr = [];
			$arr = [
				// ['btn-save','Save Data','fa-save'],
				['btn-export','Export Data','fa-upload'],
				// ($access['access_input'] ? ['btn-act-import','Import Data','fa-download'] :''),
				// ['btn-template','Template Import','fa-reg-file-alt']
			];
			echo access_button('',$arr); 
			?>
		</div>
		<div class="clearfix"></div>

	</div>
</div>

<div class="content-body mt-6">
	
	<div class="main-container mt-6">

		<div class="card">
			<div class="card-body">
				<div class="table-responsive tab-pane fade active show height-window" id="result">
					<?php
					table_open('table table-bordered table-app table-hover table-1');
					thead();
					tr();
					th('Material_name', '', 'class="text-center align-middle headcol"');
					th('Code', '', 'class="text-center align-middle headcol"');
					// for ($i = setting('actual_budget'); $i <= 12; $i++) {
					for ($i = 1; $i <= 12; $i++) {
						th(month_lang($i), '', 'class="text-center" style="min-width:60px"');
					}

					th('Total', '', 'class="text-center align-middle headcol"style="min-width:60px"');
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
	form_open(base_url('material_cost/rencana_pemakaian/import'),'post','form-import');
		col_init(3,9);
		input('text',lang('tahun'),'tahun','','','readonly');
		input('hidden',lang('tab'),'tab','','','readonly');
		input('hidden',lang('import_data'),'judul','','','readonly');
		
		fileupload('File Excel','fileimport','required','data-accept="xls|xlsx"');
        form_button(lang('impor'),lang('batal'));
		// echo '<br><button onclick="window.open(\''.base_url('material_cost/price_list/template').'\', \'_blank\')" type="button" class="btn btn-success btn-block" id="btn-download-template">Download Template Import</button>';
		// echo '<br><button onclick="download_template()" type="button" class="btn btn-success btn-block" id="btn-download-template">Download Template Import</button>';

		// echo '<button class="btn btn-primary btn-block">Import</button>';
	form_close();
modal_close();
?>

<script type="text/javascript">
	$(document).ready(function() {
		getData();
		$(document).on('keyup', '.budget', function(e) {
			// calculate();
			// calculateTotal();
			// console.log('y');
		});

	});

	$('#filter_tahun').change(function() {
		getData()
	});

	$('#filter_supplier').change(function() {
		getData()
	});


    function getData() {

        cLoader.open(lang.memuat_data + '...');
        $('.overlay-wrap').removeClass('hidden');
        
        // Tampilkan pesan loading yang informatif
        $('.table-1 tbody').html('<tr><td colspan="16" class="text-center"><i class="fa fa-spinner fa-spin"></i> Loading data rencana pemakaian...</td></tr>');
        
        var page = base_url + 'material_cost/rencana_pemakaian/data';
            page 	+= '/'+$('#filter_tahun').val();
			page 	+= '/'+$('#filter_supplier').val();

        $.ajax({
            url 	: page,
            data 	: {},
            type	: 'get',
            dataType: 'json',
            timeout: 120000, // 2 menit timeout
            success	: function(response) {
                $('.table-1 tbody').html(response.table);
                cLoader.close();
                $('.overlay-wrap').addClass('hidden');	
            },
            error: function(xhr, status, error) {
                cLoader.close();
                $('.overlay-wrap').addClass('hidden');
                if(status === 'timeout') {
                    $('.table-1 tbody').html('<tr><td colspan="16" class="text-center text-danger">Loading timeout. Please try with more specific filter.</td></tr>');
                } else {
                    $('.table-1 tbody').html('<tr><td colspan="16" class="text-center text-danger">Error loading data: ' + error + '</td></tr>');
                }
            }
        });
    }

	// Removed edit-related JavaScript functions since table is now read-only

	// Calculate function simplified for read-only display

	// Removed calculate, save, and edit functions since table is now read-only

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
		table += '<tr><td colspan="1">Rencana Pemakaian </td></tr>';
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

	function download_template(){
		let tahun = $('#tahun').val();
		window.open(base_url + 'material_cost/rencana_pemakaian/template?tahun='+tahun)
	}

	$('.btn-act-import').click(function(){
		$("#modal-import").modal()
		$('#form-import')[0].reset();
		$('#tahun').val($('#filter_tahun').val())
		$('#divisi').val($('#filter_divisi').val())
		$('#tab').val(activeTable)
		$('#judul').val(judul)
	});

	// function do_import(){
	// 	$.ajax({
	// 		url: base_url + 'material_cost/rencana_pemakaian/import',
	// 		data: {
	// 			tahun: $('#tahun').val(),
	// 			fileimport: $('#fileimport').val(),
	// 		},
	// 		type: 'post',
	// 		dataType: 'json',
	// 		success: function(response) {
	// 			if (response.status == 'success') {            
    //                 cAlert.open(response.message, response.status, refresh_page());
    //             } else {
    //                 cAlert.open(response.message, response.status);
    //             }
	// 			// console.log(response);
	// 		}
	// 	});
	// }

	function refresh_page() {
        $(document).on('click', '.swal-button--confirm', function(){
            setTimeout(function () {
                window.location.href = '<?php echo site_url('material_cost/rencana_pemakaian') ?>';
            }, 1000);
        })
    }

</script>