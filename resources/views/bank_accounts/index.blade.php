@extends('layouts.admin.index')
@section('title', $title)

@section('content')
<div class="card shadow radius-10">
	<div class="card-header">
		<div class="row">
			<div class="col-md-8">
				<button type="button" id="btn_add" class="btn btn-primary btn-label btn-block btn-sm"><i class="fas fa-plus label-icon align-middle fs-16 me-2"></i>Add Data</button>
				<button type="button" id="btn_reload" class="btn btn-secondary btn-label btn-block btn-sm"><i class="fas fa-sync label-icon align-middle fs-16 me-2"></i>Refresh Data</button>
			</div>
			<div class="col-md-4">
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
								<th width="35%">Name</th>
								<th width="20%">Code</th>
								<th width="20%">Method</th>
								<th width="15%">Account Number</th>
								<th width="15%">Account Number</th>
								<th class="center" width="15%">Logo</th>
								<th width="5%">Is Invoice</th>
								<th width="5%">Is Digital Envelope</th>
								<th width="5%">Is Auto Verif</th>
								<th width="5%">Is Manual Verif</th>
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
	<div class="modal-dialog modal-lg">
		<div class="modal-content">
			<form action="" id="form_add" enctype="multipart/form-data">
				@csrf
				<div class="modal-header">
					<h5 class="modal-title" id="modal_form_label"><strong>Form Add Data</strong></h5>
					<button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
				</div>
				<div class="modal-body">
					<input type="hidden" name="id" class="validate">
					<div class="row">	
						<div class="col-md-12">
							<div class="form-group row mb-2">
								<label class="form-label col-md-3">Name <span class="text-danger">*)</span></label>
								<div class="col-md-9">
									<input type="text" name="name" maxlength="200" class="form-control validate" placeholder="Name ...">
								</div>
							</div>
							<div class="form-group row mb-2">
								<label class="form-label col-md-3">Code <span class="text-danger">*)</span></label>
								<div class="col-md-9">
									<input type="text" name="code" maxlength="200" class="form-control validate" placeholder="Code ...">
								</div>
							</div>
							<div class="form-group row mb-2">
								<label class="form-label col-md-3">Method <span class="text-danger">*)</span></label>
								<div class="col-md-9">
									<input type="text" name="method" maxlength="200" class="form-control validate" placeholder="Method ...">
								</div>
							</div>
							<div class="form-group row mb-2">
								<label class="form-label col-md-3">Account Name </label>
								<div class="col-md-9">
									<input type="text" name="account_name" maxlength="200" class="form-control validate" placeholder="Account Name ...">
								</div>
							</div>
							<div class="form-group row mb-2">
								<label class="form-label col-md-3">Account Number </label>
								<div class="col-md-9">
									<input type="text" name="account_number" maxlength="200" class="form-control validate" placeholder="Account Number ...">
								</div>
							</div>
							<div class="form-group row mb-2 mt-3">
								<label class="form-label col-md-3">Is Invoice</label>
								<div class="col-md-9">
									<div class="form-check">
										<input name="is_invoice" class="form-check-input" type="checkbox" id="is_invoice" checked="checked" value="1">
										<label class="form-check-label" for="is_invoice">
											Yes
										</label>
									</div>
								</div>
							</div>
							<div class="form-group row mb-2 mt-3">
								<label class="form-label col-md-3">Is Digital Envelope</label>
								<div class="col-md-9">
									<div class="form-check">
										<input name="is_digital_envelope" class="form-check-input" type="checkbox" id="is_digital_envelope" checked="checked" value="1">
										<label class="form-check-label" for="is_digital_envelope">
											Yes
										</label>
									</div>
								</div>
							</div>
							<div class="form-group row mb-2 mt-3">
								<label class="form-label col-md-3">Is Automatic Verification</label>
								<div class="col-md-9">
									<div class="form-check">
										<input name="is_automatic_verification" class="form-check-input" type="checkbox" id="is_automatic_verification" checked="checked" value="1">
										<label class="form-check-label" for="is_automatic_verification">
											Yes
										</label>
									</div>
								</div>
							</div>
							<div class="form-group row mb-2 mt-3">
								<label class="form-label col-md-3">Is Manual Verification</label>
								<div class="col-md-9">
									<div class="form-check">
										<input name="is_manual_verification" class="form-check-input" type="checkbox" id="is_manual_verification" checked="checked" value="1">
										<label class="form-check-label" for="is_manual_verification">
											Yes
										</label>
									</div>
								</div>
							</div>
							<div class="form-group row mb-0">
								<label class="form-label col-md-3">Logo</label>
								<div class="col-md-9">
									<input type="hidden" name="logo_old">
									<input type="file" name="logo" class="form-control validate" accept=".jpg, .jpeg, .png">
								</div>
							</div>
							<div class="form-group row mb-2">
								<label class="form-label col-md-3"></label>
								<div class="col-md-9">
									<small class="text-danger">
										<em>*) Logo type must .jpg, .jpeg, .png Maximum size 2 MB.</em>
									</small>
								</div>
							</div>
							<div class="form-group row mb-2 logo_preview">
								<label class="form-label col-md-3"></label>
								<div class="col-md-9">
									<img id="logo_preview" width="200" class="img-fluid rounded d-block img-thumbnail">
								</div>
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
	var maxFileSize = 5 * 1024 * 1024;
	var className = '/bank-accounts';

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

		$('[name="logo"]').change(function() {
			if (this.files[0].size > maxFileSize) {
				$('[name="logo"]').val('');
				$('#logo_preview').attr('src', '');
				swalAlert('warning', 'Information', 'logo size cannot be more than 2 mb');
				return false;
			} else {
				$('.logo_preview').show();
				$('#logo_preview').attr('src', URL.createObjectURL(this.files[0]));
			}
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
								syamValidationServer('[name="code"]', 'code', data);
								syamValidationServer('[name="method"]', 'method', data);
								syamValidationServer('[name="logo"]', 'logo', data);
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

		$('.form-select').change(function() {
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
					var status = `
						<div class="form-check form-switch">
							<input class="form-check-input me-1" type="checkbox" onclick="updateStatus(${v.id}, '${v.is_active}')" ${(v.is_active == 1 ? 'checked' : '')}>
							<label class="form-check-label">${(v.is_active == 1 ? '<span class="badge bg-success">Active</span>' : '<span class="badge bg-danger">Unactive</span>')}</label>
						</div>
					`;

					var logo = ``;
					if (v.url_logo !== null) {
						logo = `
							<a data-fancybox data-src="${v.url_logo}" data-caption="${v.name}">
								<img src="${v.url_logo}" width="70px" height="25px">
							</a>
						`;
					}

					var html = '<tr>' +
						'<td class="center">' + no + '</td>' +
						'<td class="nowrap">' + v.name + '</td>' +
						'<td class="nowrap">' + v.code + '</td>' +
						'<td class="nowrap">' + v.method + '</td>' +
						'<td class="nowrap">' + (v.account_name != null ? v.account_name : '') + '</td>' +
						'<td class="nowrap">' + (v.account_number != null ? v.account_number : '') + '</td>' +
						'<td class="center nowrap">' + logo + '</td>' +
						'<td class="center">' + (v.is_invoice == 1 ? '<i class="fas fa-check"></i>' : '') + '</td>' +
						'<td class="center">' + (v.is_digital_envelope == 1 ? '<i class="fas fa-check"></i>' : '') + '</td>' +
						'<td class="center">' + (v.is_automatic_verification == 1 ? '<i class="fas fa-check"></i>' : '') + '</td>' +
						'<td class="center">' + (v.is_manual_verification == 1 ? '<i class="fas fa-check"></i>' : '') + '</td>' +
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
		$('.validate, #keyword_search').val('');
		$('.form-control').prop('readonly', false);
		syamValidationRemove('.form-control, .form-select');
		$('.logo_preview').hide();
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
					$('[name="code"]').val(data.data.code);
					$('[name="method"]').val(data.data.method);
					$('[name="account_name"]').val(data.data.account_name);
					$('[name="account_number"]').val(data.data.account_number);

					if (data.data.is_invoice == 1) {
						$('#is_invoice').prop('checked', true);
					} else {
						$('#is_invoice').prop('checked', false);
					}

					if (data.data.is_digital_envelope == 1) {
						$('#is_digital_envelope').prop('checked', true);
					} else {
						$('#is_digital_envelope').prop('checked', false);
					}

					if (data.data.is_automatic_verification == 1) {
						$('#is_automatic_verification').prop('checked', true);
					} else {
						$('#is_automatic_verification').prop('checked', false);
					}

					if (data.data.is_manual_verification == 1) {
						$('#is_manual_verification').prop('checked', true);
					} else {
						$('#is_manual_verification').prop('checked', false);
					}
					
					$('[name="logo_old"]').val(data.data.logo);
					if (data.data.url_logo !== null) {
						$('.logo_preview').show();
						$('#logo_preview').attr('src', data.data.url_logo)
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