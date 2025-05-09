
$(document).ready(function() {
	var url = base_url + 'transaction/actual_manex/data/' ;
		url 	+= '/'+$('#filter_tahun').val() 
		url 	+= '/'+$('#bulan').val() 
		url 	+= '/'+$('#filter_estimate').val() 
	$('[data-serverside]').attr('data-serverside',url);
	refreshData();
});	

$('#filter_tahun').change(function(){
	var url = base_url + 'transaction/actual_manex/data/' ;
		url 	+= '/'+$('#filter_tahun').val() 
		url 	+= '/'+$('#bulan').val() 
		url 	+= '/'+$('#filter_estimate').val() 
	$('[data-serverside]').attr('data-serverside',url);
	
	refreshData();
});

$('#bulan').change(function(){
	var url = base_url + 'transaction/actual_manex/data/' ;
		url 	+= '/'+$('#filter_tahun').val() 
		url 	+= '/'+$('#bulan').val() 
		url 	+= '/'+$('#filter_estimate').val() 
	$('[data-serverside]').attr('data-serverside',url);
	
	refreshData();
});

$('#filter_estimate').change(function(){
	var url = base_url + 'transaction/actual_manex/data/' ;
		url 	+= '/'+$('#filter_tahun').val() 
		url 	+= '/'+$('#bulan').val() 
		url 	+= '/'+$('#filter_estimate').val() 
	$('[data-serverside]').attr('data-serverside',url);
	
	refreshData();
});

$('.btn-act-import').click(function(){
	$('#form-import')[0].reset();
	$('#tahun_import').val($('#filter_tahun').val());
	$('#bulan_import').val($('#bulan').val());
	$('#filter_import').val($('#filter_estimate').val()).trigger('change')
});


var id_proses = '';
	var tahun = 0;
	$(document).on('click','.btn-proses',function(e){
		e.preventDefault();
		id_proses = 'proses';
		tahun = $('#filter_tahun').val();
		bulan = $('#bulan').val();
		is_estimate = $('#filter_estimate').val();
		cConfirm.open(lang.apakah_anda_yakin + '?','lanjut');
	});

	function lanjut() {
		$.ajax({
			url : base_url + 'transaction/actual_manex/proses',
			data : {id:id_proses,tahun : tahun, bulan : bulan, is_estimate : is_estimate},
			type : 'post',
			dataType : 'json',
			success : function(res) {
				cAlert.open(res.message,res.status,'refreshData');
			}
		});
	}
