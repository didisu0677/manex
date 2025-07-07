<div class="content-header">
    <div class="main-container position-relative">
        <div class="header-info">
            <div class="content-title"><?php echo $title; ?></div>
            <?php echo breadcrumb(); ?>
        </div>
        <div class="float-right">
        <!-- <button type="button" class="btn btn-primary btn-sm btn-act-export"><i class="fa-download"></i><?php echo '   ' .lang('export'); ?></button> -->
        <?php

		if($access['access_input']==1) {
			echo '<button class="btn btn-success btn-save" href="javascript:;" ><i class="fa-save"></i> Save</button>';
			echo '<button class="btn btn-danger btn-proses" href="javascript:;" ><i class="fa-process"></i> Recalculate Report</button>';
		}

        $arr = [];
			$arr = [
				// ['btn-save','Save Data','fa-save'],
				['btn-export','Export Data','fa-upload'],
				($access['access_input'] ? ['btn-act-import','Import Data','fa-download']:''),
				// ['btn-act-template','Template Import','fa-reg-file-alt'],
                ($access['access_input'] ? ['btn-act-clear','Clear Data','fa-reg-file-alt'] :'')
			];
		
		
			echo access_button('',$arr); 
            ?>
        </div>
        <div class="clearfix"></div>
    </div>
</div>

<?php
$required ='';
if(in_array(user('id_group'), [BUDGET_PIC_FACTORY,SCM,OPR,QC])) {
	$required = 'required';
}

modal_open('modal-import',lang('impor'));
modal_body();
    form_open(base_url('transaction/breakdown_budget/import'),'post','form-import');
        col_init(3,9);
        input('text',lang('tahun'),'tahun_budget','','','readonly');
        input('text',lang('cost_centre'),'cost_centre',$required,'','readonly');
		input('',lang('user_id'),'username','','','readonly');
		input('text',lang('preparation_pic'),'fullname','','','readonly');
        fileupload('File Excel','fileimport','required','data-accept="xls|xlsx"');
        form_button(lang('impor'),lang('batal'));
    form_close();
modal_close();
?>

<div class="content-body">
    <div class="table-responsive tab-pane fade active show height-window">
    <table id="result" class="table table-bordered table-detail table-hover table-1 mb-0">
            <thead>
                <tr>
                    <th style="background-color: #3F729B; color: white;" rowspan="2" class="text-center align-middle">NO</th>
                    <th style="background-color: #3F729B; color: white;" rowspan="2" class="text-center align-middle">DESCRIPTION</th>
                    <th style="background-color: #3F729B; color: white;" rowspan="" colspan="2" class="text-center align-middle">ACCOUNT </th>
                    <th style="background-color: #3F729B; color: white;" rowspan="" colspan="2" class="text-center align-middle">SUB ACCOUNT</th>
                       <th style="background-color: #3F729B; color: white;" colspan = "12" class="text-center align-middle">SPENDING TIME PLAN</th>
                    <th style="background-color: #3F729B; color: white;" rowspan="2" class="text-center align-middle">TOTAL</th>

                </tr>

                <tr>
                    <th style="background-color: #3F729B; color: white;" rowspan="" class="text-center align-middle">CODE</th>
                    <th style="background-color: #3F729B; color: white;" rowspan="" class="text-center align-middle">NAME</th>
                    <th style="background-color: #3F729B; color: white;" rowspan="" class="text-center align-middle">CODE</th>
                    <th style="background-color: #3F729B; color: white;" rowspan="" class="text-center align-middle">NAME</th>

                    <?PHP 	for ($i = 1; $i <= 12; $i++) { ?>
                        <th style="background-color: #3F729B; color: white;" rowspan="" class="text-center align-middle"><?PHP echo c_upper(month_lang($i)); ?></th>
                    <?PHP } ?>
                 </tr>

 

            </thead>
            <tbody></tbody>

    </table>
            </div>
    <div id="pagination" class="p-2"></div>
</div>
     
<div class="filter-panel">
	<div class="filter-body">
		<?php
			form_open('','','form-filter');
				col_init(12,12);
                

                ?>
                <label class=""><?php echo lang('tahun'); ?>  &nbsp</label>
                <select class="select2 infinity custom-select select" name ="tahun" id ="tahun">
                    <?php foreach ($tahun as $tahun) { ?>
                    <option value="<?php echo $tahun->tahun; ?>"<?php if($tahun->tahun == user('tahun_budget')) echo ' selected'; ?>><?php echo $tahun->tahun; ?></option>
                    <?php } ?>
                </select>

                <?php
                select2(lang('cost_centre'),'filter_cost_centre','',$cc,'kode','cost_centre');
				select2(lang('account_code'),'filter_account','',$account,'account_code','account_name');

				select2(lang('user_name'),'filter_username');



            form_close();
       ?>

</div>
    


<script type="text/javascript">
var xhr_ajax = null;
$(document).ready(function(){
    $('#status').val('').trigger('change');
	getUser();
    loadData();
});
$('#pagination').on('click','a',function(e){
    e.preventDefault();
    var pageNum = $(this).attr('data-ci-pagination-page');
    loadData(pageNum);
});
function loadData(pageNum){
    if(typeof pageNum == 'undefined') {
        pageNum = 1;
    }
    if( xhr_ajax != null ) {
        xhr_ajax.abort();
        xhr_ajax = null;
    }
    xhr_ajax = $.ajax({
        url: base_url + 'transaction/breakdown_budget/data/'+pageNum,
        type: 'post',
		data : $('#form-filter').serialize(),
        dataType: 'json',
        success: function(res){
        	xhr_ajax = null;
            if(res.pagination) {
                $('#pagination').html(res.pagination);
            } else {
                $('#pagination').html('');
            }
            $('#result tbody').html(res.data);
            gridTable();
        }
    });
}


$('#form-filter input').keyup(function(){
    loadData();
});

$('#form-filter select').change(function() {
    loadData();
});

$('#filter_cost_centre').change(function(){
	getUser();
});

$(document).on('click','.btn-act-export',function(e){
		// alert('x');die;
		e.preventDefault();
		$.redirect(base_url + 'transaction/breakdown_budget/export/', 
            {tahun:$('#tahun').val(),
            status:$('#filter_cost_centre').val(),
            } , 'get');
	});


$('.btn-act-import').click(function(){
	$('#form-import')[0].reset();

	$('#tahun_budget').val($('#tahun').val())
    $('#cost_centre').val($('#filter_cost_centre').val())
	$('#username').val($('#filter_username').val())

	var selectedOption = $('#filter_username').find(':selected');
    
    var namaValue = selectedOption.data('nama');
    $('#fullname').val(namaValue);
});


$(document).on('focus','.edit-value',function(){
	$(this).parent().removeClass('edited');
});
$(document).on('blur','.edit-value',function(){
	var tr = $(this).closest('tr');
	if($(this).text() != $(this).attr('data-value')) {
		$(this).addClass('edited');
	}
	if(tr.find('td.edited').length > 0) {
		tr.addClass('edited-row');
	} else {
		tr.removeClass('edited-row');
	}
});
$(document).on('keyup','.edit-value',function(e){
	var wh 			= e.which;
	if((48 <= wh && wh <= 57) || (96 <= wh && wh <= 105) || wh == 8) {
		if($(this).text() == '') {
			$(this).text('');
		} else {
			var n = parseInt($(this).text().replace(/[^0-9\-]/g,''),10);
		    $(this).text(n.toLocaleString());
		    var selection = window.getSelection();
			var range = document.createRange();
			selection.removeAllRanges();
			range.selectNodeContents($(this)[0]);
			range.collapse(false);
			selection.addRange(range);
			$(this)[0].focus();
		}
	}
});
$(document).on('keypress','.edit-value',function(e){
	var wh 			= e.which;
	if (e.shiftKey) {
		if(wh == 0) return true;
	}
	if(e.metaKey || e.ctrlKey) {
		if(wh == 86 || wh == 118) {
			$(this)[0].onchange = function(){
				$(this)[0].innerHTML = $(this)[0].innerHTML.replace(/[^0-9\-]/g, '');
			}
		}
		return true;
	}
	if(wh == 0 || wh == 8 || wh == 45 || (48 <= wh && wh <= 57) || (96 <= wh && wh <= 105)) 
		return true;
	return false;
});

function calculate() {

	$('#result .table-1 tbody tr').each(function(){
		if($(this).find('.budget').text() != '') {
	
			let B_01 = moneyToNumber($(this).find('.B_01').text().replace(/\,/g,''))
			let B_02 = moneyToNumber($(this).find('.B_02').text().replace(/\,/g,''))
			let B_03 = moneyToNumber($(this).find('.B_03').text().replace(/\,/g,''))
			let B_04 = moneyToNumber($(this).find('.B_04').text().replace(/\,/g,''))
			let B_05 = moneyToNumber($(this).find('.B_05').text().replace(/\,/g,''))
			let B_06 = moneyToNumber($(this).find('.B_06').text().replace(/\,/g,''))
			let B_07 = moneyToNumber($(this).find('.B_07').text().replace(/\,/g,''))
			let B_08 = moneyToNumber($(this).find('.B_08').text().replace(/\,/g,''))
			let B_09 = moneyToNumber($(this).find('.B_09').text().replace(/\,/g,''))
			let B_10 = moneyToNumber($(this).find('.B_10').text().replace(/\,/g,''))
			let B_11 = moneyToNumber($(this).find('.B_11').text().replace(/\,/g,''))
			let B_12 = moneyToNumber($(this).find('.B_12').text().replace(/\,/g,''))

	
			let total_budget = 0
	
			total_budget = B_01+B_02+B_03+B_04+B_05+B_06+B_07+B_08+B_09+B_10+B_11+B_12
			

			$(this).find('.total_budget').text(customFormat(total_budget))
		}
	});
}

$(document).on('click','.btn-save',function(){
	var i = 0;
	$('.edited').each(function(){
		i++;
	});
	if(i == 0) {
		cAlert.open('tidak ada data yang di ubah');
	} else {
		var msg 	= lang.anda_yakin_menyetujui;
		if( i == 0) msg = lang.anda_yakin_menolak;
		cConfirm.open(msg,'save_perubahan');        
	}

});

function save_perubahan() {
	var data_edit = {};
	var i = 0;
	
	$('.edited').each(function(){
		var content = $(this).children('div');
		if(typeof data_edit[$(this).attr('data-id')] == 'undefined') {
			data_edit[$(this).attr('data-id')] = {};
		}
		data_edit[$(this).attr('data-id')][$(this).attr('data-name')] = $(this).text().replace(/[^0-9\-]/g,'');
		i++;
	});
	
	var jsonString = JSON.stringify(data_edit);		
	$.ajax({
		url : base_url + 'transaction/breakdown_budget/save_perubahan',
		data 	: {
			'json' : jsonString,
			verifikasi : i,
			tahun : $('#tahun').val(),
		},
		type : 'post',
		success : function(response) {
			cAlert.open(response,'success','refreshData');
		}
	})
}

	var id_proses = '';
	var tahun = 0;
	$(document).on('click','.btn-act-clear',function(e){
		e.preventDefault();
		id_proses = 'proses';
		tahun = $('#tahun').val();
		cost_centre = $('#filter_cost_centre').val();
		username = $('#filter_username').val();
		cConfirm.open(lang.apakah_anda_yakin + '?','lanjut_clear');
	});

	function lanjut_clear() {
		$.ajax({
			url : base_url + 'transaction/breakdown_budget/clear_data',
			data : {id:id_proses,tahun : tahun, cost_centre : cost_centre, username : username},
			type : 'post',
			dataType : 'json',
			success : function(res) {
				cAlert.open(res.message,res.status,'refreshData');
			}
		});
	}

function getUser() {
	console.log('ok');
	$('#filter_username').html('');
	$.ajax({
			url : base_url + 'transaction/breakdown_budget/get_user',
			data : {cost_centre : $('#filter_cost_centre').val(), tahun : $('#tahun').val()},
			type : 'post',
			dataType : 'json',
			success : function(response) {
				// var konten = '<option value="">Semua user</option>';
				if($('#group_user').val()== 1 || $('#group_user').val()== 2){
					var konten = '<option value="" data-nama="ALL"></option>';
				}else{
					var konten = '';
				}	

				$.each(response,function(k,v){
					konten += '<option value="'+v.id+'" data-nama="'+v.nama+'">'+v.nama+'</option>';
				});
				$('#filter_username').html(konten);
				$('#filter_username').trigger('change');
			}
	});
}


$(document).on('click','.btn-export',function(){
	var currentdate = new Date(); 
	var datetime = currentdate.getDate() + "/"
	                + (currentdate.getMonth()+1)  + "/" 
	                + currentdate.getFullYear() + " @ "  
	                + currentdate.getHours() + ":"  
	                + currentdate.getMinutes() + ":" 
	                + currentdate.getSeconds();
	
	$('.bg-grey-2').each(function(){
		$(this).attr('bgcolor','#f4f4f4');
	});
	$('.bg-grey-2').each(function(){
		$(this).attr('bgcolor','#dddddd');
	});
	$('.bg-grey-2-1').each(function(){
		$(this).attr('bgcolor','#b4b4b4');
	});
	$('.bg-grey-2-2').each(function(){
		$(this).attr('bgcolor','#aaaaaa');
	});
	$('.bg-grey-2').each(function(){
		$(this).attr('bgcolor','#888888');
	});
	var table	= '<table>';
	table += '<tr><td colspan="1">PT Otsuka Indonesia</td></tr>';
	table += '<tr><td colspan="1"> Breakdown Budget </td><td colspan="25">: '+$('#filter_tahun option:selected').text()+'</td></tr>';
	table += '<tr><td colspan="1"> Cost centre </td><td colspan="25">: '+$('#filter_cost_centre option:selected').text()+'</td></tr>';
	table += '<tr><td colspan="1"> Print date </td><td colspan="25">: '+datetime+'</td></tr>';
	table += '</table><br />';
	table += '<table border="1">';
	table += $('.content-body').html();
	table += '</table>';
	var target = table;
	window.open('data:application/vnd.ms-excel,' + encodeURIComponent(target));
	$('.bg-grey-1,.bg-grey-2.bg-grey-2-1,.bg-grey-2-2,.bg-grey-3').each(function(){
		$(this).removeAttr('bgcolor');
	});
});

var id_proses = '';
var tahun = 0;
$(document).on('click','.btn-proses',function(e){
	e.preventDefault();
	id_proses = 'proses';
	tahun = $('#tahun').val();
	cost_centre = $('#filter_cost_centre').val();
	cConfirm.open(lang.apakah_anda_yakin + '?','lanjut');
});

function lanjut() {
	$.ajax({
		url : base_url + 'transaction/breakdown_budget/sum_budget_acaount/'+tahun+'/'+cost_centre,
		data : {id:id_proses,tahun : tahun, cost_centre : cost_centre},
		type : 'post',
		dataType : 'json',
		success : function(res) {
			cAlert.open(res.message,res.status,'refreshData');
		}
	});
}
</script>