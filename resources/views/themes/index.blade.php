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
								<th width="20%">Name</th>
								<th width="15%">Category</th>
								<th width="10%">Type</th>
								<th class="center" width="10%">Background</th>
								<th class="center" width="10%">Thumbnail</th>
								<th class="center" width="10%">Thumbnail XS</th>
								<th class="center" width="5%">Is Premium</th>
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
	<div class="modal-dialog" style="min-width:90%">
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
						<div class="col-md-6">
							<div class="form-group row mb-2">
								<label class="form-label col-md-3">Category <span class="text-danger">*)</span></label>
								<div class="col-md-9">
									<select name="category_id" class="form-select">
										@if (isset($categories_themes) && $categories_themes)
											<option value="">Choose ...</option>
											@foreach ($categories_themes as $row)
												<option value="{{ $row->id }}">{{ $row->name }}</option>
											@endforeach
										@endif
									</select>
								</div>
							</div>
							<div class="form-group row mb-2">
								<label class="form-label col-md-3">Type <span class="text-danger">*)</span></label>
								<div class="col-md-9">
									<select name="type_id" class="form-select">
										@if (isset($themes_type) && $themes_type)
											<option value="">Choose ...</option>
											@foreach ($themes_type as $row)
												<option value="{{ $row->id }}">{{ $row->name }}</option>
											@endforeach
										@endif
									</select>
								</div>
							</div>
							<div class="form-group row mb-2">
								<label class="form-label col-md-3">Name <span class="text-danger">*)</span></label>
								<div class="col-md-9">
									<input type="text" name="name" maxlength="200" class="form-control validate" placeholder="Name ...">
								</div>
							</div>
              <div class="form-group row mb-2">
								<label class="form-label col-md-3">Description</label>
								<div class="col-md-9">
									<textarea type="text" name="description" class="form-control validate" rows="4" placeholder="Description ..."></textarea>
								</div>
							</div>
              <div class="form-group row mb-2">
								<label class="form-label col-md-3">Layout <span class="text-danger">*)</span></label>
								<div class="col-md-9">
									<input type="text" name="layout" class="form-control validate" placeholder="Layout ...">
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
						</div>
						<div class="col-md-6">
              <div class="form-group row mb-2">
								<label class="form-label col-md-3">Font <span class="text-danger">*)</span></label>
								<div class="col-md-9">
									<input type="text" name="font" class="form-control validate" placeholder="Font ...">
								</div>
							</div>
              <div class="form-group row mb-2">
								<label class="form-label col-md-3">Font Body <span class="text-danger">*)</span></label>
								<div class="col-md-9">
									<input type="text" name="font_body" class="form-control validate" placeholder="Font Body ...">
								</div>
							</div>
              <div class="form-group row mb-2">
								<label class="form-label col-md-3">Color Primary <span class="text-danger">*)</span></label>
								<div class="col-md-9">
									<input type="text" name="color_primary" class="form-control validate" placeholder="Color Primary ...">
								</div>
							</div>
              <div class="form-group row mb-2">
								<label class="form-label col-md-3">Color Secondary <span class="text-danger">*)</span></label>
								<div class="col-md-9">
									<input type="text" name="color_secondary" class="form-control validate" placeholder="Color Secondary ...">
								</div>
							</div>
              <div class="form-group row mb-2">
								<label class="form-label col-md-3">Color Tertiary <span class="text-danger">*)</span></label>
								<div class="col-md-9">
									<input type="text" name="color_tertiary" class="form-control validate" placeholder="Color Tertiary ...">
								</div>
							</div>
              <div class="form-group row mb-2">
								<label class="form-label col-md-3">Color Quaternary <span class="text-danger">*)</span></label>
								<div class="col-md-9">
									<input type="text" name="color_quaternary" class="form-control validate" placeholder="Color Quaternary ...">
								</div>
							</div>
							<div class="form-group row mb-0">
								<label class="form-label col-md-3">Background</label>
								<div class="col-md-9">
									<input type="hidden" name="background_old">
									<input type="file" name="background" class="form-control validate" accept=".jpg, .jpeg, .png">
								</div>
							</div>
							<div class="form-group row mb-2">
								<label class="form-label col-md-3"></label>
								<div class="col-md-9">
									<small class="text-danger">
										<em>*) Background type must .jpg, .jpeg, .png Maximum size 2 MB.</em>
									</small>
								</div>
							</div>
							<div class="form-group row mb-2 background_preview">
								<label class="form-label col-md-3"></label>
								<div class="col-md-9">
									<img id="background_preview" width="200" class="img-fluid rounded d-block img-thumbnail">
								</div>
							</div>
              {{-- thumbnail --}}
							<div class="form-group row mb-0">
								<label class="form-label col-md-3">Thumbnail</label>
								<div class="col-md-9">
									<input type="hidden" name="thumbnail_old">
									<input type="file" name="thumbnail" class="form-control validate" accept=".jpg, .jpeg, .png">
								</div>
							</div>
							<div class="form-group row mb-2">
								<label class="form-label col-md-3"></label>
								<div class="col-md-9">
									<small class="text-danger">
										<em>*) Thumbnail type must .jpg, .jpeg, .png Maximum size 2 MB.</em>
									</small>
								</div>
							</div>
							<div class="form-group row mb-2 thumbnail_preview">
								<label class="form-label col-md-3"></label>
								<div class="col-md-9">
									<img id="thumbnail_preview" width="200" class="img-fluid rounded d-block img-thumbnail">
								</div>
							</div>
              {{-- thumbnail xs --}}
							<div class="form-group row mb-0">
								<label class="form-label col-md-3">Thumbnail XS</label>
								<div class="col-md-9">
									<input type="hidden" name="thumbnail_xs_old">
									<input type="file" name="thumbnail_xs" class="form-control validate" accept=".jpg, .jpeg, .png">
								</div>
							</div>
							<div class="form-group row mb-2">
								<label class="form-label col-md-3"></label>
								<div class="col-md-9">
									<small class="text-danger">
										<em>*) Thumbnail xs type must .jpg, .jpeg, .png Maximum size 2 MB.</em>
									</small>
								</div>
							</div>
							<div class="form-group row mb-2 thumbnail_xs_preview">
								<label class="form-label col-md-3"></label>
								<div class="col-md-9">
									<img id="thumbnail_xs_preview" width="200" class="img-fluid rounded d-block img-thumbnail">
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

<script src="{{ asset('extend/js/code-editor-shortcut-keys.js') }}"></script>

<script>
	var maxFileSize = 5 * 1024 * 1024;
	var className = '/themes';

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

		$('[name="background"]').change(function() {
			if (this.files[0].size > maxFileSize) {
				$('[name="background"]').val('');
				$('#background_preview').attr('src', '');
				swalAlert('warning', 'Information', 'Background size cannot be more than 2 mb');
				return false;
			} else {
				$('.background_preview').show();
				$('#background_preview').attr('src', URL.createObjectURL(this.files[0]));
			}
		});

		$('[name="thumbnail"]').change(function() {
			if (this.files[0].size > maxFileSize) {
				$('[name="thumbnail"]').val('');
				$('#thumbnail_preview').attr('src', '');
				swalAlert('warning', 'Information', 'Thumbnail size cannot be more than 2 mb');
				return false;
			} else {
				$('.thumbnail_preview').show();
				$('#thumbnail_preview').attr('src', URL.createObjectURL(this.files[0]));
			}
		});

		$('[name="thumbnail_xs"]').change(function() {
			if (this.files[0].size > maxFileSize) {
				$('[name="thumbnail_xs"]').val('');
				$('#thumbnail_xs_preview').attr('src', '');
				swalAlert('warning', 'Information', 'Thumbnail XS size cannot be more than 2 mb');
				return false;
			} else {
				$('.thumbnail_xs_preview').show();
				$('#thumbnail_xs_preview').attr('src', URL.createObjectURL(this.files[0]));
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
								syamValidationServer('[name="category_id"]', 'category_id', data);
								syamValidationServer('[name="type_id"]', 'type_id', data);
								syamValidationServer('[name="name"]', 'name', data);
								syamValidationServer('[name="layout"]', 'layout', data);
								syamValidationServer('[name="font"]', 'font', data);
								syamValidationServer('[name="font_body"]', 'font_body', data);
								syamValidationServer('[name="color_primary"]', 'color_primary', data);
								syamValidationServer('[name="color_secondary"]', 'color_secondary', data);
								syamValidationServer('[name="color_tertiary"]', 'color_tertiary', data);
								syamValidationServer('[name="color_quaternary"]', 'color_quaternary', data);
								syamValidationServer('[name="background"]', 'background', data);
								syamValidationServer('[name="thumbnail"]', 'thumbnail', data);
								syamValidationServer('[name="thumbnail_xs"]', 'thumbnail_xs', data);
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
				syamValidationSelectRemove(this);
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

					var background = ``;
					if (v.url_background !== null) {
						background = `
							<a data-fancybox data-src="${v.url_background}" data-caption="${v.name}">
								<img src="${v.url_background}" class="shadow" style="border-radius:1rem" width="100px" height="120px">
							</a>
						`;
					}
					var thumbnail = ``;
					if (v.url_thumbnail !== null) {
						thumbnail = `
							<a data-fancybox data-src="${v.url_thumbnail}" data-caption="${v.name}">
								<img src="${v.url_thumbnail}" class="shadow" style="border-radius:1rem" width="120px" height="80px">
							</a>
						`;
					}
					var thumbnail_xs = ``;
					if (v.url_thumbnail_xs !== null) {
						thumbnail_xs = `
							<a data-fancybox data-src="${v.url_thumbnail_xs}" data-caption="${v.name}">
								<img src="${v.url_thumbnail_xs}" class="shadow" style="border-radius:1rem" width="80px" height="120px">
							</a>
						`;
					}

					var html = '<tr>' +
						'<td class="center">' + no + '</td>' +
						'<td>' + v.name + '</td>' +
						'<td>' + v.category + '</td>' +
						'<td>' + v.type + '</td>' +
						'<td class="center">' + background + '</td>' +
						'<td class="center">' + thumbnail + '</td>' +
						'<td class="center">' + thumbnail_xs + '</td>' +
						'<td class="center">' + (v.is_premium == 1 ? '<i class="fas fa-check"></i>' : '') + '</td>' +
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
		syamValidationSelectRemove('.form-select');
		$('.background_preview').hide();
		$('.thumbnail_preview').hide();
		$('.thumbnail_xs_preview').hide();
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
					$('[name="category_id"]').val(data.data.category_id);
					$('[name="type_id"]').val(data.data.type_id);
					$('[name="name"]').val(data.data.name);
					$('[name="description"]').val(data.data.description);
					$('[name="layout"]').val(data.data.layout);

          var styles = JSON.parse(data.data.styles);
          $('[name="font"]').val(styles.font);
					$('[name="font_body"]').val(styles.font_body);
					$('[name="color_primary"]').val(styles.colors.primary);
					$('[name="color_secondary"]').val(styles.colors.secondary);
					$('[name="color_tertiary"]').val(styles.colors.tertiary);
					$('[name="color_quaternary"]').val(styles.colors.quaternary);

					if (data.data.is_premium == 1) {
						$('#is_premium').prop('checked', true);
					} else {
						$('#is_premium').prop('checked', false);
					}
					
					$('[name="background_old"]').val(data.data.background);
					if (data.data.url_background !== null) {
						$('.background_preview').show();
						$('#background_preview').attr('src', data.data.url_background)
					}

					$('[name="thumbnail_old"]').val(data.data.thumbnail);
					if (data.data.url_thumbnail !== null) {
						$('.thumbnail_preview').show();
						$('#thumbnail_preview').attr('src', data.data.url_thumbnail)
					}

					$('[name="thumbnail_xs_old"]').val(data.data.thumbnail_xs);
					if (data.data.url_thumbnail_xs !== null) {
						$('.thumbnail_xs_preview').show();
						$('#thumbnail_xs_preview').attr('src', data.data.url_thumbnail_xs)
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