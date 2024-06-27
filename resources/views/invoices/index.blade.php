@extends('layouts.admin.index')
@section('title', $title)

@section('content')
<div class="card shadow radius-10">
	<div class="card-header">
		<div class="row">
			<div class="col-md-6">
				<button type="button" id="btn_add" class="btn btn-primary btn-label btn-block btn-sm"><i class="fas fa-plus label-icon align-middle fs-16 me-2"></i>Add Data</button>
				<button type="button" id="btn_reload" class="btn btn-secondary btn-label btn-block btn-sm"><i class="fas fa-sync label-icon align-middle fs-16 me-2"></i>Refresh Data</button>
			</div>
			<div class="col-md-6">
				<div class="custom-search">
					<input type="text" class="form-search" id="keyword_search" placeholder="Parameter Search...">
				</div>
			</div>
		</div>
	</div>
	<div class="card-body">
		<div class="row">
			<div class="col-md-12">
				<div class="table-responsive">
					<table id="table_list" class="table table-striped table-hover table-sm">
						<thead>
							<tr>
								<th class="center" width="5%">No.</th>
								<th width="25%">Invoice</th>
								<th width="25%">Payer Email</th>
								<th width="25%">Packages</th>
								<th class="center" width="10%">Payment Channel</th>
								<th class="center" width="10%">Payment Method</th>
								<th class="right" width="10%">Total</th>
								<th width="10%">Created At</th>
								<th width="10%">Paid At</th>
								<th class="center" widht="5%">Status</th>
								<th widht="5%"></th>
							</tr>
						</thead>
						<tbody></tbody>
					</table>
				</div>
			</div>
		</div>
		<div class="row">
			<input type="hidden" id="page_list">
			<div class="col-sm-12 col-md-5">
				<div id="summary"></div>
			</div>
			<div class="col-sm-12 col-md-7">
				<div id="pagination"></div>
			</div>
		</div>
	</div>
</div>

<script>
	var className = '/invoices';

	$(function() {
		getListData();

		$('#keyword_search').keyup(function() {
			getListData();
		});

		$('#btn_reload').click(function() {
			resetForm();
			getListData();
		});
	});

	function getListData(page = 1) {
		$.ajax({
			type: 'GET',
			url: baseUrl + className + '/list',
			data: 'page=' + page + '&keyword=' + $('#keyword_search').val(),
			cache: false,
			dataType: 'JSON',
			beforeSend: function() {
				showLoader();
				$('#page_list').val(page);
			},
			success: function(data) {
				if ((page > 1) && (data.data.list.length == 0)) {
					getListData(page - 1);
					return false;
				}

				$('#pagination').html(paginationJump(data.data.total, data.data.limit, data.data.page, 1));
				$('#summary').html(pageSummary(data.data.total, data.data.list.length, data.data.limit, data.data.page));

				$('#table_list tbody').empty();
				$.each(data.data.list, function(i, v) {
					var no = ((i + 1) + ((data.data.page - 1) * data.data.limit));

					var btn = ''
					if (v.payment_method_invoice == 'MANUAL' && (v.status == 'PENDING' || v.status == 'UNPAID')) {
						btn += '<button type="button" class="btn btn-success btn-sm" onclick="updateVerification(' + v.id + ', ' + data.data.page + ')"><i class="fas fa-check-circle"></i></button>';
					}

					var html = '<tr>' +
						'<td class="center">' + no + '</td>' +
						'<td class="nowrap">' + v.external_id + '</td>' +
						'<td class="nowrap">' + v.payer_email + '</td>' +
						'<td class="nowrap">' + (v.items.length > 0 ? v.items[0].name : '') + '</td>' +
						'<td class="nowrap center">' + v.payment_channel + '</td>' +
						'<td class="nowrap center">' + v.payment_method_invoice + '</td>' +
						'<td class="nowrap right">Rp. ' + (v.amount != null ? numberToCurrency(v.amount) : 0) + '</td>' +
						'<td class="nowrap">' + (v.created != null ? v.created : '-') + '</td>' +
						'<td class="nowrap">' + (v.paid_at != null ? v.paid_at : '-') + '</td>' +
						'<td class="nowrap">' + v.status + '</td>' +
						'<td class="right nowrap">' +
							btn
						'</td>' +
						'</tr>';

					$('#table_list tbody').append(html);
				});
			},
			complete: function() {
				hideLoader();
			},
			error: function(e) {
				toastrAlert('error', e.status, e.statusText);
			}
		});
	}

	function paging(page) {
		getListData(page);
	}

	function resetForm() {
		$('#keyword_search').val('');
	}

	function updateVerification(id, page) {
		Swal.fire({
			title: 'Confirmation',
			text: 'Are you sure want to update this invoice to paid ?',
			icon: 'question',
			showCancelButton: true,
			confirmButtonColor: '#FF6E31',
			cancelButtonColor: '#6c757d',
			cancelButtonText: '<i class="fas fa-circle-xmark me-2"></i>Cancel',
			confirmButtonText: '<i class="fas fa-paper-plane me-2"></i>Yes',
			allowOutsideClick: false,
			reverseButtons: true,
		}).then((result) => {
			if (result.isConfirmed) {
				$.ajax({
					type: 'POST',
					url: baseUrl + className + '/update',
					data: {
						id: id,
					},
					cache: false,
					dataType: 'JSON',
					beforeSend: function() {
						showLoader();
					},
					success: function(data) {
						if (data.status) {
							getListData(page);
							toastrAlert('success', 'Success', data.message);
						} else {
							toastrAlert('warning', 'Information', data.message);
						}
					},
					complete: function() {
						hideLoader();
					},
					error: function(e) {
						toastrAlert('error', e.status, e.statusText);
					}
				});
			}
		});
	}
</script>
@endsection