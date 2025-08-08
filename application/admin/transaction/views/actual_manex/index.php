<div class="content-header">
	<div class="main-container position-relative">
		<div class="header-info">
			<div class="content-title"><?php echo $title; ?></div>
			<?php echo breadcrumb(); ?>
		</div>
		<div class="float-right">
			<label class=""><?php echo lang('tahun'); ?> &nbsp</label>
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

			<select class="select2 infinity custom-select" style = "width : 100px" id="filter_estimate">
				<option value="0"><?php echo lang('actual') . str_repeat('&nbsp;', 5); ?></option>
				<option value="1"><?php echo lang('estimate') ?></option>
			</select>

			<?php 
			$import = '';
			$delete = '';
			if($access['access_input']==1) {
				echo '<button class="btn btn-info btn-proses" href="javascript:;" ><i class="fa-process"></i> Posting to Budget</button>';
				$import = 'import';
				$delete = 'delete';
			}

			echo access_button($delete .',export,'.$import); ?>
		</div>
		<div class="clearfix"></div>
	</div>
</div>
<div class="content-body">
	<?php
	table_open('',true,base_url('transaction/actual_manex/data'),'tbl_actual_manex');
		thead();
			tr();
				th('checkbox','text-center','width="30" data-content="id"');
				th(lang('tahun'),'','data-content="tahun"');
				th(lang('bulan'),'','data-content="bulan"');
				th(lang('account_code'),'','data-content="account_code"');
				th(lang('cost_centre'),'','data-content="cost_centre"');
				th(lang('sub_account'),'','data-content="sub_account"');
				th(lang('initial_cc'),'','data-content="initial_cc"');
				th(lang('total'),'text-right','data-content="total" data-type="currency"');
				// th(lang('aktif').'?','text-center','data-content="is_active" data-type="boolean"');
				th('&nbsp;','','width="30" data-content="action_button"');
	table_close();
	?>
</div>
<?php 
modal_open('modal-form');
	modal_body();
		form_open(base_url('transaction/actual_manex/save'),'post','form');
			col_init(3,9);
			input('hidden','id','id');
			input('text',lang('tahun'),'tahun','','','readonly');
			input('text',lang('bulan'),'bulan','','','readonly');
			input('text',lang('account_code'),'account_code');
			input('text',lang('cost_centre'),'cost_centre');
			input('text',lang('initial_cc'),'initial_cc');
			input('text',lang('total'),'total');
			// toggle(lang('aktif').'?','is_active');
			form_button(lang('simpan'),lang('batal'));
		form_close();
	modal_footer();
modal_close();
modal_open('modal-import',lang('impor'));
	modal_body();
		form_open(base_url('transaction/actual_manex/import'),'post','form-import');
			col_init(3,9);
			input('text',lang('tahun'),'tahun_import','','','readonly');
			input('text',lang('bulan'),'bulan_import','','','readonly');
			?>
			<div class="form-group row">
				<label class="col-form-label col-sm-3 required"><?php echo lang('estimate') . ' / ' . lang('actual'); ?></label>
				<div class="col-md-4 col-12">
				<select class="select2 infinity custom-select" style = "width : 100px" id="filter_import" name="filter_import">
					<option value="0"><?php echo lang('actual') . str_repeat('&nbsp;', 5); ?></option>
					<option value="1"><?php echo lang('estimate') ?></option>
				</select>
				</div>
			</div>

			<?php
			fileupload('File Excel','fileimport','required','data-accept="xls|xlsx"');
			form_button(lang('impor'),lang('batal'));
		form_close();
modal_close();
?>

<script>
$(document).ready(function() {
	var url = base_url + 'transaction/actual_manex/data/' ;
		url 	+= '/'+$('#filter_tahun').val() 
		url 	+= '/'+$('#bulan').val() 
		url 	+= '/'+$('#filter_estimate').val() 
	$('[data-serverside]').attr('data-serverside',url);
	refreshData();
});	

$('#filter_tahun').change(function(){
	var url = base_url + 'transaction/actual_manex/data/' ;
		url 	+= '/'+$('#filter_tahun').val() 
		url 	+= '/'+$('#bulan').val() 
		url 	+= '/'+$('#filter_estimate').val() 
	$('[data-serverside]').attr('data-serverside',url);
	
	refreshData();
});

$('#bulan').change(function(){
	var url = base_url + 'transaction/actual_manex/data/' ;
		url 	+= '/'+$('#filter_tahun').val() 
		url 	+= '/'+$('#bulan').val() 
		url 	+= '/'+$('#filter_estimate').val() 
	$('[data-serverside]').attr('data-serverside',url);
	
	refreshData();
});

$('#filter_estimate').change(function(){
	var url = base_url + 'transaction/actual_manex/data/' ;
		url 	+= '/'+$('#filter_tahun').val() 
		url 	+= '/'+$('#bulan').val() 
		url 	+= '/'+$('#filter_estimate').val() 
	$('[data-serverside]').attr('data-serverside',url);
	
	refreshData();
});

$('.btn-act-import').click(function(){
	$('#form-import')[0].reset();
	$('#tahun_import').val($('#filter_tahun').val());
	$('#bulan_import').val($('#bulan').val());
	$('#filter_import').val($('#filter_estimate').val()).trigger('change')
});


var id_proses = '';
	var tahun = 0;
	$(document).on('click','.btn-proses',function(e){
		e.preventDefault();
		id_proses = 'proses';
		tahun = $('#filter_tahun').val();
		bulan = $('#bulan').val();
		is_estimate = $('#filter_estimate').val();
		cConfirm.open(lang.apakah_anda_yakin + '?','lanjut');
	});

	function lanjut() {
		$.ajax({
			url : base_url + 'transaction/actual_manex/proses',
			data : {id:id_proses,tahun : tahun, bulan : bulan, is_estimate : is_estimate},
			type : 'post',
			dataType : 'json',
			success : function(res) {
				cAlert.open(res.message,res.status,'refreshData');
			}
		});
	}

	$(document).on('click','.btn-act-export',function(e){
		// alert('x');die;
		e.preventDefault();
		$.redirect(base_url + 'transaction/actual_manex/export/', {tahun:$('#filter_tahun').val(),bulan:$('#bulan').val(),is_estimate:$('#filter_estimate').val()} , 'get');
	});
</script>