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
					<option value="<?php echo $tahun->tahun; ?>" <?php if ($tahun->tahun == user('tahun_budget')) echo ' selected'; ?>><?php echo $tahun->tahun; ?></option>
				<?php } ?>
			</select>

			<label class=""><?php echo lang('item_material'); ?> &nbsp</label>
			<select class="select2 custom-select" style="width: 280px;" id="filter_produk">
				<option value="ALL">ALL</option>
				<?php foreach ($produk_items as $p) { ?>
					<option value="<?php echo $p->component_item; ?>"><?php echo $p->component_item . ' | ' . $p->material_name; ?></option>
				<?php } ?>
			</select>

			<!-- <label class=""><?php echo lang('factory'); ?>  &nbsp</label>
			<select class="select2 infinity custom-select" style="width: 180px;" id="filter_cost_centre">
				<option value="ALL">ALL FACTORY</option>
				<?php //foreach ($cc as $c) { ?>
                <option value="<?php //echo $c->kode; ?>"><?php echo $c->cost_centre; ?></option>
                <?php //} ?>
			</select> -->

			<?php  

			echo '<button class="btn btn-info btn-proses" href="javascript:;" ><i class="fa-process"></i> Running MRP</button>';
            echo '<button class="btn btn-success btn-save" href="javascript:;" > Save <span class="fa-save"></span></button>';

            $arr = [];
                $arr = [
                    // ['btn-save','Save Data','fa-save'],
                    ['btn-export','Export Data','fa-upload'],
                    // ['btn-import','Import Data Begining Stock','fa-download'],
                    // ['btn-template','Template Import','fa-reg-file-alt']
                ];
            echo access_button('',$arr); 
            ?>
	
        </div>
		<div class="clearfix"></div>
	</div>
</div>

<div class="content-body mt-6">
	<div class="main-container mt-2">
		<div class="row">
			<div class="col-sm-12">
				<div class="card">
	    			<!-- <div class="card-body"> -->
	    				<div class="table-responsive tab-pane fade active show height-window">
	    				<?php
						table_open('table table-bordered table-app table-hover table-1');
							thead();
								tr();
									th('PT Otsuka Indonesia ','','width="60" colspan="13" class="text-left"');
								tr();
									th('Supply Chain Department ','','width="60" colspan="13" class="text-left"');
								tr();
									th('Inventory Analysis ','','width="60" colspan="13" class="text-left"');

                                    tr();
									th('Inventory unit analysis','','width="360" rowspan="2" class="text-center align-middle headcol"');
  									for ($i = 1; $i <= 12; $i++) { 
										th(month_lang($i),'','class="text-center" style="min-width:80px"');		
									}
							tbody();
						table_close();
						?>
	    				</div>
	    			<!-- </div> -->
	    		</div>
	    	</div>
	    </div>
	</div>
</div>

<script>
$(document).ready(function() {
	getData();
	$(document).on('keyup', '.xproduksi', function(e) {
		// calculate();
		if (e.keyCode === 13 || e.key === 'Enter' || e.keyCode === 9 || e.key === 'Tab') {
			calculate(); // Panggil fungsi calculate()
		}
	});

});

$('#filter_tahun').change(function() {
	getData()
});
  


$('#filter_produk').change(function() {
	getData()
});

function getData() {
	cLoader.open(lang.memuat_data + '...');
	$('.overlay-wrap').removeClass('hidden');
	var page = base_url + 'material_cost/material_planning/data';
		page 	+= '/'+$('#filter_tahun').val();
		page 	+= '/'+$('#filter_produk').val();

	$.ajax({
		url 	: page,
		data 	: {},
		type	: 'get',
		dataType: 'json',
		success	: function(response) {
			$('.table-1 tbody').html(response.table);
			cLoader.close();
			$('.overlay-wrap').addClass('hidden');	
			// money_init();
		}
	});
}    

var id_proses = '';
var tahun = 0;
$(document).on('click','.btn-proses',function(e){
	e.preventDefault();
	id_proses = 'proses';
	tahun = $('#filter_tahun').val();
	produk = $('#filter_produk').val();
	cConfirm.open(lang.apakah_anda_yakin + '?','lanjut');
});

function lanjut() {
	$.ajax({
		url : base_url + 'material_cost/material_planning/proses',
		data : {id:id_proses,tahun : tahun, produk : produk},
		type : 'post',
		dataType : 'json',
		success : function(res) {
			cAlert.open(res.message,res.status,'refreshData');
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
		url : base_url + 'material_cost/material_planning/save_perubahan',
		data 	: {
			'json' : jsonString,
			verifikasi : i,
			tahun : $('#filter_tahun').val(),
		},
		type : 'post',
		success : function(response) {
			cAlert.open(response,'success','refreshData');
		}
	})
}

	$(document).on('keyup', '.xproduksi', function(e) {
		var wh = e.which;
		if ((48 <= wh && wh <= 57) || (96 <= wh && wh <= 105) || wh == 8) {
			if ($(this).text() == '') {
				$(this).text('');
			} else {
				var n = parseInt($(this).text().replace(/[^0-9\-]/g, ''), 10);
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

	$(document).on('keypress', '.xproduksi', function(e) {
		var wh = e.which;
		if (e.shiftKey) {
			if (wh == 0) return true;
		}
		if (e.metaKey || e.ctrlKey) {
			if (wh == 86 || wh == 118) {
				$(this)[0].onchange = function() {
					$(this)[0].innerHTML = $(this)[0].innerHTML.replace(/[^0-9\-]/g, '');
				}
			}
			return true;
		}
		if (wh == 0 || wh == 8 || wh == 45 || (48 <= wh && wh <= 57) || (96 <= wh && wh <= 105))
			return true;
		return false;
	});

	function calculate() {
	// Objek untuk menyimpan data per kolom
		$('.table-1 tbody tr').each(function() {
			let columnData = {
			B_01: 0,
			B_02: 0,
			B_03: 0,
			B_04: 0,
			B_05: 0,
			B_06: 0,
			B_07: 0,
			B_08: 0,
			B_09: 0,
			B_10: 0,
			B_11: 0,
			B_12: 0
		};

		let columnData1 = {
			prod_01: 0,
			prod_02: 0,
			prod_03: 0,
			prod_04: 0,
			prod_05: 0,
			prod_06: 0,
			prod_07: 0,
			prod_08: 0,
			prod_09: 0,
			prod_10: 0,
			prod_11: 0,
			prod_12: 0
		};

			if ($(this).find('.xproduksi').text() !== '') {
				for (let i = 1; i <= 12; i++) {
					let key = `B_${String(i).padStart(2, '0')}`; // Membuat key seperti B_01, B_02, dst.
					let key1 = `prod_${String(i).padStart(2, '0')}`; 
					let key_begining_stock = `begining_stock_${String(i).padStart(2, '0')}`;
					let key_sales = `sales_${String(i).padStart(2, '0')}`;
					let key_end_stock = `end_stock_${String(i).padStart(2, '0')}`;
					let key_m_cov = `m_cov_${String(i).padStart(2, '0')}`;
					let budget = moneyToNumber($(this).find(`.xproduksi_${String(i).padStart(2, '0')}`).text().replace(/\,/g, ''));
					let value_xproduction = $(this).find(`.xproduksi_${String(i).padStart(2, '0')}`).text().replace(/\,/g, '')
					let nilai = $(this).find(`.xproduksi_${String(i).padStart(2, '0')}`).data('nilai');
					let idx = $(this).find(`.xproduksi_${String(i).padStart(2, '0')}`).data('id');
					let total = budget * nilai ;
					if(value_xproduction != ''){
						columnData[key] += budget * nilai;		
						columnData1[key1] += budget * nilai;	
						$('#'+key1+idx).text(columnData1[key1]);
						let value_sales = $('#'+key_sales+idx).text().replace(/\,/g, '');
						let value_end_stock = $('#'+key_end_stock+idx).text().replace(/\,/g, '');
						let value_begining_stock = $('#'+key_begining_stock+idx).text().replace(/\,/g, '');
						let value_production = columnData1[key1];

						let new_end_stock = parseInt(value_begining_stock) + parseInt(value_production) - parseInt(value_sales)
						let txt_new_end_stock = new_end_stock < 0 ? '-'+(numberFormat(new_end_stock, 0).replace(/[()]/g,'')) : numberFormat(new_end_stock)
						$('#'+key_end_stock+idx).text(txt_new_end_stock);
						let value_total_sales = 0
						let divide_number = 0
						for(let j=0;j<4;j++){
							if(j+i > 12){
								continue;
							}
							let value_sales = parseInt($(`#sales_${String(j+i).padStart(2, '0')}${idx}`).text().replace(/\,/g,''))
							value_total_sales += !isNaN(value_sales) ? value_sales : 0
							divide_number++;
						}

						let coverage = new_end_stock / (value_total_sales / divide_number);
						$('#'+key_m_cov+idx).text(numberFormat(coverage, 2));

						// update begining stock
						for(let j=i; j<=12; j++){
							let value_end_stock = $(`#end_stock_${String(j).padStart(2, '0')}${idx}`).text();
							$(`#begining_stock_${String(j+1).padStart(2, '0')}${idx}`).text(value_end_stock);
						}
					}
				}
			}

			// for (let key1 in columnData1) {
			// 	$(this).find('.'+key1+'').text(columnData1[key1]); // Perbaikan di sini
			// }

		});


		// Menampilkan data per kolom
		

	}
</script>