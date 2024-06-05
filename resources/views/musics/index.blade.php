@extends('layouts.admin.index')
@section('title', $title)

@section('content')
<div class="card shadow radius-10">
	<div class="card-header">
		<div class="row">
			<div class="col-md-8">
				<button type="button" id="btn_add" class="btn btn-primary btn-label btn-block btn-sm"><i class="fas fa-plus label-icon align-middle fs-16 me-2"></i>Add Data</button>
				<button type="button" id="btn_reload" class="btn btn-secondary btn-label btn-block btn-sm"><i class="fas fa-sync label-icon align-middle fs-16 me-2"></i>Refresh Data</button>
				<button type="button" id="btn_sync_music" class="btn btn-success btn-label btn-block btn-sm"><i class="fas fa-download label-icon align-middle fs-16 me-2"></i>Sync Musics</button>
				<button type="button" id="btn_sync_file_music" class="btn btn-warning btn-label btn-block btn-sm"><i class="fas fa-download label-icon align-middle fs-16 me-2"></i>Sync File Musics</button>
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
								<th width="30%">File</th>
								<th width="10%">Category Music</th>
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
						<label class="form-label col-md-4">Category Music <span class="text-danger">*)</span></label>
						<div class="col-md-8">
							<select name="category_music_id" id="category_music_id" class="form-select form-control">
								<option value="">-- Choose --</option>
								@foreach ($categories_musics as $row)
									<option value="{{ $row->id }}">{{ $row->name }}</option>
								@endforeach
							</select>
						</div>
					</div>
					<div class="form-group row mb-2">
						<label class="form-label col-md-4">Name <span class="text-danger">*)</span></label>
						<div class="col-md-8">
							<input type="text" name="name" class="form-control validate" placeholder="Name ...">
						</div>
					</div>
					<div class="form-group row mb-2">
						<label class="form-label col-md-4">Categories</label>
						<div class="col-md-8">
							<input type="text" name="categories" class="form-control validate" placeholder="Categories ...">
						</div>
					</div>
					<div class="form-group row mb-0">
						<label class="form-label bold col-md-4">File Mp3</label>
						<div class="col-md-8">
							<input type="hidden" name="file_music_old">
							<input type="file" name="file_music" class="form-control validate" accept=".mp3">
						</div>
					</div>
					<div class="form-group row mb-2">
						<label class="form-label bold col-md-4"></label>
						<div class="col-md-8">
							<small class="text-danger">
								<em>*) Audio type must mp3 Maximum size 10 MB.</em>
							</small>
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

<style>
	.play,
	.pause {
		width: 30px;
	}
	.play:hover,
	.pause:hover {
		cursor: pointer;
	}
	.pause {
		display: none;
	}
</style>

<link rel="stylesheet" href="{{ asset('extend/plugins/plyr/plyr.css') }}">
<script src="{{ asset('extend/plugins/plyr/plyr.js') }}"></script>

<script>
	var maxFileSize = 10 * 1024 * 1024;
	var className = '/musics';

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

		$('[name="file_music"]').change(function() {
			if (this.files[0].size > maxFileSize) {
				$('[name="file_music"]').val('');
				swalAlert('warning', 'Information', 'Image size cannot be more than 10 mb');
				return false;
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
								syamValidationServer('[name="category_music_id"]', 'category_music_id', data);
								syamValidationServer('[name="name"]', 'name', data);
								syamValidationServer('[name="file_music"]', 'file_music', data);
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

		$('#btn_sync_file_music').click(function() {
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

					var file = ``;
					if (v.url_file !== null) {
						file = `
							<audio id="plyr-${v.id}" class="js-player" crossorigin playsinline>
								<source src="${v.url_file}?dl=1" type="audio/mp3">
							</audio>
						`;
					}

					var html = '<tr>' +
						'<td class="center">' + no + '</td>' +
						'<td>' + v.name + '</td>' +
						'<td class="nowrap">' + file + '</td>' +
						'<td>' + (v.categories != null ? v.categories : '-') + '</td>' +
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
				// var controls = ["play", "progress", "duration", "mute", "volume", "download"];
				var controls = ["play", "progress", "duration", "mute"];
				const player = Plyr.setup(".js-player", { controls });

				// expose
				window.player = player;
				for (var i in player) {
					player[i].on('play', function (instance) {
						var source = instance.detail.plyr.source;
						for (var x in player) {
							if (player[x].source != source) {
								player[x].pause();
							}
						}
					});
				}
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
					$('.form_username, .form_password').hide();

					$('[name="id"]').val(data.data.id);
					$('[name="category_music_id"]').val(data.data.category_music_id);
					$('[name="name"]').val(data.data.name);
					$('[name="categories"]').val(data.data.categories);

					$('[name="file_music_old"]').val(data.data.file)

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