<div class="content-header page-data" data-additional="<?= $access_additional ?>">
	<div class="main-container position-relative">
		<div class="header-info">
			<div class="content-title"><?php echo $title; ?></div>
			<?php echo breadcrumb(); ?>
		</div>
		
		<div class="float-right">
			<label class=""><?php echo lang('tahun'); ?>  &nbsp</label>

			<select class="select2 infinity custom-select" id="filter_tahun">
				<?php foreach ($tahun as $tahun) { ?>
					<option value="<?php echo $tahun->tahun; ?>"<?php if($tahun->tahun == user('tahun_budget')) echo ' selected'; ?>><?php echo $tahun->tahun; ?></option>
                <?php } ?>
			</select>
    		
    		<?php 


			echo '<button class="btn btn-success btn-save" href="javascript:;" ><i class="fa-save"></i> Save</button>';

			$arr = [];
				$arr = [
					// ['btn-save','Save Data','fa-save'],
					['btn-export','Export Data','fa-upload'],
					['btn-import','Import Data','fa-download'],
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
	    				<div class="table-responsive tab-pane fade active show height-window">
	    				<?php
						$thn_sebelumnya = user('tahun_anggaran') -1;
						table_open('table table-bordered table-app table-hover table-1');
							thead();

								tr();
									th(lang('cost_centre_code'),'','rowspan="4" class="text-center align-middle headcol"');
									th(lang('cost_centre'),'','rowspan="4" class="text-center align-middle headcol"');
									th(lang('allocation') . ' (%)','','rowspan="2" class="text-center align-middle headcol" ');
								
							tbody();
						table_close();
						?>
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
		form_open(base_url('transaction/usulan_besaran/import_core'),'post','form-import');
			col_init(3,9);

			form_button(lang('impor'),lang('batal'));
		form_close();
modal_close();
?>
<script type="text/javascript">


$(document).ready(function () {

	getData();

    // $(document).on('keyup', '.calculate', function (e) {
    //     calculate();
    // });
});	

$('#filter_tahun').change(function(){
	getData();
});

function getData() {

		cLoader.open(lang.memuat_data + '...');
		$('.overlay-wrap').removeClass('hidden');
		var page = base_url + 'transaction/common2200/data';
		page 	+= '/'+$('#filter_tahun').val();

		$.ajax({
			url 	: page,
			data 	: {},
			type	: 'get',
			dataType: 'json',
			success	: function(response) {
				$('.table-1 tbody').html(response.table);
				$('#parent_id').html(response.option);
				cLoader.close();
				cek_autocode();
				fixedTable();
				var item_act	= {};
				if($('.table-1 tbody .btn-input').length > 0) {
					item_act['edit'] 		= {name : lang.realisasi, icon : "edit"};					
				}

				var act_count = 0;
				for (var c in item_act) {
					act_count = act_count + 1;
				}
				if(act_count > 0) {
					$.contextMenu({
				        selector: '.table-1 tbody tr', 
				        callback: function(key, options) {
				        	if($(this).find('[data-key="'+key+'"]').length > 0) {
					        	if(typeof $(this).find('[data-key="'+key+'"]').attr('href') != 'undefined') {
					        		window.location = $(this).find('[data-key="'+key+'"]').attr('href');
					        	} else {
						        	$(this).find('[data-key="'+key+'"]').trigger('click');
						        }
						    } 
				        },
				        items: item_act
				    });
				}
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
		url : base_url + 'transaction/common2200/save_perubahan',
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



</script>