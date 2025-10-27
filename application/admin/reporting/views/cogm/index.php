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

			<select class="select2 infinity custom-select" style="width: 150px;" id="filter_type">
				<option value="total_after">COGM After Bari</option>
				<option value="total_before">COGM Before Bari</option>
			</select>
    		<?php 
			echo '<button class="btn btn-danger btn-proses" href="javascript:;" ><i class="fa-process"></i> Save as unitcogs</button>';
			$arr = [];
				$arr = [
					// ['btn-save','Save Data','fa-save'],
					['btn-export','Export Data','fa-upload'],
                    ($access['access_input'] ? ['btn-act-import','Import Data','fa-download']:''),
					['btn-template','Template Import','fa-reg-file-alt']
				];
			
			
			echo access_button('',$arr); 
			?>
    		</div>
			<div class="clearfix"></div>
			
		</div>
	</div>

<div class="content-body table-freeze-fullwidth">

		<div class="table-responsive tab-pane fade active show" id="result">
	    				<?php
						table_open('table table-bordered table-app table-hover table-1');
							thead();

								tr();
									th(lang('product'),'','colspan="2" class="text-center align-middle headcol"');
									th('Raw Material','','colspan="5" class="text-center align-middle headcol" ');
									th('Variable Overhead / Unit','','colspan ="4" class="text-center align-middle headcol" ');
									th('Fixed Overhead / Unit','','colspan ="6" class="text-center align-middle headcol" ');
									th('&nbsp;','','colspan="4" class="text-center align-middle headcol" ');

								tr();
									th(lang('description'),'','rowspan="" class="text-center align-middle headcol" style="min-width:200px"');
									th(lang('code'),'','rowspan="" class="text-center align-middle headcol" ');
									foreach($material as $m) {
										th($m->material,'','rowspan="" class="text-center align-middle headcol" ');
									}
									th('total material','','rowspan="" class="text-center align-middle headcol" ');
									foreach($variable as $v) {
										th($v->account_name,'','rowspan="" class="text-center align-middle headcol" ');
									}
									th('total variable','','rowspan="" class="text-center align-middle headcol" ');
									foreach($fixed as $f) {
										th($f->account_name,'','rowspan="" class="text-center align-middle headcol" ');
									}
									th('total fixed','','rowspan="" class="text-center align-middle headcol" ');
									th('Overhead / Unit','','class="text-center align-middle headcol" ');
									th('Total Cost','','class="text-center align-middle headcol" ');
									th('Volume Produksi','','class="text-center align-middle headcol" ');
									th('Total COGM','','class="text-center align-middle headcol"');



							tbody();
						table_close();
						?>
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

$('#filter_cost_centre').change(function(){
	getData();
});

$('#filter_type').change(function(){
	getData();
});

function getData() {

		cLoader.open(lang.memuat_data + '...');
		$('.overlay-wrap').removeClass('hidden');
		var page = base_url + 'reporting/cogm/data';
			page 	+= '/'+$('#filter_tahun').val();
			page 	+= '/'+$('#filter_cost_centre').val() + '?type='+$('#filter_type').val();

		$.ajax({
			url 	: page,
			data 	: {},
			type	: 'get',
			dataType: 'json',
			success	: function(response) {
				$('.table-1 tbody').html(response.table);
				cLoader.close();
				$('.overlay-wrap').addClass('hidden');	

                // calculate();
				money_init();
				
				// Ensure sticky header is properly positioned after data load
				setTimeout(function(){
					adjustStickyHeader();
				}, 100);
			}
		});
}


// Function to adjust sticky header position when using page scroll
function adjustStickyHeader() {
    // Calculate the offset from page top elements
    var contentHeaderHeight = $('.content-header').outerHeight() || 0;
    var navbarHeight = $('.header-navbar').outerHeight() || 0;
    
    // Adjust the top position for sticky header
    var stickyTop = 0; // Since we're using page scroll, keep it at top of viewport
    
    $('.table-1 thead th').css({
        'top': stickyTop + 'px'
    });
}

$(function(){
    getData();
    
    // Adjust sticky header position
    setTimeout(function(){
        adjustStickyHeader();
    }, 300);
    
    // Re-adjust on window resize
    $(window).resize(function(){
        adjustStickyHeader();
    });
    
    // Monitor scroll to ensure header stays visible
    $(window).scroll(function(){
        // Header will automatically stick due to CSS position: sticky
        // No additional JS needed for basic sticky behavior
    });
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
	table += '<tr><td colspan="1"> Cost Overhead Per Unit </td><td colspan="25">: '+$('#filter_tahun option:selected').text()+'</td></tr>';
	table += '<tr><td colspan="1"> Type </td><td colspan="25">: '+$('#filter_type option:selected').text()+'</td></tr>';
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
$(document).on('click','.btn-proses',function(e){
	e.preventDefault();
	id_proses = 'proses';
	tahun = $('#filter_tahun').val();
	cConfirm.open(lang.apakah_anda_yakin + '?','lanjut');
});

function lanjut() {
	$.ajax({
		url : base_url + 'reporting/cogm/save_unitcogs/',
		data : {id:id_proses,tahun : tahun},
		type : 'post',
		dataType : 'json',
		success : function(res) {
			cAlert.open(res.message,res.status);
		}
	});
}

</script>