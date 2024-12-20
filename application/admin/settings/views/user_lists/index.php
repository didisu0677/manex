<div class="content-header">
	<div class="main-container position-relative">
		<div class="header-info">
			<div class="content-title"><?php echo $title; ?></div>
			<?php echo breadcrumb(); ?>
		</div>
		<div class="float-right">
			<?php echo access_button('import,export,delete,active,inactive'); ?>
		</div>
		<div class="clearfix"></div>
	</div>
</div>
<div class="content-body">
	<?php
	table_open('',true,base_url('settings/user_lists/data'),'tbl_user');
		thead();
			tr();
				th('checkbox','text-center','width="30px" data-content="id"');
				th(lang('kode'),'','data-content="kode"');
				th(lang('nama'),'','data-content="nama"');
				th(lang('nama_pengguna'),'','data-content="username"');
				th(lang('email'),'','data-content="email"');
				// th(lang('cabang'),'','data-content="nama_cabang"');
				// th(lang('akses_report'),'','data-content="nama_cabang" data-table ="tbl_m_cabang tbl_struktur"');
				th(lang('hak_akses'),'','width="150" data-content="nama" data-table="tbl_user_group"');
				th(lang('aktif').'?','text-center','width="120px" data-content="is_active" data-type="boolean"');
				th('&nbsp;','','width="30" data-content="action_button"');
	table_close();
	?>
</div>
<?php 
	modal_open('modal-form','','');
		modal_body();
			form_open(base_url('settings/user_lists/save'),'post','form');
				col_init(3,9);
				input('hidden','id','id');
				input('text',lang('kode'),'kode','required|max-length:30|unique');
				input('text',lang('nama'),'nama','required|min-length:4');
				input('text',lang('email'),'email','email|unique');
				input('text',lang('telepon'),'telepon','number|min-length:10');
				select2(lang('hak_akses'),'id_group','required',$group,'id','nama');
				// select2(lang('divisi'),'divisi','',$divisi,'divisi','divisi');
				?>
				<div id ="divisi" class="form-group row">
					<label class="col-sm-3"><?php echo lang('divisi'); ?></label>
					<div class="col-md-9 col-9 ">	
						<select class="form-control select2" name="divisi">
			                <?php foreach($divisi as $d){ ?>
			                <option value="<?php echo $d['divisi']; ?>"><?php echo $d['divisi']; ?></option>
			                <?php } ?>
						</select>   
					</div>
                </div>

				<!-- <div id ="sub_product" class="form-group row">
					<label class="col-sm-3"><?php echo lang('product'); ?></label>
					<div class="col-md-9 col-9 ">	
						<select class="form-control select2" name="sub_product[]" multiple>
			                <?php foreach($opt_sub_product as $d){ ?>
			                <option value="<?php echo $d['product_line']; ?>"><?php echo $d['sub_product']; ?></option>
			                <?php } ?>
						</select>   
					</div>
                </div> -->
				<?php
				echo '<div id ="sub_product" class="form-group">';
					select2(lang('product'),'sub_product[]','',$opt_sub_product,'product_line','sub_product','','multiple');
				echo '</div>';
				input('text',lang('nama_pengguna'),'username','required|min-length:4|unique');
				input('password',lang('kata_sandi'),'password');
				input('password',lang('konfirmasi_kata_sandi'),'konfirmasi','equal:password');
				toggle(lang('aktif').'?','is_active');
				form_button(lang('simpan'),lang('batal'));
			form_close();
		modal_footer();
	modal_close();
	modal_open('modal-import',lang('impor'));
		modal_body();
			form_open(base_url('settings/user_lists/import'),'post','form-import');
				col_init(3,9);
				fileupload('File Excel','fileimport','required','data-accept="xls|xlsx"');
				form_button(lang('impor'),lang('batal'));
			form_close();
	modal_close();
?>
<script type="text/javascript">
var id_unlock = 0;
$(document).on('click','.btn-unlock',function(e){
	e.preventDefault();
	id_unlock = $(this).attr('data-id');
	cConfirm.open(lang.apakah_anda_yakin + '?','lanjut');
});
function lanjut() {
	$.ajax({
		url : base_url + 'settings/user_lists/unlock',
		data : {id:id_unlock},
		type : 'post',
		dataType : 'json',
		success : function(res) {
			cAlert.open(res.message,res.status,'refreshData');
		}
	});
}

$(function () {
	$('#id_group').change(function() {
		var val = $(this).val();
		if(val != 30){
			$('#divisi').hide();
			$('#sub_product').hide();
		}else{
			$('#divisi').show();
			$('#sub_product').show();

		}

	});
});
</script>