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

			<?php 
			$import = '';
			$delete = '';
			if($access['access_input']==1) {
				echo '<button class="btn btn-info btn-proses" href="javascript:;" ><i class="fa-process"></i> Posting to Budget</button>';
				$import = 'import';
				$delete = 'delete';
			}
			echo access_button($delete . ',export,'.$import); ?>
		</div>
		<div class="clearfix"></div>
	</div>
</div>
<div class="content-body">
	<?php
	table_open('',true,base_url('transaction/actual_sales/data'),'tbl_actual_gross_profit');
		thead();
			tr();
				th('checkbox','text-center','width="30" data-content="id"');
				th(lang('tahun'),'','data-content="tahun"');
				th(lang('bulan'),'','data-content="bulan"');
				th(lang('product_code'),'','data-content="product_code"');
				th(lang('description'),'','data-content="description"');
				th(lang('pl_code'),'','data-content="pl_code"');
				th(lang('desc_pl'),'','data-content="desc_pl"');
				th(lang('factory'),'','data-content="factory"');
				th(lang('address'),'','data-content="address"');
				th(lang('sector'),'','data-content="sector"');
				th(lang('group_sector'),'','data-content="group_sector"');
				th(lang('qty_sales'),'text-right','data-content="qty_sales" data-type="currency"');
				th(lang('sales_amount'),'text-right','data-content="sales_amount" data-type="currency"');
				th(lang('discount'),'text-right','data-content="discount" data-type="currency"');
				th(lang('cogs'),'text-right','data-content="cogs" data-type="currency"');
				th(lang('unit_cogs'),'text-right','data-content="unit_cogs" data-type="currency"');
				th(lang('cogs_idle'),'text-right','data-content="cogs_idle" data-type="currency"');
				th(lang('cogs_loss'),'text-right','data-content="cogs_loss" data-type="currency"');
				th(lang('gross_prpofit'),'text-right','data-content="gross_prpofit" data-type="currency"');
				th(lang('customer'),'','data-content="customer"');
				th(lang('aktif').'?','text-center','data-content="is_active" data-type="boolean"');
				// th('&nbsp;','','width="30" data-content="action_button"');
	table_close();
	?>
</div>
<?php 
modal_open('modal-form');
	modal_body();
		form_open(base_url('transaction/actual_sales/save'),'post','form');
			col_init(3,9);
			input('hidden','id','id');
			input('text',lang('bulan'),'bulan');
			input('text',lang('product_code'),'product_code');
			input('text',lang('description'),'description');
			input('text',lang('pl_code'),'pl_code');
			input('text',lang('desc_pl'),'desc_pl');
			input('text',lang('factory'),'factory');
			input('text',lang('address'),'address');
			input('text',lang('sector'),'sector');
			input('text',lang('group_sector'),'group_sector');
			input('text',lang('qty_sales'),'qty_sales');
			input('text',lang('sales_amount'),'sales_amount');
			input('text',lang('discount'),'discount');
			input('text',lang('cogs'),'cogs');
			input('text',lang('unit_cogs'),'unit_cogs');
			input('text',lang('cogs_idle'),'cogs_idle');
			input('text',lang('cogs_loss'),'cogs_loss');
			input('text',lang('gross_prpofit'),'gross_prpofit');
			input('text',lang('customer'),'customer');
			toggle(lang('aktif').'?','is_active');
			form_button(lang('simpan'),lang('batal'));
		form_close();
	modal_footer();
modal_close();
modal_open('modal-import',lang('impor'));
	modal_body();
		form_open(base_url('transaction/actual_sales/import'),'post','form-import');
			col_init(3,9);
			input('text',lang('tahun'),'tahun_import','','','readonly');
			input('text',lang('bulan'),'bulan_import','','','readonly');
			fileupload('File Excel','fileimport','required','data-accept="xls|xlsx"');
			form_button(lang('impor'),lang('batal'));
		form_close();
modal_close();
?>

<script>

$(document).ready(function() {
	var url = base_url + 'transaction/actual_sales/data/' ;
		url 	+= '/'+$('#filter_tahun').val() 
		url 	+= '/'+$('#bulan').val() 
	$('[data-serverside]').attr('data-serverside',url);
	refreshData();
});	

$('#filter_tahun').change(function(){
	var url = base_url + 'transaction/actual_sales/data/' ;
		url 	+= '/'+$('#filter_tahun').val() 
		url 	+= '/'+$('#bulan').val() 
	$('[data-serverside]').attr('data-serverside',url);
	
	refreshData();
});

$('#bulan').change(function(){
	var url = base_url + 'transaction/actual_sales/data/' ;
		url 	+= '/'+$('#filter_tahun').val() 
		url 	+= '/'+$('#bulan').val() 
	$('[data-serverside]').attr('data-serverside',url);
	
	refreshData();
});

$('.btn-act-import').click(function(){
	$('#form-import')[0].reset();
	$('#tahun_import').val($('#filter_tahun').val());
	$('#bulan_import').val($('#bulan').val());
});


var id_proses = '';
	var tahun = 0;
	$(document).on('click','.btn-proses',function(e){
		e.preventDefault();
		id_proses = 'proses';
		tahun = $('#filter_tahun').val();
		bulan = $('#bulan').val();
		cConfirm.open(lang.apakah_anda_yakin + '?','lanjut');
	});

	function lanjut() {
		$.ajax({
			url : base_url + 'transaction/actual_sales/proses',
			data : {id:id_proses,tahun : tahun, bulan : bulan},
			type : 'post',
			dataType : 'json',
			success : function(res) {
				cAlert.open(res.message,res.status,'refreshData');
			}
		});
	}

</script>