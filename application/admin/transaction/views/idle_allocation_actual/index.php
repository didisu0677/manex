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
                <option value="<?php echo $tahun->tahun; ?>"<?php if($tahun->tahun == user('tahun_budget') - 1) echo ' selected'; ?>><?php echo $tahun->tahun; ?></option>
                <?php } ?>
			</select>

			<?php 
			$import = '';
			$delete = '';
			if($access['access_input']==1) {
				// echo '<button class="btn btn-info btn-proses" href="javascript:;" ><i class="fa-process"></i> Idle Allocation</button>';
				$import = 'import';
				$delete = 'delete';
			}
			
			echo access_button('delete,active,inactive,export,import'); ?>
		</div>
		<div class="clearfix"></div>
	</div>
</div>
<div class="content-body">
	<?php
	table_open('',true,base_url('transaction/idle_allocation_actual/data'),'tbl_idle_allocation_actual');
		thead();
			tr();
				th('checkbox','text-center','width="30" data-content="id"');
				th(lang('tahun'),'','data-content="tahun"');
				th(lang('bulan'),'','data-content="bulan"');
				th(lang('cost_center'),'','data-content="cost_centre" data-table="tbl_fact_cost_centre tbl_cost_centre"');
				th(lang('prsn_allocation'),'','data-content="prsn_allocation" data-type="daterange"');
				th(lang('aktif').'?','text-center','data-content="is_active" data-type="boolean"');
				th('&nbsp;','','width="30" data-content="action_button"');
	table_close();
	?>
</div>
<?php 
modal_open('modal-form','','','data-openCallback="formOpen"');
	modal_body();
		form_open(base_url('transaction/idle_allocation_actual/save'),'post','form');
			col_init(3,9);
			input('hidden','id','id');
			input('text',lang('tahun'),'tahun','required','','readonly');
			// Create months array using HTML select element
			$current_month = date('m');
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

			select2(lang('cost_centre'),'id_cost_centre','',$opt_cc,'id','cost_centre');
			input('money2',lang('prsn_allocation'),'prsn_allocation');
			toggle(lang('aktif').'?','is_active');
			form_button(lang('simpan'),lang('batal'));
		form_close();
	modal_footer();
modal_close();
modal_open('modal-import',lang('impor'));
	modal_body();
		form_open(base_url('transaction/idle_allocation_actual/import'),'post','form-import');
			col_init(3,9);
			fileupload('File Excel','fileimport','required','data-accept="xls|xlsx"');
			form_button(lang('impor'),lang('batal'));
		form_close();
modal_close();
?>

<script>
	$(document).ready(function() {
	var url = base_url + 'transaction/idle_allocation_actual/data/' ;
		url 	+= '/'+$('#filter_tahun').val(); 
	$('[data-serverside]').attr('data-serverside',url);
	refreshData();
});	

function formOpen() {
	is_edit = true;
	var response = response_edit;
	$('#tahun').val($('#filter_tahun').val())
	if(typeof response.id != 'undefined') {
	} 
	is_edit = false;
}


$('#filter_produk').change(function(){
	var url = base_url + 'transaction/idle_allocation_actual/data/' ;
		url 	+= '/'+$('#filter_tahun').val(); 
	$('[data-serverside]').attr('data-serverside',url);
	refreshData();
});

$('#filter_tahun').change(function(){
	var url = base_url + 'transaction/idle_allocation_actual/data/' ;
		url 	+= '/'+$('#filter_tahun').val(); 
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
		url : base_url + 'transaction/idle_allocation_actual/proses',
		data : {id:id_proses,tahun : tahun},
		type : 'post',
		dataType : 'json',
		success : function(res) {
			cAlert.open(res.message,res.status,'refreshData');
		}
	});
}

$(document).on('click','.btn-act-template',function(e){
	// alert('x');die;
	e.preventDefault();
	$.redirect(base_url + 'transaction/idle_allocation_actual/template/');
});

</script>