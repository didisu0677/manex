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
	table_open('',true,base_url('material_cost/material_supplier/data'),'tbl_material_supplier');
		thead();
			tr();
				th('checkbox','text-center','width="30" data-content="id"');
				th(lang('kode_supplier'),'','data-content="kode_supplier"');
				th(lang('nama_supplier'),'','data-content="nama_supplier"');
				th(lang('material_code'),'','data-content="material_code"');
				th(lang('material_name'),'','data-content="material_name"');
				th(lang('um'),'','data-content="um"');
				th(lang('moq'),'','data-content="moq"');
				th(lang('order_multiple'),'','data-content="order_multiple"');
				th(lang('m_cov'),'','data-content="m_cov"');
				th(lang('total_stock'),'','data-content="total_stock"');
				th(lang('aktif').'?','text-center','data-content="is_active" data-type="boolean"');
				th('&nbsp;','','width="30" data-content="action_button"');
	table_close();
	?>
</div>
<?php 
modal_open('modal-form');
	modal_body();
		form_open(base_url('material_cost/material_supplier/save'),'post','form');
			col_init(3,9);
			input('hidden','id','id');
			input('text',lang('kode_supplier'),'kode_supplier');
			input('text',lang('nama_supplier'),'nama_supplier');
			input('text',lang('material_code'),'material_code');
			input('text',lang('material_name'),'material_name');
			input('text',lang('um'),'um');
			input('text',lang('moq'),'moq');
			input('text',lang('order_multiple'),'order_multiple');
			input('text',lang('m_cov'),'m_cov');
			input('text',lang('total_stock'),'total_stock');
			toggle(lang('aktif').'?','is_active');
			form_button(lang('simpan'),lang('batal'));
		form_close();
	modal_footer();
modal_close();
modal_open('modal-import',lang('impor'));
	modal_body();
		form_open(base_url('material_cost/material_supplier/import'),'post','form-import');
			col_init(3,9);
			fileupload('File Excel','fileimport','required','data-accept="xls|xlsx"');
			form_button(lang('impor'),lang('batal'));
		form_close();
modal_close();
?>
