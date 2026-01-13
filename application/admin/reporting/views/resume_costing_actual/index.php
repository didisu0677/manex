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

			<select class="select2 infinity custom-select" style="width: 180px;" id="filter_jam_transfer">
				<option value="0"><?php echo lang('sebelum_jam_transfer') ; ?></option>
				<option value="1"><?php echo lang('setelah_jam_transfer') ?></option>
			</select>

			<!-- <select class="select2 infinity custom-select" style="width: 180px;" id="filter_allocated">
				<option value="0"><?php echo lang('not_allocated') ; ?></option>
				<option value="1"><?php echo lang('allocated') ?></option>
			</select> -->
    		
    		<?php 

			$arr = [];
			$arr = [
				// ['btn-save','Save Data','fa-save'],
				['btn-export','Export Data','fa-upload'],
				// ['btn-act-import','Import Data','fa-download'],
				// ['btn-act-template','Template Import','fa-reg-file-alt']
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
									th(lang('account'),'','class="text-center align-middle headcol" style="min-width:250px"');
									foreach($production as $p) { 
										th($p->abbreviation,'','class="text-center" style="min-width:60px"');		
									}
									th(lang('total'),'','class="text-center align-middle headcol"style="min-width:60px"');
							tbody();
						table_close();
						?>
	    				</div>
	    			</div>
	    		</div>
	    	</div>
	    </div>
	</div>

	<!-- <div class="main-container mt-2">
		<div class="row">

			<div class="col-sm-12">

				<div class="card">
					<div class="card-header"><b>TOTAL MANEX FOR COSTING - AFTER IDDLE COST</b></div>
	    			<div class="card-body">
	    				<div class="table-responsive tab-pane fade active show height-window" id="result2">
	    				<?php
						table_open('table table-bordered table-app table-hover table-2');
							thead();
								tr();
									th(lang('account'),'','class="text-center align-middle headcol" style="min-width:250px"');
									foreach($production as $p) { 
										th($p->abbreviation,'','class="text-center" style="min-width:60px"');		
									}
									th(lang('total'),'','class="text-center align-middle headcol"style="min-width:60px"');
							tbody();
						table_close();
						?>
	    				</div>
	    			</div>
	    		</div>
	    	</div>
	    </div>
	</div> -->

	
	<!-- <div class="overlay-wrap hidden">
		<div class="overlay-shadow"></div>
		<div class="overlay-content">
			<div class="spinner"></div>
			<p class="text-center">Please wait ... </p>
		</div>
	</div> -->
	
</div>

<script type="text/javascript">

$(document).ready(function () {
	getData();

    $('#filter_cost_centre').trigger('change')
});	


// $(function(){
// 	// getData();
// 	// $('#filter_cost_centre').trigger('change')

	
// });
$('#filter_tahun').change(function(){
	getData();
});

$('#bulan').change(function(){
	getData();
});

$('#filter_jam_transfer').change(function(){
	getData();
});

// $('#filter_allocated').change(function(){
// 	getData();
// });

function getData() {

		cLoader.open(lang.memuat_data + '...');
		// $('.overlay-wrap').removeClass('hidden');
		var page = base_url + 'reporting/resume_costing_actual/data';
			page 	+= '/'+$('#filter_tahun').val();
			page 	+= '/'+$('#bulan').val();
			page    += '/'+$('#filter_jam_transfer').val();


		$.ajax({
			url 	: page,
			data 	: {},
			type	: 'get',
			dataType: 'json',
			success	: function(response) {
				$('.table-1 tbody').html(response.table);
				$('.table-2 tbody').html(response.table2);
				cLoader.close();
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
	table += '<tr><td colspan="1"> Resume For Costing Actual</td><td colspan="25">: '+$('#filter_tahun option:selected').text()+'</td></tr>';
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

</script>