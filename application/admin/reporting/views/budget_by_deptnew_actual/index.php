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
		<div class="card-header"><b>ACTUAL VS BUDGET <?php echo user('tahun_budget')-1; ?></b></div>
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
							th(lang('total_actual') . ' ' . (user('tahun_budget')-1),'','class="text-center align-middle headcol"style="min-width:60px"');
							th(lang('total_budget') . ' ' . (user('tahun_budget')-1),'','class="text-center align-middle headcol"style="min-width:60px"');
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

let activeTable = '#result';
let judul = 'Actual and Estimate';

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
				// updateSummaryTotals();
				cLoader.close();

			// $('.overlay-wrap').addClass('hidden');	
			}
		});
}


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