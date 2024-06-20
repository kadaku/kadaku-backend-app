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
								<th class="center" width="10%">Image</th>
								<th width="75%">Title</th>
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
	<div class="modal-dialog modal-xl">
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
						<div class="col-md-7">
							<div class="form-group row mb-2">
								<label class="form-label col-md-3">Category <span class="text-danger">*)</span></label>
								<div class="col-md-9">
									<select name="category_layout_id" class="form-select">
										@if (isset($categories_layouts) && $categories_layouts)
											<option value="">Choose ...</option>
											@foreach ($categories_layouts as $row)
												<option value="{{ $row->id }}">{{ $row->name }}</option>
											@endforeach
										@endif
									</select>
								</div>
							</div>
							<div class="form-group row mb-2">
								<label class="form-label col-md-3">Title <span class="text-danger">*)</span></label>
								<div class="col-md-9">
									<input type="text" name="title" maxlength="200" class="form-control validate" placeholder="Title ...">
								</div>
							</div>
							<div class="form-group row mb-2">
								<label class="form-label col-md-3">Icon <span class="text-danger">*)</span></label>
								<div class="col-md-9">
									<textarea name="icon" class="form-control validate" rows="2" placeholder="Icon ..."></textarea>
								</div>
							</div>
							<div class="form-group row mb-2 mt-3">
								<label class="form-label col-md-3">Is Premium</label>
								<div class="col-md-9">
									<div class="form-check">
										<input name="is_premium" class="form-check-input" type="checkbox" id="is_premium" checked="checked" value="1">
										<label class="form-check-label" for="is_premium">
											Yes
										</label>
									</div>
								</div>
							</div>
							<div class="form-group row mb-2">
								<label class="form-label col-md-3">Body HTML <span class="text-danger">*)</span></label>
								<div class="col-md-9">
									<textarea name="body" class="form-control validate" rows="6" placeholder="Body HTML ..."></textarea>
								</div>
							</div>
							<div class="form-group row mb-0">
								<label class="form-label col-md-3">Image</label>
								<div class="col-md-9">
									<input type="hidden" name="image_old">
									<input type="file" name="image" class="form-control validate" accept=".jpg, .jpeg, .png">
								</div>
							</div>
							<div class="form-group row mb-2">
								<label class="form-label col-md-3"></label>
								<div class="col-md-9">
									<small class="text-danger">
										<em>*) Image type must .jpg, .jpeg, .png Maximum size 2 MB.</em>
									</small>
								</div>
							</div>
							<div class="form-group row mb-2 image_preview">
								<label class="form-label col-md-3"></label>
								<div class="col-md-9">
									<img id="image_preview" width="200" class="img-fluid rounded d-block img-thumbnail">
								</div>
							</div>
						</div>
						<div class="col-md-5">
							
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

<script src="{{ asset('extend/js/code-editor-shortcut-keys.js') }}"></script>

<script>
	var maxFileSize = 5 * 1024 * 1024;
	var className = '/layouts';

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

		$('[name="category_layout_id"]').change(function() {
			if ($(this).val() != '') {
				$('[name="title"]').val($('[name="category_layout_id"] option:selected').text());
			} else {
				$('[name="title"]').val('');
			}
		});

		$('[name="image"]').change(function() {
			if (this.files[0].size > maxFileSize) {
				$('[name="image"]').val('');
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
				title: 'Konfirmasi',
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
								syamValidationServer('[name="category_layout_id"]', 'category_layout_id', data);
								syamValidationServer('[name="title"]', 'title', data);
								syamValidationServer('[name="icon"]', 'icon', data);
								syamValidationServer('[name="body"]', 'body', data);
								syamValidationServer('[name="image"]', 'image', data);
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
							<label class="form-check-label">${(v.is_active === 1 ? '<span class="badge bg-success">Active</span>' : '<span class="badge bg-danger">Unactive</span>')}</label>
						</div>
					`;

					var image = ``;
					if (v.url_image !== null) {
						image = `
							<a data-fancybox data-src="${v.url_image}" data-caption="${v.name}">
								<img src="${v.url_image}" class="shadow" style="border-radius:1rem" width="100px" height="120px">
							</a>
						`;
					}

					var html = '<tr>' +
						'<td class="center">' + no + '</td>' +
						'<td class="center nowrap">' + image + '</td>' +
						'<td>' + v.title + '</td>' +
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
		$('.image_preview').hide();
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
					$('[name="category_layout_id"]').val(data.data.category_layout_id);
					$('[name="title"]').val(data.data.title);
					$('[name="icon"]').val(data.data.icon);
					$('[name="body"]').val(data.data.body);

					if (data.data.is_premium == 1) {
						$('#is_premium').prop('checked', true);
					} else {
						$('#is_premium').prop('checked', false);
					}
					
					$('[name="image_old"]').val(data.data.image);
					if (data.data.url_image !== null) {
						$('.image_preview').show();
						$('#image_preview').attr('src', data.data.url_image)
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