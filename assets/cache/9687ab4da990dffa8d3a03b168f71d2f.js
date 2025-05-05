
	$(document).ready(function() {
		getData();
		$(document).on('keyup', '.budget', function(e) {
			// calculate();
			// calculateTotal();
			// console.log('y');
		});

	});

	$('#filter_tahun').change(function() {
		getData()
	});

	$('#filter_cost_centre').change(function() {
		getData()
	});



    function getData() {

        cLoader.open(lang.memuat_data + '...');
        $('.overlay-wrap').removeClass('hidden');
        var page = base_url + 'transaction/budget_production/data';
            page 	+= '/'+$('#filter_tahun').val();
			page 	+= '/'+$('#filter_cost_centre').val();

        $.ajax({
            url 	: page,
            data 	: {},
            type	: 'get',
            dataType: 'json',
            success	: function(response) {
                $('.table-1 tbody').html(response.table);
                cLoader.close();
                $('.overlay-wrap').addClass('hidden');	
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
		$('.table-2 tbody tr').each(function() {
			let totalMonthly = [];
			var grandTotal = 0;

			if ($(this).find('.budget').text() != '') {

				let B_01 = moneyToNumber($(this).find('.B_01').text().replace(/\,/g, ''))
				let B_02 = moneyToNumber($(this).find('.B_02').text().replace(/\,/g, ''))
				let B_03 = moneyToNumber($(this).find('.B_03').text().replace(/\,/g, ''))
				let B_04 = moneyToNumber($(this).find('.B_04').text().replace(/\,/g, ''))
				let B_05 = moneyToNumber($(this).find('.B_05').text().replace(/\,/g, ''))
				let B_06 = moneyToNumber($(this).find('.B_06').text().replace(/\,/g, ''))
				let B_07 = moneyToNumber($(this).find('.B_07').text().replace(/\,/g, ''))
				let B_08 = moneyToNumber($(this).find('.B_08').text().replace(/\,/g, ''))
				let B_09 = moneyToNumber($(this).find('.B_09').text().replace(/\,/g, ''))
				let B_10 = moneyToNumber($(this).find('.B_10').text().replace(/\,/g, ''))
				let B_11 = moneyToNumber($(this).find('.B_11').text().replace(/\,/g, ''))
				let B_12 = moneyToNumber($(this).find('.B_12').text().replace(/\,/g, ''))

				let total_budget = 0

				total_budget = B_01 + B_02 + B_03 + B_04 + B_05 + B_06 + B_07 + B_08 + B_09 + B_10 + B_11 + B_12

				$(this).find('.total_budget').text(customFormat(total_budget))

			}

			for (let i = 1; i <= 12; i++) {
				let total = 0;

				$('.B_' + ('0' + i).slice(-2)).each(function() {
					let value = moneyToNumber($(this).text().replace(/\,/g, ''));
					total += value;
				});

				totalMonthly.push(total);
				grandTotal += total;
			}

			for (let i = 0; i < totalMonthly.length; i++) {
				$('#totalB' + ('0' + (i + 1)).slice(-2)).text(customFormat(totalMonthly[i]));
			}

			$('#grand_total').text(customFormat(grandTotal))

		});

		$('.table-1 tbody tr').each(function(){
			if ($(this).find('.budget').text() != '') {
				let EST_01 = moneyToNumber($(this).find('.EST_01').text().replace(/\,/g, ''))
				let EST_02 = moneyToNumber($(this).find('.EST_02').text().replace(/\,/g, ''))
				let EST_03 = moneyToNumber($(this).find('.EST_03').text().replace(/\,/g, ''))
				let EST_04 = moneyToNumber($(this).find('.EST_04').text().replace(/\,/g, ''))
				let EST_05 = moneyToNumber($(this).find('.EST_05').text().replace(/\,/g, ''))
				let EST_06 = moneyToNumber($(this).find('.EST_06').text().replace(/\,/g, ''))
				let EST_07 = moneyToNumber($(this).find('.EST_07').text().replace(/\,/g, ''))
				let EST_08 = moneyToNumber($(this).find('.EST_08').text().replace(/\,/g, ''))
				let EST_09 = moneyToNumber($(this).find('.EST_09').text().replace(/\,/g, ''))
				let EST_10 = moneyToNumber($(this).find('.EST_10').text().replace(/\,/g, ''))
				let EST_11 = moneyToNumber($(this).find('.EST_11').text().replace(/\,/g, ''))
				let EST_12 = moneyToNumber($(this).find('.EST_12').text().replace(/\,/g, ''))

				let total_est = 0

				total_est = EST_01 + EST_02 + EST_03 + EST_04 + EST_05 + EST_06 + EST_07 + EST_08 + EST_09 + EST_10 + EST_11 + EST_12

				$(this).find('.total_est').text(customFormat(total_est))
			}
		});
	}

	$(document).on('click', '.btn-save', function() {
		var i = 0;
		$('.edited').each(function() {
			i++;
		});
		if (i == 0) {
			cAlert.open('tidak ada data yang di ubah');
		} else {
			var msg = lang.anda_yakin_menyetujui;
			if (i == 0) msg = lang.anda_yakin_menolak;
			cConfirm.open(msg, 'save_perubahan');
		}
	});

	function save_perubahan() {
		var data_edit = {};
		var i = 0;

		$('.edited').each(function() {
			var content = $(this).children('div');
			if (typeof data_edit[$(this).attr('data-id')] == 'undefined') {
				data_edit[$(this).attr('data-id')] = {};
			}
			data_edit[$(this).attr('data-id')][$(this).attr('data-name')] = $(this).text().replace(/[^0-9\-]/g, '');
			i++;
		});

		var jsonString = JSON.stringify(data_edit);
		// var jsonString = JSON.stringify(data_edit, null, 2); // 2 spaces indentation

		console.log(jsonString);
		$.ajax({
			url: base_url + 'transaction/budget_production/save_perubahan',
			data: {
				'json': jsonString,
				'tahun': $('#filter_tahun').val(),
				verifikasi: i
			},
			type: 'post',
			success: function(response) {
				cAlert.open(response, 'success', 'refreshData');
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
    });

	$(document).on('click', '.btn-export', function() {
		var currentdate = new Date();
		var datetime = currentdate.getDate() + "/" +
			(currentdate.getMonth() + 1) + "/" +
			currentdate.getFullYear() + " @ " +
			currentdate.getHours() + ":" +
			currentdate.getMinutes() + ":" +
			currentdate.getSeconds();

		// Set background colors
		// $('.bg-grey-2').attr('bgcolor','#f4f4f4');
		// $('.bg-grey-2').attr('bgcolor','#dddddd');
		// $('.bg-grey-2-1').attr('bgcolor','#b4b4b4');
		// $('.bg-grey-2-2').attr('bgcolor','#aaaaaa');
		// $('.bg-grey-2').attr('bgcolor','#888888');

		var table = '';
		table += '<table>'; // Add border style here

		// Add table rows
		table += '<tr><td colspan="1">PT Otsuka Indonesia</td></tr>';
		table += '<tr><td colspan="1">' + judul + ' Quantity Sales </td></tr>';
		table += '<tr><td colspan="1"> Print date </td><td>: ' + datetime + '</td></tr>';
		table += '</table><br><br>';

		// Add content body
		table += $(activeTable).html();

		var target = table;
		// window.open('data:application/vnd.ms-excel,' + encodeURIComponent(target));

		htmlToExcel(target)
		
		// $('.bg-grey-1,.bg-grey-2.bg-grey-2-1,.bg-grey-2-2,.bg-grey-3').each(function(){
		// 	$(this).removeAttr('bgcolor');
		// });
	});

	function download_template(){
		let tahun = $('#tahun').val();
		window.open(base_url + 'transaction/budget_production/template?tahun='+tahun)
	}

	$('.btn-act-import').click(function(){
		$("#modal-import").modal()
		$('#form-import')[0].reset();
		$('#tahun').val($('#filter_tahun').val())
		$('#divisi').val($('#filter_divisi').val())
		$('#tab').val(activeTable)
		$('#judul').val(judul)
	});

	// function do_import(){
	// 	$.ajax({
	// 		url: base_url + 'transaction/budget_production/import',
	// 		data: {
	// 			tahun: $('#tahun').val(),
	// 			fileimport: $('#fileimport').val(),
	// 		},
	// 		type: 'post',
	// 		dataType: 'json',
	// 		success: function(response) {
	// 			if (response.status == 'success') {            
    //                 cAlert.open(response.message, response.status, refresh_page());
    //             } else {
    //                 cAlert.open(response.message, response.status);
    //             }
	// 			// console.log(response);
	// 		}
	// 	});
	// }

	function refresh_page() {
        $(document).on('click', '.swal-button--confirm', function(){
            setTimeout(function () {
                window.location.href = 'http://localhost/manex/transaction/budget_production';
            }, 1000);
        })
    }

