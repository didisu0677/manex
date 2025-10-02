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
					<option value="<?php echo $tahun->tahun; ?>"<?php if($tahun->tahun == user('tahun_budget') - 1) echo ' selected'; ?>><?php echo $tahun->tahun; ?></option>
                <?php } ?>
			</select>

			<label for="periode"><?php echo lang('bulan'); ?></label>
			<select class="select2 infinity custom-select" style = "width : 100px" name="filter_bulan" id="filter_bulan">
				<?php for($i = 1; $i <= 12; $i++) { $j = sprintf('%02d',$i); ?>
				<option value="<?php echo $j; ?>"<?php if($j == setting('actual_budget')) echo ' selected'; ?>><?php echo bulan($j); ?></option>
				<?php } ?>
			</select>

			<?php 

			echo '<button class="btn btn-info btn-proses" href="javascript:;" ><i class="fa-process"></i> Alloacation Bari</button>';
			
			?>

			<?php echo access_button('delete,active,inactive,export,import'); ?>
		</div>
		<div class="clearfix"></div>
	</div>
</div>
<div class="content-body">
	<?php
	table_open('',true,base_url('transaction/allocation_bari_actual/data'),'tbl_allocation_bari_actual');
		thead();
			tr();
				th('checkbox','text-center','width="30" data-content="id"');
				th(lang('tahun'),'','data-content="tahun"');
				th(lang('bulan'),'','data-content="bulan"');
				th(lang('product_code'),'','data-content="product_code"');
				th(lang('product_name'),'','data-content="product_name"');
				th(lang('prsn_alloc'),'','data-content="prsn_alloc"');
				th(lang('before_bari'),'','data-content="before_bari"');
				th(lang('bari'),'','data-content="bari"');
				th(lang('after_bari'),'','data-content="after_bari"');
				th(lang('aktif').'?','text-center','data-content="is_active" data-type="boolean"');
				th('&nbsp;','','width="30" data-content="action_button"');
	table_close();
	?>
</div>
<?php 
modal_open('modal-form','','','data-openCallback="formOpen"');
	modal_body();
		form_open(base_url('transaction/allocation_bari_actual/save'),'post','form');
			col_init(3,9);
			input('hidden','id','id');
			input('text',lang('tahun'),'tahun','','','readonly');
			// Create months array using HTML select element
			$current_month = setting('actual_budget');
			?>
			<div class="form-group row">
				<label class="col-sm-3 col-form-label"><?php echo lang('bulan'); ?></label>
				<div class="col-sm-9">
					<select name="bulan" id="bulan" class="form-control select2" style="width:100%">
						<?php for($i = 1; $i <= 12; $i++) { 
							$id = sprintf('%02d', $i);
							$selected = ($id == $current_month) ? 'selected' : '';
						?>
						<option value="<?php echo $id; ?>" <?php echo $selected; ?>><?php echo bulan($id); ?></option>
						<?php } ?>
					</select>
				</div>
			</div>
			<?php 
			input('text',lang('product_code'),'product_code');
			input('text',lang('product_name'),'product_name');
			input('text',lang('prsn_alloc'),'prsn_alloc');
			toggle(lang('aktif').'?','is_active');
			form_button(lang('simpan'),lang('batal'));
		form_close();
	modal_footer();
modal_close();
modal_open('modal-import',lang('impor'));
	modal_body();
		form_open(base_url('transaction/allocation_bari_actual/import'),'post','form-import');
			col_init(3,9);
			fileupload('File Excel','fileimport','required','data-accept="xls|xlsx"');
			form_button(lang('impor'),lang('batal'));
		form_close();
modal_close();
?>

<script>
	$(document).ready(function() {
		var url = base_url + 'transaction/allocation_bari_actual/data/' ;
			url 	+= '/'+$('#filter_tahun').val(),
			url 	+= '/'+$('#filter_bulan').val(),
		$('[data-serverside]').attr('data-serverside',url);
		refreshData();
	});	

	function formOpen() {
		is_edit = true;
		var response = response_edit;
		$('#tahun').val($('#filter_tahun').val()),
		$('#bulan').val($('#filter_bulan').val());
		if(typeof response.id != 'undefined') {
		} 
		is_edit = false;
	}

	$('#filter_tahun').change(function(){
		var url = base_url + 'transaction/allocation_bari_actual/data/' ;
			url 	+= '/'+$('#filter_tahun').val(),
			url 	+= '/'+$('#filterbulan').val()
		$('[data-serverside]').attr('data-serverside',url);
		refreshData();
	});

	$('#filter_bulan').change(function(){
		var url = base_url + 'transaction/allocation_bari_actual/data/' ;
			url 	+= '/'+$('#filter_tahun').val(),
			url 	+= '/'+$('#filterbulan').val()
		$('[data-serverside]').attr('data-serverside',url);
		refreshData();
	});

	var id_proses = '';
	var tahun = 0;
	$(document).on('click','.btn-proses',function(e){
		e.preventDefault();
		id_proses = 'proses';
		tahun = $('#filter_tahun').val();
		bulan = $('#filter_bulan').val();
		cConfirm.open(lang.apakah_anda_yakin + '?','lanjut');
	});

	function lanjut() {
		$.ajax({
			url : base_url + 'transaction/allocation_bari_actual/proses',
			data : {id:id_proses,tahun : tahun, bulan : bulan},
			type : 'post',
			dataType : 'json',
			success : function(res) {
				cAlert.open(res.message,res.status,'refreshData');
			}
		});
	}
</script>