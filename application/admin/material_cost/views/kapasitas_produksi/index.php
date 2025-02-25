<div class="content-header">
	<div class="main-container position-relative">
		<div class="header-info">
			<div class="content-title"><?php echo $title; ?></div>
			<?php echo breadcrumb(); ?>
		</div>
		<div class="float-right">
			<?php echo access_button('delete,active,inactive,export,import'); ?>
		</div>
		<div class="clearfix"></div>
	</div>
</div>
<div class="content-body">
	<?php
	table_open('',true,base_url('material_cost/kapasitas_produksi/data'),'tbl_kapasitas_produksi');
		thead();
			tr();
				th('checkbox','text-center','width="30" data-content="id"');
				th(lang('id_factory'),'','data-content="id_factory"');
				th(lang('cost_centre'),'','data-content="cost_centre"');
				th(lang('kapasitas'),'','data-content="kapasitas"');
				th(lang('factory'),'','data-content="factory"');
				th(lang('wd_01'),'','data-content="WD_01"');
				th(lang('wd_02'),'','data-content="WD_02"');
				th(lang('wd_03'),'','data-content="WD_03"');
				th(lang('wd_04'),'','data-content="WD_04"');
				th(lang('wd_05'),'','data-content="WD_05"');
				th(lang('wd_06'),'','data-content="WD_06"');
				th(lang('wd_07'),'','data-content="WD_07"');
				th(lang('wd_08'),'','data-content="WD_08"');
				th(lang('wd_09'),'','data-content="WD_09"');
				th(lang('wd_10'),'','data-content="WD_10"');
				th(lang('wd_11'),'','data-content="WD_11"');
				th(lang('wd_12'),'','data-content="WD_12"');
				th(lang('aktif').'?','text-center','data-content="is_active" data-type="boolean"');
				th('&nbsp;','','width="30" data-content="action_button"');
	table_close();
	?>
</div>
<?php 
modal_open('modal-form');
	modal_body();
		form_open(base_url('material_cost/kapasitas_produksi/save'),'post','form');
			col_init(3,9);
			input('hidden','id','id');
			input('text',lang('id_factory'),'id_factory');
			input('text',lang('cost_centre'),'cost_centre');
			input('text',lang('kapasitas'),'kapasitas');
			input('text',lang('factory'),'factory');
			input('text',lang('wd_01'),'WD_01');
			input('text',lang('wd_02'),'WD_02');
			input('text',lang('wd_03'),'WD_03');
			input('text',lang('wd_04'),'WD_04');
			input('text',lang('wd_05'),'WD_05');
			input('text',lang('wd_06'),'WD_06');
			input('text',lang('wd_07'),'WD_07');
			input('text',lang('wd_08'),'WD_08');
			input('text',lang('wd_09'),'WD_09');
			input('text',lang('wd_10'),'WD_10');
			input('text',lang('wd_11'),'WD_11');
			input('text',lang('wd_12'),'WD_12');
			toggle(lang('aktif').'?','is_active');
			form_button(lang('simpan'),lang('batal'));
		form_close();
	modal_footer();
modal_close();
modal_open('modal-import',lang('impor'));
	modal_body();
		form_open(base_url('material_cost/kapasitas_produksi/import'),'post','form-import');
			col_init(3,9);
			fileupload('File Excel','fileimport','required','data-accept="xls|xlsx"');
			form_button(lang('impor'),lang('batal'));
		form_close();
modal_close();
?>
