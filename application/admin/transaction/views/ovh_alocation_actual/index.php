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
					<option value="<?php echo $tahun->tahun; ?>"<?php if($tahun->tahun == user('tahun_budget') - 1) echo ' selected'; ?>><?php echo $tahun->tahun; ?></option>
                <?php } ?>
			</select>

			<label for="periode"><?php echo lang('bulan'); ?></label>
			<select class="select2 infinity custom-select" style = "width : 100px" name="bulan" id="bulan">
				<?php for($i = 1; $i <= 12; $i++) { $j = sprintf('%02d',$i); ?>
				<option value="<?php echo $j; ?>"<?php if($j == setting('actual_budget')) echo ' selected'; ?>><?php echo bulan($j); ?></option>
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


			// echo '<button class="btn btn-success btn-save" href="javascript:;" ><i class="fa-save"></i> Save</button>';
			if($access['access_input'])
			echo '<button class="btn btn-danger btn-proses" href="javascript:;" ><i class="fa-process"></i> Rounding Persentage</button>';

			$arr = [];
				$arr = [
					// ['btn-save','Save Data','fa-save'],
					['btn-export','Export Data','fa-upload'],
					($access['access_input'] ? ['btn-act-import','Import Data','fa-download'] :''),
					// ['btn-template','Template Import','fa-reg-file-alt']
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
									th(lang('factory'),'','rowspan="4" class="text-center align-middle headcol"');
									th(lang('production'),'','rowspan="2" class="text-center align-middle headcol" ');
									th(lang('productivity'),'','rowspan="" colspan ="2" class="text-center align-middle headcol" ');
									th(lang('total'),'','rowspan="" colspan ="2" class="text-center align-middle headcol" ');
									th(lang('prosentase') . ' (%)','','rowspan="" colspan ="2" class="text-center align-middle headcol" ');
								tr();
									th(lang('description'),'','rowspan="3" width="300"class="text-center align-middle headcol" ');
									th(lang('code'),'','rowspan="3" class="text-center align-middle headcol" ');
								tr();
									th(lang('quantity'),'','rowspan="2" class="text-center align-middle headcol" ');
									th(lang('man_working'),'','rowspan="1" class="text-center align-middle headcol" ');
									th(lang('machine_working'),'','rowspan="1" class="text-center align-middle headcol" ');
									th(lang('man_working'),'','rowspan="1" class="text-center align-middle headcol" ');
									th(lang('machine_working'),'','rowspan="1" class="text-center align-middle headcol" ');
									th(lang('man_working'),'','rowspan="1" class="text-center align-middle headcol" ');
									th(lang('machine_working'),'','rowspan="1" class="text-center align-middle headcol" ');
								tr();
									th(lang('hours'),'','rowspan="" class="text-center align-middle headcol" ');
									th(lang('hours'),'','rowspan="" class="text-center align-middle headcol" ');
									th(lang('hours'),'','rowspan="1" class="text-center align-middle headcol" ');
									th(lang('hours'),'','rowspan="1" class="text-center align-middle headcol" ');
									th(lang('hours'),'','rowspan="1" class="text-center align-middle headcol" ');
									th(lang('hours'),'','rowspan="1" class="text-center align-middle headcol" ');

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
		form_open(base_url('transaction/ovh_alocation_actual/import'),'post','form-import');
			col_init(3,9);
			input('text',lang('tahun'),'tahun','','','readonly');
			input('text',lang('bulan'),'bulan_import','','','readonly');
			fileupload('File Excel','fileimport','required','data-accept="xls|xlsx"');
			form_button(lang('impor'),lang('batal'));
		form_close();
modal_close();
?>
<script type="text/javascript">

$('#filter_anggaran').change(function(){
	getData();
	getData_dpk();
	getData_kredit();
});

$('#filter_cabang').change(function(){
	getData();
	getData_dpk();
	getData_kredit();
});

$(document).ready(function () {

	getData();

    $(document).on('keyup', '.calculate', function (e) {
    	// calculate();
    });
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
		var page = base_url + 'transaction/ovh_alocation_actual/data';
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
			}
		});
}


$(function(){
	getData();
});

$(document).on('dblclick','.table-1 tbody td .badge',function(){
	if($(this).closest('tr').find('.btn-input').length == 1) {
		var badge_status 	= '0';
		var data_id 		= $(this).closest('tr').find('.btn-input').attr('data-id');
		if( $(this).hasClass('badge-danger') ) {
			badge_status = '1';
		}
		active_inactive(data_id,badge_status);
	}
});


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
	// var total_manwh = 0;

	$('#result tbody tr').each(function(){
		if($(this).find('.qty_production').length == 1) {
			// $(this).find('.manwh_total').val(1000)

			let qtyProduction = moneyToNumber($(this).find('.qty_production').text().replace(/\,/g,''))
			let manwh_prod = moneyToNumber($(this).find('.manwh_productivity').text().replace(/\,/g,''))
			let macwh_prod = moneyToNumber($(this).find('.macwh_productivity').text().replace(/\,/g,''))

			let total_manwh = 0
			if(manwh_prod > 0) total_manwh = customFormat(qtyProduction / manwh_prod)
			

			let total_macwh = 0
			if(macwh_prod > 0) total_macwh = customFormat(qtyProduction / macwh_prod)

			$(this).find('.manwh_total').text(total_manwh)
			$(this).find('.macwh_total').text(total_macwh)
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
		url : base_url + 'transaction/ovh_alocation_actual/save_perubahan',
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


$('.btn-act-import').click(function(){
	$('#form-import')[0].reset();
	$('#tahun').val($('#filter_tahun').val())
	$('#bulan_import').val($('#bulan').val())
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
	table += '<tr><td colspan="1"> Product OVerhead Allocation </td><td colspan="25">: '+$('#filter_tahun option:selected').text()+'</td></tr>';
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
var tahun = '';
var bulan = '';
var id_cost_centre = '';
$(document).on('click','.btn-proses',function(e){
	e.preventDefault();
	id_proses = 'proses';
	tahun = $('#filter_tahun').val();
	bulan = $('#bulan').val();
	id_cost_centre = $('#filter_cost_centre').val();
	cConfirm.open(lang.apakah_anda_yakin + '?','lanjut');
});

function lanjut() {
	$.ajax({
		url : base_url + 'transaction/ovh_alocation_actual/proses_rounding/'+tahun+'/'+bulan+'/'+id_cost_centre,
			data : {id:id_proses,tahun : tahun, bulan : bulan, cost_centre : id_cost_centre},
			type : 'post',
			dataType : 'json',
			success : function(res) {
				cAlert.open(res.message,res.status,'refreshData');
			}
		});
	}

</script>