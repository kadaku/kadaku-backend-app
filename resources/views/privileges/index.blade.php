@extends('layouts.admin.index')
@section('title', 'User Group & Privileges')

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
								<th class="center" width="10%">ID</th>
								<th class="center" width="25%">Date & Time</th>
								<th width="65%">Name Group</th>
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

<div class="modal fade" id="modal_form" data-bs-backdrop="static" tabindex="-1">
	<div class="modal-dialog">
		<div class="modal-content">
			<form action="" id="form_add">
				<div class="modal-header">
					<h5 class="modal-title" id="modal_form_label"><strong>Form Add Data & Setting Privileges</strong></h5>
					<button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
				</div>
				<div class="modal-body">
					<input type="hidden" name="id" class="validate">
					<div class="form-group mb-2">
						<label class="form-label">Name Group <span class="text-danger">*)</span></label>
						<input type="text" name="name" class="form-control validate" placeholder="Name User Group ...">
					</div>

					<!-- privileges list -->
					<div id="privileges_area"></div>
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
	var className = '/privileges';

	$(function() {
		getListData(1);

		$('#keyword_search').keyup(function() {
			getListData(1);
		});

		$('#btn_add').click(function() {
			resetForm();
			getListDataPrivileges();
			$('#modal_form').modal('show');
			$('#modal_form_label').text('Form Add Data & Setting Privileges');
		});

		$('#btn_reload').click(function() {
			resetForm();
			getListData(1);
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
				confirmButtonText: '<i class="bx bx-paper-plane"></i> Yes',
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
								return false;
							}

							if (data.status) {
								$('#modal_form').modal('hide');
								resetForm();
								getListData($('#page_list').val());
								toastrAlert('success', 'Success', data.message);
								setTimeout(location.reload(), 6000);
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

	function getListData(page) {
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
					var html = '<tr>' +
						'<td class="center">' + no + '</td>' +
						'<td class="center">' + v.id + '</td>' +
						'<td class="center">' + dateTimeIndoNew(v.created_at) + '</td>' +
						'<td>' + v.name + '</td>' +
						'<td class="right nowrap">' +
						'<button type="button" class="btn btn-success btn-sm" onclick="editData(' + v.id + ', ' + data.data.page + ')"><i class="bx bx-edit"></i></button> ' +
						'<button type="button" class="btn btn-danger btn-sm" onclick="deleteData(' + v.id + ', ' + data.data.page + ')"><i class="bx bx-trash"></i></button>' +
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
		$('#icon_boxicons').removeClass();
		$('.validate, #keyword_search').val('');
		syamValidationRemove('.form-control');
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

					getListDataPrivileges(data.data.id);
					$('#modal_form').modal('show');
					$('#modal_form_label').text('Form Update Data & Setting Privileges');
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
		})
	}

	function deleteData(id, page) {
		Swal.fire({
			title: 'Konfirmasi',
			text: 'Apakah anda yakin ingin mengapus data ini?',
			icon: 'question',
			showCancelButton: true,
			confirmButtonColor: '#FF6E31',
			cancelButtonColor: '#6c757d',
			cancelButtonText: '<i class="bx bx-x-circle"></i> Tutup',
			confirmButtonText: '<i class="bx bx-paper-plane"></i> Ya',
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
							toastrAlert('success', 'Berhasil', data.message);
						} else {
							toastrAlert('warning', 'Validasi', data.message);
						}
					},
					complete: function() {
						hideLoader();
					},
					error: function(e) {
						toastrAlert('error', e.status, e.statusText);
					}
				})
			}
		})
	}

	function getListDataPrivileges(id = '') {
		$.ajax({
			type: 'GET',
			url: baseUrl + className + '/list-privileges',
			data: 'id=' + id,
			cache: false,
			dataType: 'JSON',
			beforeSend: function() {

			},
			success: function(data) {
				var html = `
					<div class="row mb-2">
						<div class="col-md-12">
							<button type="button" class="btn btn-primary btn-sm btn-block me-1" onclick="checkAll()"><i class="bx bxs-checkbox-checked"></i> Check All</button>
							<button type="button" class="btn btn-danger btn-sm btn-block" onclick="unCheckAll()"><i class="bx bxs-checkbox"></i> Uncheck All</button>
						</div>
					</div>
					<div class="table-responsive">
						<table id="table_privileges" class="table table-striped">
							<thead class="table-light">
								<tr>
									<th>All Menus</th>
								</tr>
							</thead>
							<tbody></tbody>
						</table>
					</div>
				`;

				$('#privileges_area').html(html);
				$('#table_privileges tbody').empty();

				if (id == '' || id == null || id == undefined) {
					var list = `<tr><td><ul class="tree mt-2" id="tree">`;
					for (let i = 0; i < data.data.all_menus.length; i++) {
						if (data.data.all_menus[i].parent_id == null) {
							list += `
								<li class="form-check" style="padding:5px 0.5rem">
									<input type="checkbox" name="menu_id[]" id="check_menu_${i}" onclick="checkParent(this)" value="${data.data.all_menus[i].id}" class="form-check-input check me-1">
									<label class="form-check-label" for="check_menu_${i}"><b><i class="${data.data.all_menus[i].icon}"></i>&nbsp;&nbsp;${data.data.all_menus[i].name}</b></label>
									<ul style="padding-left:0.9rem">`;
							for (let j = 0; j < data.data.all_menus.length; j++) {
								if (data.data.all_menus[j].parent_id == data.data.all_menus[i].id) {
									list += `
													<li class="form-check" style="padding:5px 0.5rem">
														<input type="checkbox" name="menu_id[]" id="check_menu_${j}" onclick="checkParent(this)" value="${data.data.all_menus[j].id}" class="form-check-input check me-1">
														<label class="form-check-label" for="check_menu_${j}"><i class="${(data.data.all_menus[j].icon !== '' ? data.data.all_menus[j].icon : 'bx bx-right-arrow-alt')}"></i>&nbsp;&nbsp;${data.data.all_menus[j].name}</label>
														<ul style="padding-left:0.9rem">`;
									for (let k = 0; k < data.data.all_menus.length; k++) {
										if (data.data.all_menus[k].parent_id == data.data.all_menus[j].id) {
											list += `<li class="form-check" style="padding:5px 0.5rem">
																			<input type="checkbox" name="menu_id[]" id="check_menu_${k}" onclick="checkParent(this)" value="${data.data.all_menus[k].id}" class="form-check-input check me-1">
																			<label class="form-check-label" for="check_menu_${k}"><i class="${(data.data.all_menus[k].icon !== '' ? data.data.all_menus[k].icon : 'bx bx-right-arrow-alt')}"></i>&nbsp;&nbsp;${data.data.all_menus[k].name}</label>
																		</li>`;
										}
									}
									list += `</ul>
													</li>`;
								}
							}
							list += `</ul>
								</li>`;
						}
					}
					list += `</ul></td></tr>`;
					$('#table_privileges tbody').append(list);
				} else {
					var list = `<tr><td><ul class="tree mt-2" id="tree">`;
					for (let i = 0; i < data.data.all_menus.length; i++) {
						if (data.data.all_menus[i].parent_id == null) {
							var checked = '';
							for (let isub = 0; isub < data.data.all_menus_with_group.length; isub++) {
								if (data.data.all_menus[i].id == data.data.all_menus_with_group[isub].id) {
									checked = 'checked="checked"';
								}
							}
							list += `
								<li class="form-check" style="padding:5px 0.5rem">
									<input type="checkbox" name="menu_id[]" id="check_menu_${i}" onclick="checkParent(this)" value="${data.data.all_menus[i].id}" class="form-check-input check me-1" ${checked}>
									<label class="form-check-label" for="check_menu_${i}"><b><i class="${data.data.all_menus[i].icon}"></i>&nbsp;&nbsp;${data.data.all_menus[i].name}</b></label>
									<ul style="padding-left:0.9rem">`;
							for (let j = 0; j < data.data.all_menus.length; j++) {
								if (data.data.all_menus[j].parent_id == data.data.all_menus[i].id) {
									var checked = '';
									for (let jsub = 0; jsub < data.data.all_menus_with_group.length; jsub++) {
										if (data.data.all_menus[j].id == data.data.all_menus_with_group[jsub].id) {
											checked = 'checked="checked"';
										}
									}
									list += `
													<li class="form-check" style="padding:5px 0.5rem">
														<input type="checkbox" name="menu_id[]" id="check_menu_${j}" onclick="checkParent(this)" value="${data.data.all_menus[j].id}" class="form-check-input check me-1" ${checked}>
														<label class="form-check-label" for="check_menu_${j}"><i class="${(data.data.all_menus[j].icon !== '' ? data.data.all_menus[j].icon : 'bx bx-right-arrow-alt')}"></i>&nbsp;&nbsp;${data.data.all_menus[j].name}</label>
														<ul style="padding-left:0.9rem">`;
									for (let k = 0; k < data.data.all_menus.length; k++) {
										if (data.data.all_menus[k].parent_id == data.data.all_menus[j].id) {
											var checked = '';
											for (let ksub = 0; ksub < data.data.all_menus_with_group.length; ksub++) {
												if (data.data.all_menus[k].id == data.data.all_menus_with_group[ksub].id) {
													checked = 'checked="checked"';
												}
											}
											list += `
																		<li class="form-check" style="padding:5px 0.5rem">
																			<input type="checkbox" name="menu_id[]" id="check_menu_${k}" onclick="checkParent(this)" value="${data.data.all_menus[k].id}" class="form-check-input check me-1" ${checked}>
																			<label class="form-check-label" for="check_menu_${k}"><i class="${(data.data.all_menus[k].icon !== '' ? data.data.all_menus[k].icon : 'bx bx-right-arrow-alt')}"></i>&nbsp;&nbsp;${data.data.all_menus[k].name}</label>
																		</li>
																	`;
										}
									}

									list += `</ul>
													</li>
												`;
								}
							}
							list += `</ul>
								</li>`;
						}
					}
					list += `</ul></td></tr>`;
					$('#table_privileges tbody').append(list);
				}
			},
			complete: function() {

			},
			error: function(e) {
				toastrAlert('error', e.status, e.statusText);
			}
		})
	}

	function checkAll() {
		$('.check').each(function() {
			$(this).attr('checked', 'checked');
		})
	}

	function unCheckAll() {
		$('.check').each(function() {
			$(this).removeAttr('checked', 'checked');
		})
	}

	function checkParent(el) {
		if (el.checked) {
			$(el).parents('li').children('input[type=checkbox]').prop('checked', true);
		}
		$(el).parent().find('input[type=checkbox]').prop('checked', el.checked);
	}
</script>
@endsection