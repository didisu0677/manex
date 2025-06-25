<div class="content-header">
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

			<label class=""><?php echo lang('users'); ?>  &nbsp</label>
			<select class="select2 infinity custom-select" style="width: 180px;" id="filter_username">
				<option value = 0>ALL</option>
				<?php foreach ($user_filter as $c) { ?>
                <option value="<?php echo $c['id']; ?>"><?php echo $c['nama']; ?></option>
                <?php } ?>
			</select>
			<?php
			$arr = [];
			$arr = [
				// ['btn-export','Export Data','fa-download'],
				// ['btn-template','Template Import','fa-file-excel']
			];

			$delete = '';
			$import = '';
			if($submit == 0) {
				$delete = 'delete';
				$import = 'import';
			}

			echo access_button($delete. ',active,inactive,export,' .$import ,$arr)
			?>
			
		</div>
		<div class="clearfix"></div>
	</div>
</div>
<div class="content-body">
	<?php
	table_open('',true,base_url('material_cost/material_price/data'),'tbl_material_price');
		thead();
			tr();
				th('checkbox','text-center','width="30" data-content="id"');
				th(lang('year'),'','data-content="year"');
				th(lang('material_code'),'','data-content="material_code"');
				th(lang('kode_budget'),'','data-content="kode_budget"');
				th(lang('nama'),'','data-content="nama"');
				th(lang('vcode'),'','data-content="vcode"');
				th(lang('loc'),'','data-content="loc"');
				th(lang('bm'),'','data-content="bm"');
				th(lang('curr'),'','data-content="curr"');
				th(lang('price_us'),'','data-content="price_us"');
				th(lang('bank_charges'),'','data-content="bank_charges"');
				th(lang('handling_charges'),'','data-content="handling_charges"');
				th(lang('user_id'),'','data-content="nama", data-table ="tbl_user"');
				th(lang('aktif').'?','text-center','data-content="is_active" data-type="boolean"');
				th('&nbsp;','','width="30" data-content="action_button"');
	table_close();
	?>
</div>
<?php 
modal_open('modal-form','','','data-openCallback="formOpen"');
	modal_body();
		form_open(base_url('material_cost/material_price/save'),'post','form');
			col_init(3,9);
			input('hidden','id','id');
			input('text',lang('year'),'year','required','','readonly');
			input('text',lang('material_code'),'material_code');
			input('text',lang('kode_budget'),'kode_budget');
			input('text',lang('nama'),'nama');
			input('text',lang('vcode'),'vcode');
			input('text',lang('loc'),'loc');
			input('text',lang('bm'),'bm');
			input('text',lang('curr'),'curr');
			input('text',lang('price_us'),'price_us');
			select2(lang('user_id'),'id_user','required',$user_id,'id','nama');
			toggle(lang('aktif').'?','is_active');
			form_button(lang('simpan'),lang('batal'));
		form_close();
	modal_footer();
modal_close();
modal_open('modal-import',lang('impor'));
	modal_body();
		form_open(base_url('material_cost/material_price/import'),'post','form-import');
			col_init(3,9);
			select2(lang('users'),'user_import','required',$user_id,'id','nama');
			input('text',lang('year'),'tahun_import','','','readonly');
			fileupload('File Excel','fileimport','required','data-accept="xls|xlsx"');
			form_button(lang('impor'),lang('batal'));
		form_close();
modal_close();
?>

<script>
	$(document).ready(function() {
		var url = base_url + 'material_cost/material_price/data/' ;
			url 	+= '/'+$('#filter_tahun').val(),
			url 	+= '/'+$('#filter_username').val() 
		$('[data-serverside]').attr('data-serverside',url);
		refreshData();
	});	

	$('#filter_tahun').change(function(){
		var url = base_url + 'material_cost/material_price/data/' ;
			url 	+= '/'+$('#filter_tahun').val() 
			url 	+= '/'+$('#filter_username').val() 
		$('[data-serverside]').attr('data-serverside',url);
		refreshData();
	});

	$('#filter_username').change(function(){
		var url = base_url + 'material_cost/material_price/data/' ;
			url 	+= '/'+$('#filter_tahun').val() 
			url 	+= '/'+$('#filter_username').val() 
		$('[data-serverside]').attr('data-serverside',url);
		refreshData();
	});
			
	function formOpen() {
		is_edit = true;
		var response = response_edit;
		$('#year').val($('#filter_tahun').val())
		if(typeof response.id != 'undefined') {
		} 
		is_edit = false;
	}

	$('.btn-act-import').click(function(){
		$("#modal-import").modal()
		$('#form-import')[0].reset();
		$('#tahun_import').val($('#filter_tahun').val())
		$('#user_import').val(<?php echo user('id'); ?>).trigger('change');
	});

	$(document).on('click','.btn-act-export',function(e){
		// alert('x');die;
		e.preventDefault();
		$.redirect(base_url + 'material_cost/material_price/export/', {tahun:$('#filter_tahun').val()} , 'get');
	});
</script>