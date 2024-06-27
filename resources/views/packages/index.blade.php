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
								<th width="25%">Name</th>
								<th width="45%">Description</th>
								<th class="right" width="10%">Price</th>
								<th class="center" width="10%">Discount</th>
								<th width="5%">Status</th>
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

<div class="modal fade" id="modal_form" data-bs-backdrop="static">
	<div class="modal-dialog">
		<div class="modal-content">
			<form action="" id="form_add">
				@csrf
				<div class="modal-header">
					<h5 class="modal-title" id="modal_form_label"><strong>Form Add Data</strong></h5>
					<button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
				</div>
				<div class="modal-body">
					<input type="hidden" name="id" class="validate">
					<div class="form-group row mb-2">
						<label class="form-label col-md-4">Name <span class="text-danger">*)</span></label>
						<div class="col-md-8">
							<input type="text" name="name" class="form-control validate" placeholder="Name ...">
						</div>
					</div>
					<div class="form-group row mb-2">
						<label class="form-label col-md-4">Description</label>
						<div class="col-md-8">
              <textarea name="description" class="form-control validate" placeholder="Description ..." rows="4"></textarea>
						</div>
					</div>
					<div class="form-group row mb-2">
						<label class="form-label col-md-4">Price <span class="text-danger">*)</span></label>
						<div class="col-md-8">
							<input type="text" name="price" onkeypress="return justNumber(event)" onkeyup="convertToCurrency(this)" class="form-control validate" value="0" placeholder="Price ...">
						</div>
					</div>
					<div class="form-group row mb-2">
						<label class="form-label col-md-4">Discount</label>
						<div class="col-md-4">
							<input type="number" name="discount" min="0" max="100" maxlength="3" class="form-control validate" placeholder="Discount ...">
						</div>
					</div>
					<div class="form-group row mb-2">
						<label class="form-label col-md-4">Valid Days <span class="text-danger">*)</span></label>
						<div class="col-md-4">
							<input type="number" name="valid_days" min="0" max="100" maxlength="3" class="form-control validate" placeholder="Valid Days ...">
						</div>
					</div>
					<div class="form-group row mb-2">
						<label class="form-label col-md-4">Is Premium</label>
						<div class="col-md-8">
							<div class="form-check">
								<input name="is_premium" class="form-check-input" type="checkbox" id="is_premium" value="1">
								<label class="form-check-label" for="is_premium">
									Yes
								</label>
							</div>
						</div>
					</div>
					<div class="form-group row mb-2">
						<label class="form-label col-md-4">Is Reseller</label>
						<div class="col-md-8">
							<div class="form-check">
								<input name="is_reseller" class="form-check-input" type="checkbox" id="is_reseller" value="1">
								<label class="form-check-label" for="is_reseller">
									Yes
								</label>
							</div>
						</div>
					</div>
					<div class="form-group row mb-2">
						<label class="form-label col-md-4">Is Recommended</label>
						<div class="col-md-8">
							<div class="form-check">
								<input name="is_recommended" class="form-check-input" type="checkbox" id="is_recommended" value="1">
								<label class="form-check-label" for="is_recommended">
									Yes
								</label>
							</div>
						</div>
					</div>
				</div>
				<div class="modal-footer">
					<button type="button" class="btn btn-secondary btn-label" data-bs-dismiss="modal"><i class="fas fa-circle-xmark label-icon align-middle fs-16 me-2"></i>Cancel</button>
					<button type="submit" class="btn btn-primary btn-label"><i class="fas fa-save label-icon align-middle fs-16 me-2"></i>Save</button>
				</div>
			</form>
		</div>
	</div>
</div>

<script>
	var className = '/packages';

	$(function() {
		getListData();

		$('#keyword_search').keyup(function() {
			getListData();
		});

		$('#btn_add').click(function() {
			resetForm();
			$('#modal_form').modal('show');
			$('#modal_form_label').text('Form Add Data');
		});

		$('#btn_reload').click(function() {
			resetForm();
			getListData();
		});

		$('#form_add').submit(function(e) {
			e.preventDefault();

			var text = 'Are you sure want to save this data ?';
			var id = $('[name="id"]').val();
			if (id !== '') {
				text = 'Are you sure want to update this data ?';
			}

			Swal.fire({
				title: 'Confirmation',
				text: text,
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
					var data = new FormData($(this)[0]);
					$.ajax({
						type: 'POST',
						url: baseUrl + className + '/store',
						data: data,
						cache: false,
						contentType: false,
						processData: false,
						dataType: 'JSON',
						beforeSend: function() {
							showLoader();
						},
						success: function(data) {
							if (data.validate == true) {
								syamValidationServer('[name="name"]', 'name', data);
								syamValidationServer('[name="valid_days"]', 'valid_days', data);
								syamValidationServer('[name="price"]', 'price', data);
								syamValidationServer('[name="discount"]', 'discount', data);
								return false;
							}

							if (data.status) {
								$('#modal_form').modal('hide');
								resetForm();
								getListData($('#page_list').val());
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
		});

		$('.validate').keyup(function() {
			if ($(this).val() !== '') {
				syamValidationRemove(this);
			}
		});

		$('.validate').change(function() {
			if ($(this).val() !== '') {
				syamValidationRemove(this);
			}
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
					var status = `<div class="form-check form-switch">
									<input class="form-check-input me-1" type="checkbox" onclick="updateStatus(${v.id}, '${v.is_active}')" ${(v.is_active == 1 ? 'checked' : '')}>
									<label class="form-check-label">${(v.is_active === 1 ? '<span class="badge bg-success">Active</span>' : '<span class="badge bg-danger">Unactive</span>')}</label>
								</div>`;

					var html = '<tr>' +
						'<td class="center">' + no + '</td>' +
						'<td class="nowrap">' + v.name + '</td>' +
						'<td>' + (v.description != null ? v.description : '-') + '</td>' +
						'<td class="nowrap right">' + (v.price != null ? numberToCurrency(v.price) : '-') + '</td>' +
						'<td class="nowrap center">' + (v.discount != null ? v.discount + (v.discount != 0 ? '%' : '') : '-') + '</td>' +
						'<td class="nowrap">' + status + '</td>' +
						'<td class="right nowrap">' +
						'<button type="button" class="btn btn-success btn-sm" onclick="editData(' + v.id + ', ' + data.data.page + ')"><i class="fas fa-edit"></i></button> ' +
						'<button type="button" class="btn btn-danger btn-sm" onclick="deleteData(' + v.id + ', ' + data.data.page + ')"><i class="fas fa-trash-alt"></i></button>' +
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
		$('#form_add')[0].reset();
		$('.validate, #keyword_search').val('');
		$('[name="price"], [name="discount"], [name="valid_days"]').val('0');
		$('.form-control').prop('readonly', false);
		syamValidationRemove('.form-control');
	}

	function updateStatus(id, status) {
		$.ajax({
			type: 'POST',
			url: baseUrl + className + '/update-status',
			data: {
				id: id,
				status: status,
			},
			cache: false,
			dataType: 'JSON',
			beforeSend: function() {
				showLoader();
			},
			success: function(data) {
				if (data.status !== false) {
					getListData($('#page_list').val());
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

	function editData(id, page) {
		$.ajax({
			type: 'GET',
			url: baseUrl + className + '/show/' + id,
			cache: false,
			dataType: 'JSON',
			beforeSend: function() {
				resetForm();
				showLoader();
				$('#page_list').val(page);
			},
			success: function(data) {
				if (data.status) {
					$('[name="id"]').val(data.data.id);
					$('[name="name"]').val(data.data.name);
					$('[name="description"]').val(data.data.description);
					$('[name="price"]').val(data.data.price);
					$('[name="discount"]').val(data.data.discount);
					$('[name="valid_days"]').val(data.data.valid_days);

					if (data.data.is_premium == 1) {
						$('#is_premium').prop('checked', true);
					} else {
						$('#is_premium').prop('checked', false);
					}

					if (data.data.is_reseller == 1) {
						$('#is_reseller').prop('checked', true);
					} else {
						$('#is_reseller').prop('checked', false);
					}

					if (data.data.is_recommended == 1) {
						$('#is_recommended').prop('checked', true);
					} else {
						$('#is_recommended').prop('checked', false);
					}

					$('#modal_form').modal('show');
					$('#modal_form_label').text('Form Update Data');
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

	function deleteData(id, page) {
		Swal.fire({
			title: 'Confirmation',
			text: 'Are you sure want to destroy this data ?',
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
					type: 'DELETE',
					url: baseUrl + className + '/destroy/' + id,
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