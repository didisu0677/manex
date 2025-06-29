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
			<?php 
			if($access['access_input']==1){ 
				echo '<button class="btn btn-info btn-proses" href="javascript:;" ><i class="fa-process"></i> Posting to Budget</button>';			
				echo access_button('delete,active,inactive,export,import'); 
			}
			?>
		</div>
		<div class="clearfix"></div>
	</div>
</div>
<div class="content-body">
	<?php
	table_open('',true,base_url('transaction/unit_materialcost/data'),'tbl_unit_material_cost');
		thead();
			tr();
				th('checkbox','text-center','width="30" data-content="id"');
				th(lang('tahun'),'','data-content="tahun"');
				th(lang('product_code'),'','data-content="product_code"');
				th(lang('qty_production'),'text-right','data-content="qty_production" data-type="currency"');
				th(lang('bottle'),'text-right','data-content="bottle" data-type="currency"');
				th(lang('content'),'text-right','data-content="content" data-type="currency"');
				th(lang('packing'),'text-right','data-content="packing" data-type="currency"');
				th(lang('set'),'text-right','data-content="set" data-type="currency"');
				th(lang('subrm_total'),'text-right','data-content="subrm_total" data-type="currency"');
				th(lang('aktif').'?','text-center','data-content="is_active" data-type="boolean"');
				th('&nbsp;','','width="30" data-content="action_button"');
	table_close();
	?>
</div>
<?php 
modal_open('modal-form');
	modal_body();
		form_open(base_url('transaction/unit_materialcost/save'),'post','form');
			col_init(3,9);
			input('hidden','id','id');
			input('text',lang('tahun'),'tahun');
			input('text',lang('id_product'),'id_product');
			input('text',lang('product_code'),'product_code');
			input('text',lang('qty_production'),'qty_production');
			input('text',lang('bottle'),'bottle');
			input('text',lang('content'),'content');
			input('text',lang('packing'),'packing');
			input('text',lang('set'),'set');
			input('text',lang('subrm_total'),'subrm_total');
			toggle(lang('aktif').'?','is_active');
			form_button(lang('simpan'),lang('batal'));
		form_close();
	modal_footer();
modal_close();
modal_open('modal-import',lang('impor'));
	modal_body();
		form_open(base_url('transaction/unit_materialcost/import'),'post','form-import');
			col_init(3,9);
			fileupload('File Excel','fileimport','required','data-accept="xls|xlsx"');
			form_button(lang('impor'),lang('batal'));
		form_close();
modal_close();
?>

<script>
$(document).ready(function() {
	var url = base_url + 'transaction/unit_materialcost/data/' ;
		url 	+= '/'+$('#filter_tahun').val() 
	$('[data-serverside]').attr('data-serverside',url);
	refreshData();
});	

$('#filter_tahun').change(function(){
	var url = base_url + 'transaction/unit_materialcost/data/' ;
		url 	+= '/'+$('#filter_tahun').val() 
	$('[data-serverside]').attr('data-serverside',url);
	
	refreshData();
});

var id_proses = '';
	var tahun = 0;
	$(document).on('click','.btn-proses',function(e){
		e.preventDefault();
		id_proses = 'proses';
		tahun = $('#filter_tahun').val();
		cConfirm.open(lang.apakah_anda_yakin + '?','lanjut');
	});

	function lanjut() {
		$.ajax({
			url : base_url + 'transaction/unit_materialcost/proses',
			data : {id:id_proses,tahun : tahun},
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
		$.redirect(base_url + 'transaction/unit_materialcost/export/', {tahun:$('#filter_tahun').val()} , 'get');
	});
</script>