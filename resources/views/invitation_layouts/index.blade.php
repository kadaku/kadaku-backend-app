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
								<th width="15%">Category</th>
								<th width="40%">Title</th>
								<th class="center" width="5%">Icon</th>
								<th class="center" width="5%">Premium</th>
								<th class="center" width="5%">Order</th>
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
	<div class="modal-dialog" style="min-width: 100%">
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
						<div class="col-md-9">
							<div class="form-group row mb-2">
								<label class="form-label col-md-2">Category <span class="text-danger">*)</span></label>
								<div class="col-md-10">
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
								<label class="form-label col-md-2">Title <span class="text-danger">*)</span></label>
								<div class="col-md-10">
									<input type="text" name="title" maxlength="200" class="form-control validate" placeholder="Title ...">
								</div>
							</div>
							<div class="form-group row mb-2">
								<label class="form-label col-md-2">Body HTML <span class="text-danger">*)</span></label>
								<div class="col-md-10">
									<textarea name="body" id="body" class="form-control validate code_editor" rows="6" placeholder="Body HTML ..."></textarea>
								</div>
							</div>
							<div class="form-group row mb-2">
								<label class="form-label col-md-2">Icon <span class="text-danger">*)</span></label>
								<div class="col-md-10">
									<button class="btn btn-primary search_phosphor_icons mb-2" type="button"><i class="bx bx-search-alt"></i> Search Icon</button>
									<div class="input-group">
										<span class="input-group-text"><i id="icon_phosphor_icons" style="font-size: 50px;"></i></span>
										<textarea name="icon" class="form-control input-group input_phosphor_icons validate" rows="5" placeholder="Icon ..."></textarea>
									</div>
								</div>
							</div>
							<div class="form-group row mb-2 mt-3">
								<label class="form-label col-md-2">Is Premium</label>
								<div class="col-md-10">
									<div class="form-check">
										<input name="is_premium" class="form-check-input" type="checkbox" id="is_premium" checked="checked" value="1">
										<label class="form-check-label" for="is_premium">
											Yes
										</label>
									</div>
								</div>
							</div>
							<div class="form-group row mb-2">
								<label class="form-label col-md-2">Order </label>
								<div class="col-md-2">
									<input type="number" name="order" maxlength="100" value="1" class="form-control validate" placeholder="Order ...">
								</div>
							</div>
							<div class="form-group row mb-0">
								<label class="form-label col-md-2">Image</label>
								<div class="col-md-10">
									<input type="hidden" name="image_old">
									<input type="file" name="image" class="form-control validate" accept=".jpg, .jpeg, .png">
								</div>
							</div>
							<div class="form-group row mb-2">
								<label class="form-label col-md-2"></label>
								<div class="col-md-10">
									<small class="text-danger">
										<em>*) Image type must .jpg, .jpeg, .png Maximum size 2 MB.</em>
									</small>
								</div>
							</div>
							<div class="form-group row mb-2 image_preview">
								<label class="form-label col-md-2"></label>
								<div class="col-md-10">
									<img id="image_preview" width="200" class="img-fluid rounded d-block img-thumbnail">
								</div>
							</div>
						</div>
						<div class="col-md-3 d-flex justify-content-center">
							<style>
								.editor-wrapper {
									background-position: 50%;
									background-size: cover;
									height: 832px;
									overflow: hidden;
									position: relative;
									width: 468px;
								}

								.editor-wrapper .editor {
										height: 736px;
										padding: 30px;
										position: absolute;
										width: 414px;
								}
							</style>

							<div class="editor-wrapper rounded" style="background-color:rgb(245, 245, 245); width: 468px; height: 832px; zoom: 0.6">
								<div class="panzoom position-absolute h-100 w-100">
									<div class="h-100 w-100 d-flex align-items-center justify-content-center">
										<div id="preview-layout" class="editor">
											
										</div>
									</div>
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

@include('icons.phospor.modal.index')

<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/codemirror/5.52.2/codemirror.min.css">
<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/codemirror/5.52.2/theme/material-ocean.css"></link>
<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/4.7.0/css/font-awesome.min.css">
<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/codemirror/5.52.2/addon/hint/show-hint.css">
<script src="https://cdnjs.cloudflare.com/ajax/libs/codemirror/5.52.2/codemirror.min.js"></script>
<script src="https://cdnjs.cloudflare.com/ajax/libs/codemirror/5.52.2/mode/xml/xml.js"></script>
<script src="https://cdnjs.cloudflare.com/ajax/libs/codemirror/5.52.2/mode/javascript/javascript.js"></script>
<script src="https://cdnjs.cloudflare.com/ajax/libs/codemirror/5.52.2/mode/css/css.js"></script>
<script src="https://cdnjs.cloudflare.com/ajax/libs/codemirror/5.52.2/mode/htmlmixed/htmlmixed.js"></script>
<script src="https://cdnjs.cloudflare.com/ajax/libs/codemirror/5.52.2/addon/edit/matchbrackets.js"></script>
<script src="https://cdnjs.cloudflare.com/ajax/libs/codemirror/5.52.2/addon/hint/show-hint.js"></script>
<script src="https://cdnjs.cloudflare.com/ajax/libs/codemirror/5.52.2/addon/hint/javascript-hint.js"></script>
<script src="https://cdnjs.cloudflare.com/ajax/libs/codemirror/5.52.2/addon/hint/html-hint.js"></script>
<script src="https://cdnjs.cloudflare.com/ajax/libs/codemirror/5.52.2/addon/hint/xml-hint.js"></script>
<script src="https://cdnjs.cloudflare.com/ajax/libs/codemirror/5.52.2/addon/hint/css-hint.js"></script>
<script src="https://cdnjs.cloudflare.com/ajax/libs/codemirror/5.52.2/keymap/sublime.js"></script>
<style>
  .CodeMirror {
    height: 500px;
		background-color: rgb(241, 241, 241);
  }
</style>
<script>
  var currentEditable;
  var dataSection;
  var configCodeEditor = {
    mode: "text/html",
    extraKeys: {
      "Ctrl-Space": "autocomplete"
    },
    autoRefresh: true,
    keyMap: "sublime",
    tabSize: 4,
    lineNumbers: true, 
    indentWithTabs: true,
    matchBrackets: true,
  }
  var codeEditor;
	var myTimeoutId = null;
</script>

<script src="{{ asset('extend/js/code-editor-shortcut-keys.js') }}"></script>

<script>
	var maxFileSize = 5 * 1024 * 1024;
	var className = '/layouts';

	function loadHtml(html) {
		const document_pattern = /( )*?document\./i;
		let finalHtml = html.replace(document_pattern, "document.getElementById('preview-layout').contentWindow.document.");
		$('#preview-layout').html(finalHtml);
	}

	loadHtml($('#body').val());

	$(function() {
		$('#modal_form').on('shown.bs.modal', function () {
			codeEditor = CodeMirror.fromTextArea(document.getElementById('body'), configCodeEditor);
			codeEditor.refresh();
			codeEditor.focus();

			codeEditor.on('change', function (cMirror) {
				if (myTimeoutId !== null) {
					clearTimeout(myTimeoutId);
				}
				myTimeoutId = setTimeout(function () {
					try {
						loadHtml(cMirror.getValue());
					} catch (err) {
						console.log('err:' + err);
					}
				}, 1000);
			});
		});

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
				$('[name="order"]').val($(this).val());
			} else {
				$('[name="title"]').val('');
				$('[name="order"]').val('1');
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
								syamValidationServer('[name="category_layout_id"]', 'category_layout_id', data);
								syamValidationServer('[name="title"]', 'title', data);
								syamValidationServer('[name="icon"]', 'icon', data);
								syamValidationServer('[name="body"]', 'body', data);
								syamValidationServer('[name="image"]', 'image', data);
								syamValidationServer('[name="order"]', 'order', data);
								return false;
							}

							if (data.status) {
								$('#modal_form').modal('hide');
								var page = $('#page_list').val();
								getListData(page);
								resetForm();
								editData(data.data.id, page)
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

					var image = ``;
					if (v.url_image !== null) {
						image = `
							<a data-fancybox data-src="${v.url_image}" data-caption="${v.name}">
								<img src="${v.url_image}" class="shadow" style="border-radius:0.5rem" width="45px" height="70px">
							</a>
						`;
					}

					var premium = `
						<svg width="22" height="19" viewBox="0 0 22 19" fill="none" xmlns="http://www.w3.org/2000/svg">
							<path d="M10.9997 5.8022L6.05467 8.43956L1.39819 5.8022L3.44479 15.2967H18.5551L20.6017 5.8022L15.9448 8.43956L10.9997 5.8022Z" fill="#FDCB5A"/>
							<path fill-rule="evenodd" clip-rule="evenodd" d="M2.61536 18C2.61536 17.4477 3.06307 17 3.61536 17H18.3846C18.9369 17 19.3846 17.4477 19.3846 18C19.3846 18.5523 18.9369 19 18.3846 19H3.61536C3.06307 19 2.61536 18.5523 2.61536 18Z" fill="#FDCB5A"/>
							<path d="M2 4C3.10457 4 4 3.10457 4 2C4 0.895431 3.10457 0 2 0C0.895431 0 0 0.895431 0 2C0 3.10457 0.895431 4 2 4Z" fill="#FDCB5A"/>
							<path d="M19.7803 4C20.8848 4 21.7803 3.10457 21.7803 2C21.7803 0.895431 20.8848 0 19.7803 0C18.6757 0 17.7803 0.895431 17.7803 2C17.7803 3.10457 18.6757 4 19.7803 4Z" fill="#FDCB5A"/>
							<path d="M11 4C12.1046 4 13 3.10457 13 2C13 0.895431 12.1046 0 11 0C9.89543 0 9 0.895431 9 2C9 3.10457 9.89543 4 11 4Z" fill="#FDCB5A"/>
						</svg>
					`;

					var html = '<tr>' +
						'<td class="center">' + no + '</td>' +
						'<td class="center nowrap">' + image + '</td>' +
						'<td>' + v.category + '</td>' +
						'<td>' + v.title + '</td>' +
						'<td class="center text-muted">' + v.icon + '</td>' +
						'<td class="center">' + (v.is_premium == 1 ? premium : '') + '</td>' +
						'<td class="center">' + v.order + '</td>' +
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
		$('#icon_phosphor_icons').removeClass();
		$('.image_preview').hide();
		$('#preview-layout').empty()
		$('.code_editor').val('')
		$('.CodeMirror').remove()
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
				// showLoader();
				$('#page_list').val(page);
			},
			success: function(data) {
				if (data.status) {
					$('[name="id"]').val(data.data.id);
					$('[name="category_layout_id"]').val(data.data.category_layout_id);
					$('[name="title"]').val(data.data.title);
					$('[name="body"]').val(data.data.body);
					$('[name="order"]').val(data.data.order);
					
					$('[name="icon"]').val(data.data.icon);
					$('#icon_phosphor_icons').removeClass();
					$('#icon_phosphor_icons').addClass(data.data.icon);

					$('#preview-layout').html(data.data.body);

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
				// hideLoader();
				codeEditor.refresh();
				codeEditor.focus();
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

	function chooseIcons(el) {
		var id = $(el).attr('data-id') - 1;
		var iconBase = $(el).children().children()[0];
		var iconBase2 = $(iconBase)[0].children[0].className;
		var splitIconName = iconBase2.split(' ');
		
		$('#icon_phosphor_icons').removeClass();
		$('#icon_phosphor_icons').addClass(splitIconName[3] + ' ' + splitIconName[4]);
		$('.input_phosphor_icons').val(splitIconName[3] + ' ' + splitIconName[4]);
		$('#modal_phospor_icons').modal('hide');
	}
</script>
@endsection