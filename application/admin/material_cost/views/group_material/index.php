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
	table_open('',true,base_url('material_cost/group_material/data'),'tbl_group_material');
		thead();
			tr();
				th('checkbox','text-center','width="30" data-content="id"');
				th(lang('pc'),'','data-content="pc"');
				th(lang('pc_um'),'','data-content="pc_um"');
				th(lang('mc'),'','data-content="mc"');
				th(lang('mc_um'),'','data-content="mc_um"');
				th(lang('qty'),'','data-content="qty"');
				th(lang('scr'),'','data-content="scr"');
				th(lang('group'),'','data-content="group"');
				th(lang('aktif').'?','text-center','data-content="is_active" data-type="boolean"');
				th('&nbsp;','','width="30" data-content="action_button"');
	table_close();
	?>
</div>
<?php 
modal_open('modal-form');
	modal_body();
		form_open(base_url('material_cost/group_material/save'),'post','form');
			col_init(3,9);
			input('hidden','id','id');
			input('text',lang('pc'),'pc');
			input('text',lang('pc_um'),'pc_um');
			input('text',lang('mc'),'mc');
			input('text',lang('mc_um'),'mc_um');
			input('text',lang('qty'),'qty');
			input('text',lang('scr'),'scr');
			input('text',lang('group'),'group');
			toggle(lang('aktif').'?','is_active');
			form_button(lang('simpan'),lang('batal'));
		form_close();
	modal_footer();
modal_close();
modal_open('modal-import',lang('impor'));
	modal_body();
		form_open(base_url('material_cost/group_material/import'),'post','form-import');
			col_init(3,9);
			fileupload('File Excel','fileimport','required','data-accept="xls|xlsx"');
			form_button(lang('impor'),lang('batal'));
		form_close();
modal_close();
?>
