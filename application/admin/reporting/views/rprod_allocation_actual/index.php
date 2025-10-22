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

			<label class=""><?php echo lang('cc'); ?>  &nbsp</label>
			<select class="select2 infinity custom-select" style="width: 180px;" id="filter_cost_centre">
				<option value="ALL">ALL</option>
				<?php foreach ($cc as $c) { ?>
                <option value="<?php echo $c->kode; ?>"><?php echo $c->cost_centre; ?></option>
                <?php } ?>
			</select>
    		<?php 

			if($access['access_input']==1)
			echo '<button class="btn btn-danger btn-proses" href="javascript:;" ><i class="fa-process"></i> Save Report</button>';

			$arr = [];
				$arr = [
					// ['btn-save','Save Data','fa-save'],
					['btn-export','Export Data','fa-upload'],
                    ($access['access_input'] ? ['btn-act-import','Import Data','fa-download'] : ''),
					['btn-template','Template Import','fa-reg-file-alt']
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

	    			<div class="card-body">
	    				<div class="table-responsive tab-pane fade active show height-window" id="result">
	    				<?php
						table_open('table table-bordered table-app table-hover table-1');
							thead();

								tr();
									th(lang('product'),'','colspan="2" class="text-center align-middle headcol"');
									th('Variable Overhead','','colspan ="4" class="text-center align-middle headcol" ');
									th('Fixed Overhead','','colspan ="6" class="text-center align-middle headcol" ');
									th('Total Overhead','','rowspan="2" class="text-center align-middle headcol" ');
								tr();
									th(lang('description'),'','rowspan="" width="300"class="text-center align-middle headcol" ');
									th(lang('code'),'','rowspan="" class="text-center align-middle headcol" ');
									foreach($variable as $v) {
										th($v->account_name,'','rowspan="" class="text-center align-middle headcol" ');
									}
									th('total variable','','rowspan="" class="text-center align-middle headcol" ');
									foreach($fixed as $f) {
										th($f->account_name,'','rowspan="" class="text-center align-middle headcol" ');
									}
									th('total fixed','','rowspan="" class="text-center align-middle headcol" ');

							tbody();
						table_close();
						?>
	    				</div>
	    			</div>
	    		</div>
	    	</div>
	    </div>
	</div>
</div>
	
	<div class="overlay-wrap hidden">
		<div class="overlay-shadow"></div>
		<div class="overlay-content">
			<div class="spinner"></div>
			<p class="text-center">Please wait ... </p>
		</div>
	</div>
	
</div>
<?php
modal_open('modal-import',lang('impor'));
modal_body();
    form_open(base_url('transaction/allocation_qc/import'),'post','form-import');
        col_init(3,9);
        input('text',lang('tahun'),'tahun','','','readonly');
        fileupload('File Excel','fileimport','required','data-accept="xls|xlsx"');
        form_button(lang('impor'),lang('batal'));
    form_close();
modal_close();
?>
<script type="text/javascript">

$(document).ready(function () {
	getData();
});	

$('#filter_tahun').change(function(){
	getData();
});

$('#bulan').change(function(){
	getData();
});

$('#filter_cost_centre').change(function(){
	getData();
});

function getData() {

		cLoader.open(lang.memuat_data + '...');
		$('.overlay-wrap').removeClass('hidden');
		var page = base_url + 'reporting/rprod_allocation_actual/data';
			page 	+= '/'+$('#filter_tahun').val();
			page 	+= '/'+$('#bulan').val();
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

                calculate();
				money_init()
			}
		});
}


$(function(){
	getData();
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
	table += '<tr><td colspan="1"> Cost Product Overhead Allocation </td><td colspan="25">: '+$('#filter_tahun option:selected').text()+'</td></tr>';
	table += '<tr><td colspan="1"> Print date </td><td colspan="25">: '+datetime+'</td></tr>';
	table += '</table><br />';
	table += '<table border="1">';
	table += $('.content-body').html();
	table += '</table>';
	var target = table;
	window.open('data:application/vnd.ms-excel,' + encodeURIComponent(target));
	$('.bg-grey-1,.bg-grey-2.bg-grey-2-1,.bg-grey-2-2,.bg-grey-3').each(function(){
		$(this).removeAttr('bgcolor');
	});
});

var id_proses = '';
var tahun = 0;
var bulan = '';
$(document).on('click','.btn-proses',function(e){
	e.preventDefault();
	id_proses = 'proses';
	tahun = $('#filter_tahun').val();
	bulan = $('#bulan').val();
	cConfirm.open(lang.apakah_anda_yakin + '?','lanjut');
});

function lanjut() {
	$.ajax({
		url : base_url + 'reporting/rprod_allocation_actual/save_alokasi/',
		data : {id:id_proses,tahun : tahun, bulan: bulan},
		type : 'post',
		dataType : 'json',
		success : function(res) {
			cAlert.open(res.message,res.status);
		}
	});
}
</script>

<style>
/* Force white font for ALL headers */
.table-1 th, 
.table-1 th *,
.table-1 thead th,
.table-1 thead th * { 
    color: #fff !important; 
    background-color: #4a5569 !important; 
    font-weight: bold !important;
}

/* Header positioning and styling */
.table-1 th { 
    position: sticky !important; 
    top: 0 !important; 
    z-index: 5 !important; 
    height: 41px !important; /* Set consistent header height */
    padding: 6px 8px !important;
    vertical-align: middle !important;
    box-sizing: border-box !important;
}

/* Header row positioning - handle multi-row headers */
.table-1 thead tr:first-child th { 
    top: 0 !important; 
    z-index: 6 !important; 
}

.table-1 thead tr:nth-child(2) th { 
    top: 41px !important; /* Match the height of first row */
    z-index: 6 !important; 
}

/* Special handling for rowspan columns - Total Overhead */
.table-1 thead tr:first-child th[rowspan="2"] { 
    height: 82px !important; /* Double height for rowspan=2 */
    top: 0 !important;
    z-index: 7 !important;
    border-bottom: 2px solid #dee2e6 !important;
    vertical-align: middle !important;
}

/* First row - "Product" spans 2 columns, freeze it */
.table-1 thead tr:first-child th:first-child { 
    left: 0 !important; 
    z-index: 16 !important; 
    border-right: 2px solid #fff !important;
    min-width: 400px !important; /* Covers both Description + Code columns */
}

/* Second row - freeze "Description" and "Code" columns separately */
.table-1 thead tr:nth-child(2) th:first-child { 
    left: 0 !important; 
    z-index: 15 !important; 
    border-right: 2px solid #fff !important;
    min-width: 300px !important;
}

.table-1 thead tr:nth-child(2) th:nth-child(2) { 
    left: 300px !important; 
    z-index: 14 !important; 
    border-right: 2px solid #fff !important;
    min-width: 100px !important;
}

/* Body column styling - freeze first 2 columns */
.table-1 tbody td:first-child { 
    position: sticky !important; 
    left: 0 !important; 
    z-index: 10 !important; 
    background-color: #f8f9fa !important; 
    font-weight: bold !important; 
    border-right: 2px solid #dee2e6 !important;
    min-width: 300px !important;
}

.table-1 tbody td:nth-child(2) { 
    position: sticky !important; 
    left: 300px !important; 
    z-index: 9 !important; 
    background-color: #f8f9fa !important; 
    font-weight: bold !important; 
    border-right: 2px solid #dee2e6 !important;
    min-width: 100px !important;
}

/* Total columns styling - highlight last 3 columns */
.table-1 td:nth-last-child(-n+3) { 
    background-color: #f0f8ff !important; 
    font-weight: bold !important; 
    border-left: 2px solid #007bff !important; 
}

/* Table container */
.height-window { 
    height: calc(100vh - 140px) !important; 
    overflow: auto !important; 
}

.table-1 { 
    border-collapse: collapse !important; 
    width: 100% !important; 
}

.table-1 th, .table-1 td { 
    white-space: nowrap !important; 
    min-width: 80px !important; 
    padding: 6px 8px !important; 
    border: 1px solid #dee2e6 !important; 
    line-height: 1.2 !important;
}

/* Reduce border thickness for headers */
.table-1 thead th {
    border-width: 1px !important;
}

/* Subtotal rows - maintain background for frozen columns */
.table-1 tbody tr.bg-grey-3 td:first-child,
.table-1 tbody tr.bg-grey-3 td:nth-child(2) { 
    background-color: #778899 !important; 
    color: white !important;
    position: sticky !important;
    left: 0 !important;
    z-index: 10 !important;
}

.table-1 tbody tr.bg-grey-3 td:nth-child(2) {
    left: 300px !important;
    z-index: 9 !important;
}

.table-1 tbody tr.bg-grey-2 td:first-child,
.table-1 tbody tr.bg-grey-2 td:nth-child(2) { 
    background-color: #D2691E !important; 
    color: white !important;
    position: sticky !important;
    left: 0 !important;
    z-index: 10 !important;
}

.table-1 tbody tr.bg-grey-2 td:nth-child(2) {
    left: 300px !important;
    z-index: 9 !important;
}

/* Additional CSS classes for consistent styling */
.bg-grey-3 {
    background-color: #778899 !important;
}

.bg-grey-2 {
    background-color: #D2691E !important;
}

.bg-grey-3 th,
.bg-grey-3 td,
.bg-grey-2 th,
.bg-grey-2 td {
    color: #fff !important;
}
</style>