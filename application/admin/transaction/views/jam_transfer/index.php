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

			<?php echo access_button('delete,active,inactive,export,import'); ?>
		</div>
		<div class="clearfix"></div>
	</div>
</div>
<div class="content-body">
	<?php
	table_open('',true,base_url('transaction/jam_transfer/data'),'tbl_jam_transfer_allocation');
		thead();
			tr();
				th('checkbox','text-center','width="30" data-content="id"');
				th(lang('tahun'),'','data-content="tahun"');
				th(lang('bulan'),'','data-content="bulan"');
				th(lang('account_code'),'','data-content="account_code"');
				th(lang('allocation_amount'),'','data-content="allocation_amount"');
				th(lang('cost_centre_asal'),'','data-content="cost_centre_asal"');
				th(lang('cost_centre_tujuan'),'','data-content="cost_centre_tujuan"');
				th(lang('aktif').'?','text-center','data-content="is_active" data-type="boolean"');
				th('&nbsp;','','width="30" data-content="action_button"');
	table_close();
	?>
</div>
<?php 
modal_open('modal-form');
	modal_body();
		form_open(base_url('transaction/jam_transfer/save'),'post','form');
			col_init(3,9);
			input('hidden','id','id');
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
			input('text',lang('account_code'),'account_code');
			input('text',lang('allocation_amount'),'allocation_amount');
			input('text',lang('cost_centre_asal'),'cost_centre_asal');
			input('text',lang('cost_centre_tujuan'),'cost_centre_tujuan');
			toggle(lang('aktif').'?','is_active');
			form_button(lang('simpan'),lang('batal'));
		form_close();
	modal_footer();
modal_close();
modal_open('modal-import',lang('impor'));
	modal_body();
		form_open(base_url('transaction/jam_transfer/import'),'post','form-import');
			col_init(3,9);
			fileupload('File Excel','fileimport','required','data-accept="xls|xlsx"');
			form_button(lang('impor'),lang('batal'));
		form_close();
modal_close();
?>
