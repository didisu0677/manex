
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
