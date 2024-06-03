@extends('layouts.admin.index')
@section('title', $title)

@section('content')
<div class="card shadow radius-10">
	<div class="card-header">
		<div class="row">
			<div class="col-md-6">
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
								<th class="center" width="5%">Avatar</th>
								<th width="25%">Fullname</th>
								<th width="15%">Email</th>
								<th width="15%">Phone</th>
								<th width="20%">Address</th>
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

<script>
	var className = '/customers';
	var avatarDefault = '<?php echo asset("storage/images/accounts/avatar.png") ?>';

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
					var status = `<div class="form-check form-switch">
									<input class="form-check-input me-1" type="checkbox" onclick="updateStatus(${v.id}, '${v.is_active}')" ${(v.is_active == 1 ? 'checked' : '')}>
									<label class="form-check-label">${(v.is_active === 1 ? '<span class="badge bg-success">Active</span>' : '<span class="badge bg-danger">Unactive</span>')}</label>
								</div>`;

					var avatar = `<img src="${avatarDefault}" class="rounded-circle border shadow" width="34" height="34">`;
					if (v.url_photo !== null) {
						avatar = `<a data-fancybox data-src="${v.url_photo}" data-caption="${v.name}">
									<img src="${v.url_photo}" class="rounded-circle shadow" width="34" height="34">
								</a>`;
					} else if (v.avatar !== null) {
            avatar = `<a data-fancybox data-src="${v.avatar}" data-caption="${v.name}">
									<img src="${v.avatar}" class="rounded-circle shadow" width="34" height="34">
								</a>`;
          }


					var html = '<tr>' +
						'<td class="center">' + no + '</td>' +
						'<td class="center">' + avatar + '</td>' +
						'<td class="nowrap">' + v.name + '</td>' +
						'<td>' + v.email + '<br><small class="text-muted">' + v.social + '</small></td>' +
						'<td>' + (v.phone != null ? v.phone : '-') + '</td>' +
						'<td>' + (v.address != null ? v.address : '-') + '</td>' +
						'<td class="nowrap">' + status + '</td>' +
						'<td class="nowrap">' + dateTimeSlash(v.created_at) + '</td>' +
						'<td class="right nowrap">' +
						'<button type="button" class="btn btn-primary btn-sm" onclick="showData(' + v.id + ', ' + data.data.page + ')"><i class="fas fa-eye"></i></button> ' +
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
		// $('#form_add')[0].reset();
		$('.validate, #keyword_search').val('');
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

	function showData(id, page) {
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
					toastrAlert('error', 'Yeay', 'Masih dalam tahap pengembangan');
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