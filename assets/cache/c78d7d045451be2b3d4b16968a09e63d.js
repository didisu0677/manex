
	$(document).ready(function() {
		// getData();

		$('#filter_cost_centre').trigger('change')
		$(document).on('keyup', '.budget', function(e) {
			calculate();
		});

	});

	$('#filter_cost_centre').change(function() {
		getSubaccount();
		// getProduk();
		getUser();
		// $('#filter_product').trigger('change')
	});

	$('#filter_sub_account').change(function() {
		getData();
	});
	
	function getSubaccount() {
		console.log('ok');
		$('#filter_sub_account').html('');
		$.ajax({
			url: base_url + 'transaction/budget_detail/get_subaccount',
			data: {
				cost_centre: $('#filter_cost_centre').val()
			},
			type: 'post',
			dataType: 'json',
			success: function(response) {
				var konten = '';
				$.each(response, function(k, v) {
					konten += '<option value="' + v.kode + '">' + v.sub_account + '</option>';
				});
				$('#filter_sub_account').html(konten);
				$('#filter_sub_account').trigger('change');
			}
		});
	}

	function getProduk() {
		console.log('ok');
		$('#filter_product').html('');
		$.ajax({
			url: base_url + 'transaction/budget_detail/get_produk',
			data: {
				cost_centre: $('#filter_cost_centre').val()
			},
			type: 'post',
			dataType: 'json',
			success: function(response) {
				var konten = '';
				$.each(response, function(k, v) {
					konten += '<option value="' + v.code + '">' + v.product_name + '</option>';
				});
				$('#filter_product').html(konten);
				$('#filter_product').trigger('change');
			}
		});
	}

	function getUser() {
		console.log('ok');
		$('#filter_username').html('');
		$.ajax({
			url: base_url + 'transaction/budget_detail/get_user',
			data: {
				cost_centre: $('#filter_cost_centre').val(),
				tahun: $('#filter_tahun').val()
			},
			type: 'post',
			dataType: 'json',
			success: function(response) {
				var konten = '';
				$.each(response, function(k, v) {
					konten += '<option value="' + v.id + '">' + v.nama + '</option>';
				});
				$('#filter_username').html(konten);
				$('#filter_username').trigger('change');
			}
		});
	}

	function getData() {

		cLoader.open(lang.memuat_data + '...');
		// $('.overlay-wrap').removeClass('hidden');
		var page = base_url + 'budget_sales/data';
		page += '/' + $('#filter_tahun').val();
		page += '/' + $('#filter_cost_centre').val();
		page += '/' + $('#filter_sub_account').val();
		// page 	+= '/'+$('#filter_username').val();
		// page 	+= '/'+$('#filter_product').val();

		$.ajax({
			url: page,
			data: {},
			type: 'get',
			dataType: 'json',
			success: function(response) {
				$('.table-1 tbody').html(response.table);
				cLoader.close();

				// $('.overlay-wrap').addClass('hidden');	
			}
		});
	}


	// $(document).on('focus', '.edit-value', function() {
	// 	$(this).parent().removeClass('edited');
	// });
	// $(document).on('blur', '.edit-value', function() {
	// 	var tr = $(this).closest('tr');
	// 	if ($(this).text() != $(this).attr('data-value')) {
	// 		$(this).addClass('edited');
	// 	}
	// 	if (tr.find('td.edited').length > 0) {
	// 		tr.addClass('edited-row');
	// 	} else {
	// 		tr.removeClass('edited-row');
	// 	}
	// });
	// $(document).on('keyup', '.edit-value', function(e) {
	// 	var wh = e.which;
	// 	if ((48 <= wh && wh <= 57) || (96 <= wh && wh <= 105) || wh == 8) {
	// 		if ($(this).text() == '') {
	// 			$(this).text('');
	// 		} else {
	// 			var n = parseInt($(this).text().replace(/[^0-9\-]/g, ''), 10);
	// 			$(this).text(n.toLocaleString());
	// 			var selection = window.getSelection();
	// 			var range = document.createRange();
	// 			selection.removeAllRanges();
	// 			range.selectNodeContents($(this)[0]);
	// 			range.collapse(false);
	// 			selection.addRange(range);
	// 			$(this)[0].focus();
	// 		}
	// 	}
	// });
	// $(document).on('keypress', '.edit-value', function(e) {
	// 	var wh = e.which;
	// 	if (e.shiftKey) {
	// 		if (wh == 0) return true;
	// 	}
	// 	if (e.metaKey || e.ctrlKey) {
	// 		if (wh == 86 || wh == 118) {
	// 			$(this)[0].onchange = function() {
	// 				$(this)[0].innerHTML = $(this)[0].innerHTML.replace(/[^0-9\-]/g, '');
	// 			}
	// 		}
	// 		return true;
	// 	}
	// 	if (wh == 0 || wh == 8 || wh == 45 || (48 <= wh && wh <= 57) || (96 <= wh && wh <= 105))
	// 		return true;
	// 	return false;
	// });

	// function calculate() {

	// 	$('#result .table-1 tbody tr').each(function() {
	// 		if ($(this).find('.budget').text() != '') {

	// 			let B_01 = moneyToNumber($(this).find('.B_01').text().replace(/\,/g, ''))
	// 			let B_02 = moneyToNumber($(this).find('.B_02').text().replace(/\,/g, ''))
	// 			let B_03 = moneyToNumber($(this).find('.B_03').text().replace(/\,/g, ''))
	// 			let B_04 = moneyToNumber($(this).find('.B_04').text().replace(/\,/g, ''))
	// 			let B_05 = moneyToNumber($(this).find('.B_05').text().replace(/\,/g, ''))
	// 			let B_06 = moneyToNumber($(this).find('.B_06').text().replace(/\,/g, ''))
	// 			let B_07 = moneyToNumber($(this).find('.B_07').text().replace(/\,/g, ''))
	// 			let B_08 = moneyToNumber($(this).find('.B_08').text().replace(/\,/g, ''))
	// 			let B_09 = moneyToNumber($(this).find('.B_09').text().replace(/\,/g, ''))
	// 			let B_10 = moneyToNumber($(this).find('.B_10').text().replace(/\,/g, ''))
	// 			let B_11 = moneyToNumber($(this).find('.B_11').text().replace(/\,/g, ''))
	// 			let B_12 = moneyToNumber($(this).find('.B_12').text().replace(/\,/g, ''))


	// 			let total_budget = 0

	// 			total_budget = B_01 + B_02 + B_03 + B_04 + B_05 + B_06 + B_07 + B_08 + B_09 + B_10 + B_11 + B_12


	// 			$(this).find('.total_budget').text(customFormat(total_budget))
	// 		}
	// 	});
	// }

	// $(document).on('click', '.btn-save', function() {
	// 	var i = 0;
	// 	$('.edited').each(function() {
	// 		i++;
	// 	});
	// 	if (i == 0) {
	// 		cAlert.open('tidak ada data yang di ubah');
	// 	} else {
	// 		var msg = lang.anda_yakin_menyetujui;
	// 		if (i == 0) msg = lang.anda_yakin_menolak;
	// 		cConfirm.open(msg, 'save_perubahan');
	// 	}

	// });

	// function save_perubahan() {
	// 	var data_edit = {};
	// 	var i = 0;

	// 	$('.edited').each(function() {
	// 		var content = $(this).children('div');
	// 		if (typeof data_edit[$(this).attr('data-id')] == 'undefined') {
	// 			data_edit[$(this).attr('data-id')] = {};
	// 		}
	// 		data_edit[$(this).attr('data-id')][$(this).attr('data-name')] = $(this).text().replace(/[^0-9\-]/g, '');
	// 		i++;
	// 	});

	// 	var jsonString = JSON.stringify(data_edit);
	// 	$.ajax({
	// 		url: base_url + 'transaction/budget_detail/save_perubahan',
	// 		data: {
	// 			'json': jsonString,
	// 			verifikasi: i
	// 		},
	// 		type: 'post',
	// 		success: function(response) {
	// 			cAlert.open(response, 'success', 'refreshData');
	// 		}
	// 	})
	// }
