<div class="content-header page-data" data-additional="<?= $access_additional ?>">
	<div class="main-container position-relative">
		<div class="header-info">
			<div class="content-title"><?php echo $title; ?></div>
			<?php echo breadcrumb(); ?>
		</div>
		
		<div class="float-right">
			<label class=""><?php echo lang('tahun'); ?>  &nbsp</label>
			<select class="select2 infinity custom-select" style="width: 80px;" id="filter_tahun">
				<?php foreach ($tahun as $tahun) { ?>
                <option value="<?php echo $tahun->tahun; ?>"<?php if($tahun->tahun == user('tahun_budget')) echo ' selected'; ?>><?php echo $tahun->tahun; ?></option>
                <?php } ?>
			</select>

			<label class=""><?php echo lang('cc'); ?>  &nbsp</label>
			<select class="select2 infinity custom-select" style="width: 180px;" id="filter_cost_centre">
				<option value="ALL">ALL</option>
				<?php foreach ($cc as $c) { ?>
                <option value="<?php echo $c->kode; ?>"><?php echo $c->cost_centre; ?></option>
                <?php } ?>
			</select>

			<!-- <label class=""><?php echo lang('sa'); ?>  &nbsp</label>
			<select class="select2 infinity custom-select" style="width: 180px;" id="filter_sub_account">
			</select> -->

			<!-- <label class=""><?php echo lang('user_name'); ?>  &nbsp</label>
			<select class="select2 infinity custom-select" style="width: 150px;" id="filter_username">
			</select> -->

			<!-- <label class=""><?php echo lang('product'); ?>  &nbsp</label>
			<select class="select2 infinity custom-select" style="width: 130px;" id="filter_product">
			</select> -->
			<!-- <select class="select2 infinity custom-select" style="width: 180px;" id="filter_allocated">
				<option value="0"><?php echo lang('not_allocated') ; ?></option>
				<option value="1"><?php echo lang('allocated') ?></option>
			</select> -->

    		
    		<?php 

			if($access['access_input'])
			echo '<button class="btn btn-success btn-save" href="javascript:;" ><i class="fa-save"></i> Save</button>';

			$arr = [];
			$arr = [
				// ['btn-save','Save Data','fa-save'],
				['btn-export','Export Data','fa-upload'],
				($access['access_input'] ? ['btn-act-import','Import Data','fa-download']:''),
				// ['btn-act-template','Template Import','fa-reg-file-alt']
			];
		
		
			echo access_button('',$arr); 

			?>
    		</div>
			<div class="clearfix"></div>
			
		</div>
	</div>

<div class="content-body table-freeze-fullwidth">
	<div class="card">
		<div class="card-header pl-3 pr-3">
			<ul class="nav nav-pills card-header-pills">
				<li class="nav-item">
					<a class="nav-link active" id="overall-tab" data-toggle="pill" href="#overall" role="tab" aria-controls="overall" aria-selected="true">Actual & Estimate <?php echo user('tahun_budget') - 1 ; ?></a>
				</li>
				<li class="nav-item">
					<a class="nav-link" id="budget-tab" data-toggle="pill" href="#budget" role="tab" aria-controls="budget" aria-selected="false">Monthly Budget <?php echo user('tahun_budget') ; ?></a>
				</li>
			</ul>
		</div>

		<div class="card-body tab-content" id="pills-tabContent">
			<div class="tab-pane fade show active" id="overall" role="tabpanel" aria-labelledby="overall-tab">
				<div class="table-responsive" id="result">
							<?php
							table_open('table table-bordered table-app table-hover table-1');
								thead();
									tr();
									th(lang('account'),'','class="text-center align-middle headcol" style="min-width:250px"');
									for ($i = 1; $i <= 12; $i++) {
										$actual = "";
										if($i <= setting('actual_budget')) {
											$actual = "ACT";
										}else{
											$actual = "EST";
										}
			
										th($actual . ' ' . month_lang($i), '', 'class="text-center align-middle" style="min-width:60px"');
									}
									th(lang('total'),'','class="text-center align-middle headcol"style="min-width:60px"');
								tbody();
								table_close();
								?>
				</div>
			</div>

			<div class="tab-pane fade" id="budget" role="tabpanel" aria-labelledby="budget-tab">
				<div class="table-responsive" id="result2">
							<?php
							table_open('table table-bordered table-app table-hover table-2');
								thead();
									tr();
									th(lang('account'),'','class="text-center align-middle headcol" style="min-width:250px"');
									for ($i = 1; $i <= 12; $i++) {
			
										th(month_lang($i), '', 'class="text-center align-middle" style="min-width:60px"');
									}
									th(lang('total'),'','class="text-center align-middle headcol" style="min-width:60px"');
									th(lang('total_le'),'','class="text-center align-middle headcol" style="min-width:60px"');
									th(lang('increase'),'','class="text-center align-middle headcol" style="min-width:40px"');
								tbody();
								table_close();
								?>
				</div>
			</div>
		</div>
	</div>
</div>
<?php
modal_open('modal-import',lang('impor'));
	modal_body();
		form_open(base_url('reporting/budget_by_deptnew/import'),'post','form-import');
			col_init(3,9);
			input('text',lang('tahun'),'tahun','','','readonly');
			input('text',lang('cost_centre'),'cost_centre','','','readonly');
			
			input('text',lang('tab'),'tab','','','readonly');
			input('text',lang('import_data'),'judul','','','readonly');

			fileupload('File Excel','fileimport','required','data-accept="xls|xlsx"');
			form_button(lang('impor'),lang('batal'));
		form_close();
modal_close();
?>
<script type="text/javascript">

$(document).ready(function () {
	getData();

    $('#filter_cost_centre').trigger('change')
	$(document).on('keyup', '.budget', function (e) {
    	calculate();
    });

    // Initialize Bootstrap tabs properly
    $('a[data-toggle="pill"]').tab();
    
    // Re-adjust when tab is switched
    $('a[data-toggle="pill"]').on('shown.bs.tab', function (e) {
        setTimeout(function(){
            adjustStickyHeader();
        }, 100);
        
        // Force another adjustment after a bit longer delay
        setTimeout(function(){
            adjustStickyHeader();
        }, 300);
    });
});	

$('#filter_tahun').change(function(){
	getData();
});

$('#filter_cost_centre').change(function(){
	getData();
});

$('#filter_allocated').change(function(){
	getData();
});

function getData() {

		cLoader.open(lang.memuat_data + '...');
		// $('.overlay-wrap').removeClass('hidden');
		var page = base_url + 'reporting/budget_by_deptnew/data';
			page 	+= '/'+$('#filter_tahun').val();
			page 	+= '/'+$('#filter_cost_centre').val();
			// page    += '/'+$('#filter_allocated').val();

		$.ajax({
			url 	: page,
			data 	: {},
			type	: 'get',
			dataType: 'json',
			success	: function(response) {
				$('.table-1 tbody').html(response.table);
				$('.table-2 tbody').html(response.table2);
				cLoader.close();

				// Ensure sticky header is properly positioned after data load
				setTimeout(function(){
					adjustStickyHeader();
				}, 100);

			// $('.overlay-wrap').addClass('hidden');	
			}
		});
}


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



// Function to adjust sticky header position for both tables
function adjustStickyHeader() {
    // Calculate the tab header height
    var tabHeaderHeight = $('.card-header').outerHeight() || 60;
    
    // Only apply if this page has tabs (card-header exists)
    if($('.content-body.table-freeze-fullwidth .card-header').length > 0) {
        // Ensure tab header is at top
        $('.content-body.table-freeze-fullwidth .card-header').css({
            'position': 'sticky',
            'top': '0px',
            'z-index': '1001',
            'background-color': '#fff',
            'box-shadow': '0 2px 4px rgba(0, 0, 0, 0.1)'
        });
        
        // Adjust the top position for sticky table header (below tab header) - using same color as CSS
        $('.content-body.table-freeze-fullwidth .table-1 thead th, .content-body.table-freeze-fullwidth .table-2 thead th').css({
            'position': 'sticky',
            'top': tabHeaderHeight + 'px',
            'z-index': '1000',
            'background-color': '#495057',
            'color': '#ffffff',
            'font-weight': 'bold',
            'font-size': '12px',
            'padding': '8px 6px'
        });
        
        // Special handling for headcol class - using same color as CSS
        $('.content-body.table-freeze-fullwidth .headcol').css({
            'position': 'sticky',
            'top': tabHeaderHeight + 'px',
            'z-index': '1000',
            'background-color': '#495057',
            'color': '#ffffff',
            'font-weight': 'bold',
            'font-size': '12px',
            'padding': '8px 6px'
        });
    }

    

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
		url : base_url + 'reporting/budget_by_deptnew/save_perubahan',
		data 	: {
			'json' : jsonString,
			verifikasi : i
		},
		type : 'post',
		success : function(response) {
			cAlert.open(response,'success','refreshData');
		}
	})
}

let activeTable = '#result';
	let judul = 'Actual and Estimate' 

	$('a[data-toggle="pill"]').on('shown.bs.tab', function (e) {
        var activeTab = $(e.target).attr('href'); // Get the current active tab href attribute
        if(activeTab == '#overall'){
			activeTable = '#result'
			judul = 'Actual and Estimate'
		}else if(activeTab == '#budget'){
			activeTable = '#result2'
			judul = "Budget by Month"
		}else if(activeTab == '#detail'){
			activeTable = '#result3'
			judul = 'Yearly Budget'
		}
		
		// Adjust sticky header after tab switch
		setTimeout(function(){
			adjustStickyHeader();
		}, 100);
		
		// Double check after longer delay
		setTimeout(function(){
			adjustStickyHeader();
		}, 300);
    });

$('.btn-act-import').click(function(){
		$("#modal-import").modal()
		$('#form-import')[0].reset();
		$('#tahun').val($('#filter_tahun').val())
		$('#cost_centre').val($('#filter_cost_centre').val())
		$('#tab').val(activeTable)
		$('#judul').val(judul)
});


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
	table += '<tr><td colspan="1"> Budget ALL Department </td><td colspan="25">: '+$('#filter_tahun option:selected').text()+'</td></tr>';
	table += '<tr><td colspan="1"> Cost centre </td><td colspan="25">: '+$('#filter_cost_centre option:selected').text()+'</td></tr>';
	table += '<tr><td colspan="1"> Print date </td><td colspan="25">: '+datetime+'</td></tr>';
	table += '</table><br />';
	table += '<table border="1">';
	table += '</table>';

	table += $(activeTable).html();
	
	var target = table;
	window.open('data:application/vnd.ms-excel,' + encodeURIComponent(target));
	$('.bg-grey-1,.bg-grey-2.bg-grey-2-1,.bg-grey-2-2,.bg-grey-3').each(function(){
		$(this).removeAttr('bgcolor');
	});
});

</script>