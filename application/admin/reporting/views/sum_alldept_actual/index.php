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
				<option value="<?php echo $j; ?>"<?php if($j == setting('actual_budget')-1) echo ' selected'; ?>><?php echo bulan($j); ?></option>
				<?php } ?>
			</select>

    		<?php 

			$arr = [];
			$arr = [
				// ['btn-save','Save Data','fa-save'],
				['btn-export','Export Data','fa-upload'],
				['btn-act-import','Import Data','fa-download'],
				// ['btn-act-template','Template Import','fa-reg-file-alt']
			];
		
		
			echo access_button('',$arr); 

			?>
    		</div>
			<div class="clearfix"></div>
			
		</div>
	</div>

<div class="content-body mt-6">
	<div class="main-container mt-6">


		<div class="card-body tab-content">
			<div class="card">
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
								th(lang('total_le'),'','class="text-center align-middle headcol"style="min-width:60px"');
								th(lang('increase'),'','class="text-center align-middle headcol"style="min-width:40px"');
							tbody();
						table_close();
						?>
					</div>
				</div>
			</div>		
		</div>


		
		<!-- <div class="overlay-wrap hidden">
			<div class="overlay-shadow"></div>
			<div class="overlay-content">
				<div class="spinner"></div>
				<p class="text-center">Please wait ... </p>
			</div>
		</div> -->
	</div>
</div>
<?php
modal_open('modal-import',lang('impor'));
	modal_body();
		form_open(base_url('reporting/sum_alldept_actual/import'),'post','form-import');
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

$(document).ready(function () {
	getData();

    $('#filter_cost_centre').trigger('change')
	$(document).on('keyup', '.budget', function (e) {
    	calculate();
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
		var page = base_url + 'reporting/sum_alldept_actual/data';
			page 	+= '/'+$('#filter_tahun').val();
			page 	+= '/'+$('#bulan').val();
			// page    += '/'+$('#filter_allocated').val();

		$.ajax({
			url 	: page,
			data 	: {},
			type	: 'get',
			dataType: 'json',
			success	: function(response) {
				$('.table-1 tbody').html(response.table);
				$('.table-2 tbody').html(response.table2);
				cLoader.close();

			// $('.overlay-wrap').addClass('hidden');	
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

	$('#result .table-1 tbody tr').each(function(){
		if($(this).find('.budget').text() != '') {
	
			let B_01 = moneyToNumber($(this).find('.B_01').text().replace(/\,/g,''))
			let B_02 = moneyToNumber($(this).find('.B_02').text().replace(/\,/g,''))
			let B_03 = moneyToNumber($(this).find('.B_03').text().replace(/\,/g,''))
			let B_04 = moneyToNumber($(this).find('.B_04').text().replace(/\,/g,''))
			let B_05 = moneyToNumber($(this).find('.B_05').text().replace(/\,/g,''))
			let B_06 = moneyToNumber($(this).find('.B_06').text().replace(/\,/g,''))
			let B_07 = moneyToNumber($(this).find('.B_07').text().replace(/\,/g,''))
			let B_08 = moneyToNumber($(this).find('.B_08').text().replace(/\,/g,''))
			let B_09 = moneyToNumber($(this).find('.B_09').text().replace(/\,/g,''))
			let B_10 = moneyToNumber($(this).find('.B_10').text().replace(/\,/g,''))
			let B_11 = moneyToNumber($(this).find('.B_11').text().replace(/\,/g,''))
			let B_12 = moneyToNumber($(this).find('.B_12').text().replace(/\,/g,''))

	
			let total_budget = 0
	
			total_budget = B_01+B_02+B_03+B_04+B_05+B_06+B_07+B_08+B_09+B_10+B_11+B_12
			

			$(this).find('.total_budget').text(customFormat(total_budget))
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
		url : base_url + 'reporting/sum_alldept_actual/save_perubahan',
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

$('.btn-act-import').click(function(){
		$("#modal-import").modal()
		$('#form-import')[0].reset();
		$('#tahun').val($('#filter_tahun').val())
		$('#cost_centre').val($('#filter_cost_centre').val())
		$('#tab').val(activeTable)
		$('#judul').val(judul)
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
	table += '<tr><td colspan="1"> Summary Budget ALL Department </td><td colspan="25">: '+$('#filter_tahun option:selected').text()+'</td></tr>';
	table += '<tr><td colspan="1"> Cost centre </td><td colspan="25">: '+$('#filter_cost_centre option:selected').text()+'</td></tr>';
	table += '<tr><td colspan="1"> Print date </td><td colspan="25">: '+datetime+'</td></tr>';
	table += '</table><br />';
	table += '<table border="1">';
	table += '</table>';

	table += $('#result2').html();
	
	var target = table;
	window.open('data:application/vnd.ms-excel,' + encodeURIComponent(target));
	$('.bg-grey-1,.bg-grey-2.bg-grey-2-1,.bg-grey-2-2,.bg-grey-3').each(function(){
		$(this).removeAttr('bgcolor');
	});
});

</script>