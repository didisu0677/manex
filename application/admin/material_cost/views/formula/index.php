<div class="content-header">
	<div class="main-container position-relative">
		<div class="header-info">
			<div class="content-title"><?php echo $title; ?></div>
			<?php echo breadcrumb(); ?>
		</div>
		<div class="float-right">

			<label class=""><?php echo lang('item_product'); ?> &nbsp</label>
			<select class="select2 custom-select" style="width: 280px;" id="filter_produk">
				<option value="ALL">ALL</option>
				<?php foreach ($produk_items as $p) { ?>
					<option value="<?php echo $p->parent_item; ?>"><?php echo $p->parent_item . ' | ' . $p->item_name; ?></option>
				<?php } ?>
			</select>

			<?php echo access_button('delete,active,inactive,export,import'); ?>
		</div>
		<div class="clearfix"></div>
	</div>
</div>
<div class="content-body">
	<?php
	table_open('',true,base_url('material_cost/formula/data'),'tbl_material_formula');
		thead();
			tr();
				th('checkbox','text-center','width="30" data-content="id"');
				th(lang('parent_item'),'','data-content="parent_item"');
				th(lang('item_name'),'','data-content="item_name"');
				th(lang('description'),'','data-content="description"');
				th(lang('component_item'),'','data-content="component_item"');
				th(lang('material_name'),'','data-content="material_name"');
				th(lang('um'),'','data-content="um"');
				th(lang('quantity'),'','data-content="quantity"');
				th(lang('scrap'),'','data-content="scrap"');
				th(lang('total'),'','data-content="total"');
				th(lang('operation'),'','data-content="operation"');
				th(lang('article_number'),'','data-content="article_number"');
				th(lang('start_effective'),'','data-content="start_effective" data-type="daterange"');
				th(lang('aktif').'?','text-center','data-content="is_active" data-type="boolean"');
				th('&nbsp;','','width="30" data-content="action_button"');
	table_close();
	?>
</div>
<?php 
modal_open('modal-form');
	modal_body();
		form_open(base_url('material_cost/formula/save'),'post','form');
			col_init(3,9);
			input('hidden','id','id');
			input('text',lang('parent_item'),'parent_item');
			input('text',lang('item_name'),'item_name');
			input('text',lang('description'),'description');
			input('text',lang('component_item'),'component_item');
			input('text',lang('material_name'),'material_name');
			input('text',lang('um'),'um');
			input('text',lang('quantity'),'quantity');
			input('text',lang('scrap'),'scrap');
			input('text',lang('total'),'total');
			input('text',lang('operation'),'operation');
			input('text',lang('article_number'),'article_number');
			input('date',lang('start_effective'),'start_effective');
			toggle(lang('aktif').'?','is_active');
			form_button(lang('simpan'),lang('batal'));
		form_close();
	modal_footer();
modal_close();
modal_open('modal-import',lang('impor'));
	modal_body();
		form_open(base_url('material_cost/formula/import'),'post','form-import');
			col_init(3,9);
			fileupload('File Excel','fileimport','required','data-accept="xls|xlsx"');
			form_button(lang('impor'),lang('batal'));
		form_close();
modal_close();
?>

<script>

$(document).ready(function() {
	var url = base_url + 'material_cost/formula/data/' ;
		url 	+= '/'+$('#filter_produk').val() 
	$('[data-serverside]').attr('data-serverside',url);
	refreshData();
});	

$('#filter_produk').change(function(){
	var url = base_url + 'material_cost/formula/data/' ;
		url 	+= '/'+$('#filter_produk').val() 
	$('[data-serverside]').attr('data-serverside',url);
	refreshData();
});
				
</script>
