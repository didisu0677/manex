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

			<label class=""><?php echo lang('factory'); ?>  &nbsp</label>
			<select class="select2 infinity custom-select" style="width: 180px;" id="filter_cost_centre">
				<option value="ALL">ALL FACTORY</option>
				<?php foreach ($cc as $c) { ?>
                <option value="<?php echo $c->kode; ?>"><?php echo $c->cost_centre; ?></option>
                <?php } ?>
			</select>

			<?php

			if($access['access_input']==1)
			$arr = [];
			$arr = [
				['btn-export','Export Data','fa-upload'],
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
					tr(); ?>
	             	<th style="background-color: #CC0000; color: white;" width =""><font color="#fff"><?php echo lang('product_code'); ?></th>
              		<th style="background-color: #CC0000; color: white;" width =""><font color="#fff"><?php echo lang('material_code'); ?></th>
					<th style="background-color: #CC0000; color: white;" width ="" class="text-center"><font color="#fff"><?php echo lang('material_name'); ?></th>
					<th style="background-color: #CC0000; color: white;"><font color="#fff"><?php echo lang('group_formula'); ?></th>
					<th style="background-color: #CC0000; color: white;"><font color="#fff"><?php echo lang('um'); ?></th>
					<th style="background-color: #CC0000; color: white;"><font color="#fff"><?php echo lang('quantity'); ?></th>
					<th style="background-color: #CC0000; color: white;"><font color="#fff"><?php echo lang('price'); ?></th>
					<th style="background-color: #CC0000; color: white;"><font color="#fff"><?php echo lang('total'); ?></th>
					<?php
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
	form_open(base_url('material_cost/formula_cost/import'),'post','form-import');
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

	$('#filter_cost_centre').change(function() {
		getData()
	});



    function getData() {

        cLoader.open(lang.memuat_data + '...');
        $('.overlay-wrap').removeClass('hidden');
        var page = base_url + 'material_cost/formula_cost/data';
            page 	+= '/'+$('#filter_tahun').val();
			page 	+= '/'+$('#filter_cost_centre').val();

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
		table += '<tr><td colspan="1">' + judul + ' Quantity Sales </td></tr>';
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

</script>