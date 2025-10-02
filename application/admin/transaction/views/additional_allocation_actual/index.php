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
					<option value="<?php echo $tahun->tahun; ?>" <?php if ($tahun->tahun == user('tahun_budget') - 1) echo ' selected'; ?>><?php echo $tahun->tahun; ?></option>
				<?php } ?>
			</select>

			<?php echo access_button('delete,active,inactive,export,import'); ?>
		</div>
		<div class="clearfix"></div>
	</div>
</div>
<div class="content-body">
	<?php
	table_open('',true,base_url('transaction/Additional_allocation_actual/data'),'tbl_add_alloc_product_actual');
		thead();
			tr();
				th('checkbox','text-center','width="30" data-content="id"');
				th(lang('tahun'),'','data-content="tahun"');
				th(lang('bulan'),'','data-content="bulan"');
				th(lang('product_code'),'','data-content="product_code"');
				th(lang('account_code'),'','data-content="account_code"');
				th(lang('jumlah'),'','data-content="jumlah_asal" data-type="currency"');
				th(lang('jumlah_penyesuaian'),'','data-content="jumlah_penyesuaian" data-type="currency"');
				th(lang('jumlah_allocation'),'','data-content="jumlah_allocation" data-type="currency"');
				th(lang('alloc_cc_product'),'','data-content="alloc_cc_product"');
				th(lang('aktif').'?','text-center','data-content="is_active" data-type="boolean"');
				th('&nbsp;','','width="30" data-content="action_button"');
	table_close();
	?>
</div>
<?php 
modal_open('modal-form');
	modal_body();
		form_open(base_url('transaction/Additional_allocation_actual/save'),'post','form');
			col_init(3,9);
			input('hidden','id','id');
			?>

			<div class="form-group row">
				<label class="col-form-label col-sm-3" for="tahun_ajaran"><?php echo lang('tahun'); ?></label>
				<div class="col-sm-9">
					<select class="select2 infinity custom-select" id="tahun" name ="tahun">
						<?php for($i = (user('tahun_budget')); $i >= (user('tahun_budget') - 2); $i--){ ?>
		                <option value="<?php echo $i; ?>"<?php if($i == user('tahun_budget')) echo ' selected'; ?>><?php echo $i; ?></option>
		                <?php } ?>
					</select>
				</div>
			</div>	
			<?php
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
			select2(lang('product_code'),'product_code','required',$product,'code','product_name');
			select2(lang('account_code'),'account_code','required',$account,'account_code','account_name');
			input('money',lang('jumlah'),'jumlah_asal');
			input('money',lang('jumlah_penyesuaian'),'jumlah_penyesuaian');
			input('money',lang('jumlah_allocation'),'jumlah_allocation');
			select2(lang('alloc_cc_product'),'alloc_cc_product','required',$cc,'kode','cost_centre');

			// textarea(lang('product_detail'),'product_detail');
			toggle(lang('aktif').'?','is_active');
			form_button(lang('simpan'),lang('batal'));
			?>
			<div class ="item form form-group">
				<div class="row">
					<div class="col-md-12">
					<button id="btn-alocation" type="button" class="btn btn-block btn-secondary btn-xs btn-proses"><?php echo 'Click for Allocation Process'; ?></button>
					</div>
				</div>
			</div>
			<?php
		form_close();
	modal_footer();
modal_close();
modal_open('modal-import',lang('impor'));
	modal_body();
		form_open(base_url('transaction/Additional_allocation_actual/import'),'post','form-import');
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
	id_allocation = $('#id').val();
	cConfirm.open(lang.apakah_anda_yakin + '?','lanjut');
});

function lanjut() {
	$.ajax({
		url : base_url + 'transaction/Additional_allocation_actual/proses',
		data : {id:id_proses,tahun : tahun, id_allocation : id_allocation},
		type : 'post',
		dataType : 'json',
		success : function(res) {
			cAlert.open(res.message,res.status,'refreshData');
		}
	});
}

function detail_callback(id){
	$.get(base_url+'transaction/Additional_allocation_actual/detail/'+id,function(result){
		cInfo.open(lang.detil,result);
	});
}
</script>