@extends('layouts.admin.index')
@section('title', 'Accounts')

@section('content')
<div class="card shadow radius-10">
	<div class="card-header">
		<div class="row">
			<div class="col-md-6">
				<button type="button" id="btn_add" class="btn btn-primary btn-block btn-sm me-1"><i class="bx bx-plus-circle"></i> Add Data</button>
				<button type="button" id="btn_reload" class="btn btn-secondary btn-block btn-sm"><i class="bx bx-sync"></i> Refresh Data</button>
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
					<table id="table_list" class="table table-striped table-hover">
						<thead class="table-light">
							<tr>
								<th class="center" width="5%">No.</th>
								<th class="center" width="5%">Avatar</th>
								<th width="35%">Fullname</th>
								<th width="15%">Email</th>
								<th width="15%">Phone</th>
								<th width="10%">Privilege</th>
								<th width="5%">Status</th>
								<th class="nowrap" width="20%">Created Date</th>
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
				<div class="modal-header">
					<h5 class="modal-title" id="modal_form_label"><strong>Form Add Data</strong></h5>
					<button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
				</div>
				<div class="modal-body">
					<input type="hidden" name="id" class="validate">
					<div class="form-group mb-2">
						<label class="form-label">Name <span class="text-danger">*)</span></label>
						<input type="text" name="name" class="form-control validate" placeholder="Name ...">
					</div>
					<div class="form-group mb-2">
						<label class="form-label">Email <span class="text-danger">*)</span></label>
						<input type="email" name="email" class="form-control validate" placeholder="Email ...">
					</div>
					<div class="form-group mb-2">
						<label class="form-label">Phone <span class="text-danger">*)</span></label>
						<input type="text" name="phone" onkeypress="return justNumber(event)" class="form-control validate" placeholder="Phone ...">
					</div>
					<div class="form-group mb-2">
						<label class="form-label">Privilege <span class="text-danger">*)</span></label>
						<select name="user_group_id" id="user_group_id" class="form-select form-control">
							<option value="">-- Pilih --</option>
							@foreach ($user_groups as $row)
								<option value="{{ $row->id }}">{{ $row->name }}</option>
							@endforeach
						</select>
					</div>
				</div>
				<div class="modal-footer">
					<button type="button" class="btn btn-secondary btn-sm" data-bs-dismiss="modal"><i class="bx bx-x-circle"></i> Cancel</button>
					<button type="submit" class="btn btn-primary btn-sm"><i class="bx bx-save"></i> Save</button>
				</div>
			</form>
		</div>
	</div>
</div>

<script>
	var maxFileSize = 2 * 1024 * 1024;
	var className = '/accounts';
	var avatarDefault = '<?php echo asset("storage/images/accounts/avatar.png") ?>';

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

		$('[name="file_avatar"]').change(function() {
			if (this.files[0].size > maxFileSize) {
				$('[name="file_avatar"]').val('');
				$('#image_preview').attr('src', '');
				swalAlert('warning', 'Information', 'Image size cannot be more than 2 mb');
				return false;
			} else {
				$('#image_preview').attr('src', URL.createObjectURL(this.files[0]));
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
				title: 'Konfirmasi',
				text: text,
				icon: 'question',
				showCancelButton: true,
				confirmButtonColor: '#FF6E31',
				cancelButtonColor: '#6c757d',
				cancelButtonText: '<i class="bx bx-x-circle"></i> Cancel',
				confirmButtonText: '<i class="bx bx-paper-plane"></i> Confirm',
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
								syamValidationServer('[name="email"]', 'email', data);
								syamValidationServer('[name="user_group_id"]', 'user_group_id', data);
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

				$('#pagination').html(pagination(data.data.total, data.data.limit, data.data.page, 1));
				$('#summary').html(pageSummary(data.data.total, data.data.list.length, data.data.limit, data.data.page));

				$('#table_list tbody').empty();
				$.each(data.data.list, function(i, v) {
					var no = ((i + 1) + ((data.data.page - 1) * data.data.limit));
					var status = `<div class="form-check form-switch">
									<input class="form-check-input me-1" type="checkbox" onclick="updateStatus(${v.id}, '${v.is_active}')" ${(v.is_active == 1 ? 'checked' : '')}>
									<label class="form-check-label">${(v.is_active === 1 ? '<span class="badge bg-success">Active</span>' : '<span class="badge bg-danger">Unactive</span>')}</label>
								</div>`;

					var avatar = `<img src="${avatarDefault}" class="rounded-circle border shadow" width="34" height="34">`;
					if (v.photo !== null) {
						avatar = `<a data-fancybox data-src="${v.photo}" data-caption="${v.name}">
									<img src="${v.photo}" class="rounded-circle shadow" width="34" height="34">
								</a>`;
					}

					var btnResetPassword = '<button type="button" class="btn btn-warning btn-sm text-black" onclick="resetPasswordData(' + v.id + ', ' + data.data.page + ')"><i class="bx bx-key"></i></button> ';

					var html = '<tr>' +
						'<td class="center">' + no + '</td>' +
						'<td class="center">' + avatar + '</td>' +
						'<td class="nowrap">' + v.name + '</td>' +
						'<td>' + v.email + '</td>' +
						'<td>' + v.phone + '</td>' +
						'<td class="nowrap">' + v.user_group + '</td>' +
						'<td class="nowrap">' + status + '</td>' +
						'<td class="nowrap">' + dateTimeIndoNew(v.created_at) + '</td>' +
						'<td class="right nowrap">' +
						btnResetPassword +
						'<button type="button" class="btn btn-success btn-sm" onclick="editData(' + v.id + ', ' + data.data.page + ')"><i class="bx bx-edit"></i></button> ' +
						'<button type="button" class="btn btn-danger btn-sm" onclick="deleteData(' + v.id + ', \'' + (v.photo !== null ? v.photo : '') + '\', ' + data.data.page + ')"><i class="bx bx-trash"></i></button>' +
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
		$('.form_username, .form_password').show();
		$('.form-control').prop('readonly', false);
		syamValidationRemove('.form-control');
		$('#image_preview').attr('src', avatarDefault);
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
				showLoader();
				$('#page_list').val(page);
			},
			success: function(data) {
				if (data.status) {
					$('[name="id"]').val(data.data.id);
					$('[name="name"]').val(data.data.name);
					$('[name="email"]').val(data.data.email);
					$('[name="phone"]').val(data.data.phone);
					$('[name="user_group_id"]').val(data.data.user_group_id).change();

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
</script>
@endsection