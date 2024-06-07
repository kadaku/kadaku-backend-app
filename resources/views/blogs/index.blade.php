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
								<th class="center" width="10%">Featured Image</th>
								<th width="50%">Title</th>
								<th width="15%">Written By</th>
								<th width="5%">Status</th>
								<th width="5%">Publish</th>
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
					<div class="form-group row mb-0">
						<label class="form-label col-md-2">Name <span class="text-danger">*)</span></label>
						<div class="col-md-6">
							<input type="text" name="name" maxlength="200" class="form-control validate" placeholder="Title ...">
						</div>
					</div>
					<div class="form-group row mb-2">
						<label class="form-label col-md-2"></label>
						<div class="col-md-10">
							<em><small style="padding:0 12px" class="text-muted">You have entered <span id="total_characters_title" class="bold">0</span> characters / <span id="total_words_title" class="bold">0</span> words.</small></em>
						</div>
					</div>
					<div class="form-group row mb-2">
						<label class="form-label col-md-2">Topic</label>
						<div class="col-md-4">
							<input type="text" name="topic" maxlength="200" class="form-control validate" autocomplete="off" placeholder="Topic ...">
						</div>
					</div>
					<div class="form-group row mb-0">
						<label class="form-label col-md-2">Intro <span class="text-danger">*)</span></label>
						<div class="col-md-8">
              <textarea name="intro" maxlength="160" class="form-control validate" rows="3" placeholder="Intro ..."></textarea>
						</div>
					</div>
          <div class="form-group row mb-2">
						<label class="form-label col-md-2"></label>
						<div class="col-md-10">
              <em><small style="padding:0 12px" class="text-muted">You have entered <span id="total_characters_intro" class="bold">0</span> characters / You only have <b>160</b> characters.</small></em>
						</div>
					</div>
          <div class="form-group row mb-2">
						<label class="form-label col-md-2">Content</label>
						<div class="col-md-10">
              <textarea id="content" rows="3" placeholder="Content ..."></textarea>
						</div>
					</div>
          <div class="form-group row mb-0">
						<label class="form-label col-md-2">Source From</label>
						<div class="col-md-4">
							<input type="text" name="source" maxlength="255" class="form-control validate" autocomplete="off" placeholder="Source ...">
						</div>
					</div>
          <div class="form-group row mb-2">
						<label class="form-label col-md-2"></label>
						<div class="col-md-10">
              <em><small style="padding:0 12px" class="text-muted">Fill in if the news comes from other media.</small></em>
						</div>
					</div>
          <div class="form-group row mb-2">
						<label class="form-label col-md-2">Written By</label>
						<div class="col-md-4">
							<input type="text" name="written_by" maxlength="200" class="form-control validate" autocomplete="off" placeholder="Written By ..." disabled>
						</div>
					</div>
          <div class="form-group row mb-0">
						<label class="form-label col-md-2">Tags</label>
						<div class="col-md-8">
							<input type="text" name="tags" maxlength="255" class="form-control validate" autocomplete="off" placeholder="Tags ...">
						</div>
					</div>
          <div class="form-group row mb-2">
						<label class="form-label col-md-2"></label>
						<div class="col-md-10">
              <em><small style="padding:0 12px" class="text-muted">Maximal <b>20</b> Tag.</small></em>
						</div>
					</div>
					<div class="form-group row mb-0">
						<label class="form-label col-md-2">Featured Image</label>
						<div class="col-md-6">
							<input type="hidden" name="featured_image_old">
							<input type="file" name="featured_image" class="form-control validate" accept=".jpg, .jpeg, .png">
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
					<div class="form-group row mb-2 featured_image_preview">
						<label class="form-label col-md-2"></label>
						<div class="col-md-8">
							<img id="featured_image_preview" width="200" class="img-fluid rounded d-block img-thumbnail">
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
	var className = '/blogs';

	$(function() {
		getListData();

		$('#keyword_search').keyup(function() {
			getListData();
		});

		$('#btn_add').click(function() {
			resetForm();
			tinyMCE.activeEditor.setContent('');
			$('#modal_form').modal('show');
			$('#modal_form_label').text('Form Add Data');
		});

		$('#btn_reload').click(function() {
			resetForm();
			getListData();
		});

		$('[name="name"]').keyup(function() {
			countWords(this, '#total_words_title');
			countCharacters(this, '#total_characters_title');
		});

		$('[name="intro"]').keyup(function() {
			countCharacters(this, '#total_characters_intro');
		});

		$('[name="featured_image"]').change(function() {
			if (this.files[0].size > maxFileSize) {
				$('[name="featured_image"]').val('');
				$('#featured_image_preview').attr('src', '');
				swalAlert('warning', 'Information', 'Image size cannot be more than 2 mb');
				return false;
			} else {
				$('.featured_image_preview').show();
				$('#featured_image_preview').attr('src', URL.createObjectURL(this.files[0]));
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
					data.append('content', tinyMCE.get('content').getContent());

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
								syamValidationServer('[name="intro"]', 'intro', data);
								syamValidationServer('[name="written_by"]', 'written_by', data);
								syamValidationServer('[name="featured_image"]', 'featured_image', data);
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

		$('#btn_sync_music').click(function() {
			$.ajax({
				type: 'GET',
				url: baseUrl + className + '/sync-musics',
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
		});

		$('#btn_sync_featured_image').click(function() {
			$.ajax({
				type: 'GET',
				url: baseUrl + className + '/sync-file-musics',
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

					var status_publish = `
						<div class="form-check form-switch">
							<input class="form-check-input me-1" type="checkbox" onclick="updateStatusPublish(${v.id}, '${v.is_publish}')" ${(v.is_publish == 1 ? 'checked' : '')}>
							<label class="form-check-label">${(v.is_publish === 1 ? '<span class="badge bg-success">Publish</span>' : '<span class="badge bg-danger">Unpublish</span>')}</label>
						</div>
					`;

					var featured_image = ``;
					if (v.url_featured_image !== null) {
						featured_image = `
							<a data-fancybox data-src="${v.url_featured_image}" data-caption="${v.name}">
								<img src="${v.url_featured_image}" class="shadow" style="border-radius:1rem" width="150px" height="80px">
							</a>
						`;
					}

					var html = '<tr>' +
						'<td class="center">' + no + '</td>' +
						'<td class="center nowrap">' + featured_image + '</td>' +
						'<td><a href=""><b>' + v.name + '</b></a><br><span class="text-muted"><small>' + (v.intro != null ? v.intro : '-') + '</small></span></td>' +
						'<td><small>' + (v.written_by != null ? v.written_by : '-') + '</small></td>' +
						'<td class="nowrap">' + status + '</td>' +
						'<td class="nowrap">' + status_publish + '</td>' +
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
		tinymcePlugin('#content', useDarkMode);
		$('.validate, #keyword_search').val('');
		$('[name="written_by"]').val('<?php echo auth()->user()->name ?>');
		$('.form-control').prop('readonly', false);
		syamValidationRemove('.form-control');
		$('.featured_image_preview').hide();
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

	function updateStatusPublish(id, status) {
		$.ajax({
			type: 'POST',
			url: baseUrl + className + '/update-publish',
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
					if (data.data.is_publish == 1) {
						$('[name="name"]').prop('readonly', true);
					} else {
						$('[name="name"]').prop('readonly', false);
					}

					$('[name="id"]').val(data.data.id);
					$('[name="name"]').val(data.data.name);
					$('[name="topic"]').val(data.data.topic);
					$('[name="intro"]').val(data.data.intro);
					$('[name="content"]').val(data.data.content);
					$('[name="source"]').val(data.data.source);
					$('[name="written_by"]').val(data.data.written_by);
					$('[name="tags"]').val(data.data.tags);
					setTimeout(() => {
						tinyMCE.activeEditor.setContent(data.data.content);
					}, 500);

					$('[name="featured_image_old"]').val(data.data.featured_image);
					if (data.data.url_featured_image !== null) {
						$('.featured_image_preview').show();
						$('#featured_image_preview').attr('src', data.data.url_featured_image)
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

	function toggleAudio(id) {
		var audioElement = document.getElementById('player' + id)
		var soundOn = document.getElementById('play' + id)
		var soundOff = document.getElementById('pause' + id)

		if (audioElement.paused) {
			audioElement.play();
			$(soundOn).show();
			$(soundOff).hide();
		} else {
			audioElement.pause();
			$(soundOn).hide();
			$(soundOff).show();
		}
	} 
</script>
@endsection