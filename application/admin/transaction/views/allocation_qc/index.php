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
    		
			<label class=""><?php echo lang('factory'); ?>  &nbsp</label>
			<select class="select2 infinity custom-select" style="width: 180px;" id="filter_cost_centre">
				<option value="ALL">ALL FACTORY</option>
				<?php foreach ($cc as $c) { ?>
                <option value="<?php echo $c->kode; ?>"><?php echo $c->cost_centre; ?></option>
                <?php } ?>
			</select>

    		<?php 

			// echo '<button class="btn btn-success btn-save" href="javascript:;" ><i class="fa-save"></i> Save</button>';
			if($access['access_input']==1) {
				echo '<button class="btn btn-danger btn-proses" href="javascript:;" ><i class="fa-process"></i> Rounding Persentage</button>';
			}

			$arr = [];
				$arr = [
					// ['btn-save','Save Data','fa-save'],
					['btn-export','Export Data','fa-upload'],
                    ($access['access_input']  ? ['btn-act-import','Import Data','fa-download']:''),
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
									th(lang('factory'),'','rowspan="2" class="text-center align-middle headcol"');
									th(lang('production'),'','rowspan="" class="text-center align-middle headcol" ');
									th(lang('total') ,'','colspan="3" class="text-center align-middle headcol" ');
								tr();
									th(lang('description'),'','rowspan="" width="300"class="text-center align-middle headcol" ');
									th(lang('code'),'','rowspan="" class="text-center align-middle headcol" ');
									th(lang('quantity'),'','rowspan="" class="text-center align-middle headcol" ');
									th(lang('point_perunit'),'','rowspan="" class="text-center align-middle headcol" ');
									th(lang('total_point'),'','rowspan="" class="text-center align-middle headcol" ');
									th(lang('prosentase'),'','rowspan="" class="text-center align-middle headcol" ');

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

$('#filter_anggaran').change(function(){
	getData();

});

$('#filter_cabang').change(function(){
	getData();

});

$(document).ready(function () {

	getData();

    $(document).on('keyup', '.alokasi', function (e) {
        // calculate();
    });
});	

$('#filter_tahun').change(function(){
	getData();
});

$('#filter_cost_centre').change(function(){
	getData();
});

function getData() {

		cLoader.open(lang.memuat_data + '...');
		$('.overlay-wrap').removeClass('hidden');
		var page = base_url + 'transaction/allocation_qc/data';
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

                // calculate();
				money_init()
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

// $(document).on('keyup','.edit-value',function(e){
// 	var wh 			= e.which;
// 	if((48 <= wh && wh <= 57) || (96 <= wh && wh <= 105) || wh == 8) {
// 		if($(this).text() == '') {
// 			$(this).text('');
// 		} else {
// 			var n = parseInt($(this).text().replace(/[^0-9\-]/g,''),10);
// 		    $(this).text(n.toLocaleString());
// 		    var selection = window.getSelection();
// 			var range = document.createRange();
// 			selection.removeAllRanges();
// 			range.selectNodeContents($(this)[0]);
// 			range.collapse(false);
// 			selection.addRange(range);
// 			$(this)[0].focus();
// 		}
// 	}
// });
// $(document).on('keypress','.edit-value',function(e){
// 	var wh 			= e.which;
// 	if (e.shiftKey) {
// 		if(wh == 0) return true;
// 	}
// 	if(e.metaKey || e.ctrlKey) {
// 		if(wh == 86 || wh == 118) {
// 			$(this)[0].onchange = function(){
// 				$(this)[0].innerHTML = $(this)[0].innerHTML.replace(/[^0-9\-]/g, '');
// 			}
// 		}
// 		return true;
// 	}
// 	if(wh == 0 || wh == 8 || wh == 45 || (48 <= wh && wh <= 57) || (96 <= wh && wh <= 105)) 
// 		return true;
// 	return false;
// });


function calculate() {
	var sum_product_qty = 0;
	var sum_point_perunit = 0;
	var sum_total_point = 0;
	var sum_prsnalokasi = 0;
	$('#result tbody tr').each(function(){
		if($(this).find('.product_qty').length == 1) {
			var product_qty = moneyToNumber($(this).find('.product_qty').text());
			sum_product_qty += product_qty;
		}

		if($(this).find('.prsnalokasi').length == 1) {
			var prsnalokasi = moneyToNumberxx($(this).find('.prsnalokasi').text());
			sum_prsnalokasi += prsnalokasi;
		}

		// if($(this).find('.point_perunit').length == 1) {
		// 	var point_perunit = moneyToNumberxx($(this).find('.point_perunit').text());
		// 	sum_point_perunit += point_perunit;
		// }

		if($(this).find('.total_point').length == 1) {
			var total_point = moneyToNumber($(this).find('.total_point').text());
			sum_total_point += total_point;
		}

		// if(($(this).find('.product_qty').length == 1 && $(this).find('.point_perunit').length == 1)) {
		// 	// $(this).find('.manwh_total').val(1000)
		// 	let qtyProduction = moneyToNumber($(this).find('.product_qty').text().replace(/\,/g,''))
		// 	let point_perunit = moneyToNumberxx($(this).find('.point_perunit').text().replace(/\,/g,''))

		// 	let total_point = 0
		// 	total_point = (qtyProduction * point_perunit)
			
		// 	$(this).find('.total_point').text(customFormat(total_point,2))
		// }

		
		
	});
	$('.sum_product_qty').text(customFormat(sum_product_qty));
	$('.total_alokasi1').text(customFormat(sum_prsnalokasi));
	$('.sum_total_point').text(customFormat(sum_total_point));

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
		// data_edit[$(this).attr('data-id')][$(this).attr('data-name')] = $(this).text().replace(/[^0-9\-]/g,'');
        data_edit[$(this).attr('data-id')][$(this).attr('data-name')] = $(this).text().replace(/\,/g,'');
		i++;
	});
	
	var jsonString = JSON.stringify(data_edit);		
	$.ajax({
		url : base_url + 'transaction/allocation_qc/save_perubahan',
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
	table += '<tr><td colspan="1"> Product QC Allocation </td><td colspan="25">: '+$('#filter_tahun option:selected').text()+'</td></tr>';
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
$(document).on('click','.btn-proses',function(e){
	e.preventDefault();
	id_proses = 'proses';
	tahun = $('#filter_tahun').val();
	cConfirm.open(lang.apakah_anda_yakin + '?','lanjut');
});

function lanjut() {
	$.ajax({
		url : base_url + 'transaction/allocation_qc/proses_rounding/'+tahun,
			data : {id:id_proses,tahun : tahun},
			type : 'post',
			dataType : 'json',
			success : function(res) {
				cAlert.open(res.message,res.status,'refreshData');
			}
		});
	}

</script>