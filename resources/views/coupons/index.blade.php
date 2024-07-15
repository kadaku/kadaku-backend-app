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
								<th width="10%">Code</th>
								<th class="nowrap" width="35%">Name</th>
								<th width="10%">Periode</th>
								<th class="right" width="10%">Amount</th>
								<th class="right" width="10%">Minimum Amount</th>
								<th width="5%">Status</th>
								<th class="nowrap" width="5%">Created By</th>
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
	<div class="modal-dialog" style="max-width:600px">
		<div class="modal-content">
			<form action="" id="form_add" enctype="multipart/form-data">
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
						<label class="form-label col-md-4">Code <span class="text-danger">*)</span></label>
						<div class="col-md-8">
							<input type="text" name="code" class="form-control validate" placeholder="Code ...">
						</div>
					</div>
					<div class="form-group row mb-2">
						<label class="form-label col-md-4">Description</label>
						<div class="col-md-8">
              <textarea name="description" class="form-control validate" rows="3" placeholder="Description ..."></textarea>
						</div>
					</div>
					<div class="form-group row mb-2">
						<label class="form-label col-md-4">Periode Start <span class="text-danger">*)</span></label>
						<div class="col-md-8">
							<div class="input-group">
                <span class="input-group-text"><i class="fas fa-calendar-alt"></i></span>
                <input type="text" name="periode_start" id="periode_start" class="form-control datetimepicker" data-provider="flatpickr" data-date-format="Y-m-d" data-enable-time required>
              </div>
						</div>
					</div>
					<div class="form-group row mb-2">
						<label class="form-label col-md-4">Periode End <span class="text-danger">*)</span></label>
						<div class="col-md-8">
							<div class="input-group">
                <span class="input-group-text"><i class="fas fa-calendar-alt"></i></span>
                <input type="text" name="periode_end" id="periode_end" class="form-control datetimepicker" data-provider="flatpickr" data-date-format="Y-m-d" data-enable-time required>
              </div>
						</div>
					</div>
          <div class="form-group row mb-2">
						<label class="form-label col-md-4">Amount</label>
						<div class="col-md-8">
              <div class="input-group">
                <span class="input-group-text"><i class="fas fa-dollar"></i></span>
                <input type="text" name="amount" onkeypress="return justNumber(event)" onkeyup="convertToCurrency(this)" class="form-control validate" value="0" placeholder="Amount ...">
              </div>
						</div>
					</div>
          <div class="form-group row mb-2">
						<label class="form-label col-md-4">Minimum Amount</label>
						<div class="col-md-8">
              <div class="input-group">
                <span class="input-group-text"><i class="fas fa-dollar"></i></span>
                <input type="text" name="minimum_amount" onkeypress="return justNumber(event)" onkeyup="convertToCurrency(this)" class="form-control validate" value="0" placeholder="Minimum Amount ...">
              </div>
						</div>
					</div>
					<div class="form-group row mb-0">
						<label class="form-label bold col-md-4">Thumbnail</label>
						<div class="col-md-8">
							<input type="hidden" name="file_thumbnail_old">
							<input type="file" name="file_thumbnail" class="form-control validate" accept=".png, .jpeg, .jpg">
						</div>
					</div>
					<div class="form-group row mb-2">
						<label class="form-label bold col-md-4"></label>
						<div class="col-md-8">
							<small class="text-danger">
								<em>*) Image type must png, jpg, jpeg, Maximum size 5 MB.</em>
							</small>
						</div>
					</div>
					<div class="form-group row mb-2 image_preview" style="display:none">
						<label class="form-label col-md-4"></label>
						<div class="col-md-8">
							<img id="image_preview" width="100" height="100" class="img-fluid rounded d-block img-thumbnail">
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
	var maxFileSize = 2 * 1024 * 1024;
	var className = '/coupons';

	$(function() {
		getListData();

    $('.datetimepicker').flatpickr({
      enableTime: true,
      time_24hr: true,
      dateFormat: 'Y-m-d H:i'
    });

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

		$('[name="file_thumbnail"]').change(function() {
			if (this.files[0].size > maxFileSize) {
				$('[name="file_thumbnail"]').val('');
				$('.image_preview').hide();
				$('#image_preview').attr('src', '');
				swalAlert('warning', 'Information', 'Image size cannot be more than 2 mb');
				return false;
			} else {
        $('.image_preview').show();
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
								syamValidationServer('[name="description"]', 'description', data);
								syamValidationGroupServer('[name="periode_start"]', 'periode_start', data);
								syamValidationGroupServer('[name="periode_end"]', 'periode_end', data);
								syamValidationGroupServer('[name="amount"]', 'amount', data);
								syamValidationGroupServer('[name="minimum_amount"]', 'minimum_amount', data);
								syamValidationServer('[name="thumbnail"]', 'thumbnail', data);
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

		$('.form-select, .validate').change(function() {
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
									<label class="form-check-label">${(v.is_active == 1 ? '<span class="badge bg-success">Active</span>' : '<span class="badge bg-danger">Unactive</span>')}</label>
								</div>`;

					var avatar = ``;
					if (v.url_thumbnail !== null) {
						avatar = `<a data-fancybox data-src="${v.url_thumbnail}" data-caption="${v.name}">
									<img src="${v.url_thumbnail}" class="rounded-circle shadow" width="34" height="34">
								</a>`;
					}

					var html = '<tr>' +
						'<td class="center">' + no + '</td>' +
						'<td>' + v.code + '</td>' +
						'<td>' + v.name + (v.description != null ? '<br><small class="text-muted">' + v.description + '</small>' : '-') + '</td>' +
						'<td class="nowrap">' + dateTimeSlash(v.periode_start) + '<br>' + dateTimeSlash(v.periode_end) + '</td>' +
						'<td class="nowrap right">' + numberToCurrency(v.amount) + '</td>' +
						'<td class="nowrap right">' + numberToCurrency(v.minimum_amount) + '</td>' +
						'<td class="nowrap">' + status + '</td>' +
						'<td><small>' + (v.created_by != null ? v.created_by : '') + '</small></td>' +
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
		$('.datetimepicker').val('<?php echo date('Y-m-d 08:00') ?>');
    $('[name="amount"], [name="minimum_amount"]').val('0');
		$('.form-control').prop('readonly', false);
		syamValidationRemove('.form-control');
		syamValidationGroupRemove('.form-control');
    $('.image_preview').hide();
		$('#image_preview').attr('src', '');
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
					$('.form_username, .form_password').hide();

					$('[name="id"]').val(data.data.id);
					$('[name="name"]').val(data.data.name);
					$('[name="code"]').val(data.data.code);
					$('[name="description"]').val(data.data.description);
					$('[name="periode_start"]').val(data.data.periode_start);
					$('[name="periode_end"]').val(data.data.periode_end);
					$('[name="amount"]').val(data.data.amount);
					$('[name="minimum_amount"]').val(data.data.minimum_amount);
					
					$('[name="file_thumbnail_old"]').val(data.data.thumbnail)
					if (data.data.url_thumbnail !== null) {
            $('.image_preview').show();
						$('#image_preview').attr('src', data.data.url_thumbnail)
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