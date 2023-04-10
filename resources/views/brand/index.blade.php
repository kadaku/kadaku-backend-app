@extends('layouts.admin.index')
@section('title', 'Brand')

@section('content')
<div class="row">
	<div class="col-md-4">
		<div class="card radius-15" style="background-color:rgb(19,31,59)">
			<div class="card-body text-center">
				<img src="" id="logo_brand" width="200" class="p-1" alt="">
				<h5 id="name_brand" class="mb-0 mt-4 text-white"></h5>
				<p id="email_brand" class="mb-0 text-white"></p>
				<p id="address_brand" class="mb-0 text-white"></p>
				<div class="list-inline contacts-social mt-3">
					<a href="" id="link_phone_brand" class="list-inline-item"><i class="bx bxs-phone"></i> <span id="phone_brand"></span></a>
				</div>
			</div>
		</div>
	</div>
	<div class="col-md-8">
		<div class="card shadow radius-10">
			<div class="card-header"><strong>Form Update Brand Website</strong></div>
			<div class="card-body">
				<form action="" id="form_add" enctype="multipart/form-data">
                    @csrf
					<input type="hidden" name="id" class="validate">
					<div class="form-group row mb-2">
						<label class="form-label col-md-3">Title <span class="text-danger">*)</span></label>
						<div class="col-md-9">
							<input type="text" name="name" class="form-control validate" placeholder="Title ...">
						</div>
					</div>
					<div class="form-group row mb-2">
						<label class="form-label col-md-3">Email</label>
						<div class="col-md-9">
							<input type="email" name="email" class="form-control validate" placeholder="Email ...">
						</div>
					</div>
					<div class="form-group row mb-2">
						<label class="form-label col-md-3">Address</label>
						<div class="col-md-9">
							<textarea name="address" class="form-control" placeholder="Address ..."></textarea>
						</div>
					</div>
					<div class="form-group row mb-2">
						<label class="form-label col-md-3">Phone</label>
						<div class="col-md-9">
							<input type="text" name="phone" max="15" class="form-control validate" placeholder="Phone ...">
						</div>
					</div>
					<div class="dashline"></div>
					<div class="form-group row mb-2">
						<label class="form-label col-md-3">Logo <span class="text-danger">*)</span></label>
						<div class="col-md-9">
							<input type="hidden" name="file_brand_old" class="validate">
							<input type="file" name="file_brand" class="form-control validate" accept=".png, .svg">
							<span class="text-danger">
								<em>*) Image type must .png, and Image size max 5 MB.</em>
							</span>
						</div>
					</div>
					<div class="form-group row mb-2">
						<label class="form-label col-md-3"></label>
						<div class="col-md-9">
							<a id="logo_brand_preview_fancy" data-fancybox data-src="">
								<img id="logo_brand_preview" class="img-thumbnail rounded" style="width:150px;height:auto;max-height:none;">
							</a>
						</div>
					</div>
					<div class="dashline"></div>
					<div class="form-group row mb-2">
						<label class="form-label col-md-3">Logo Light <span class="text-danger">*)</span></label>
						<div class="col-md-9">
							<input type="hidden" name="file_brand_light_old" class="validate">
							<input type="file" name="file_brand_light" class="form-control validate" accept=".png, .svg">
							<span class="text-danger">
								<em>*) Image type must .png, and Image size max 5 MB.</em>
							</span>
						</div>
					</div>
					<div class="form-group row mb-2">
						<label class="form-label col-md-3"></label>
						<div class="col-md-9">
							<a id="logo_brand_light_preview_fancy" data-fancybox data-src="">
								<img id="logo_brand_light_preview" class="img-thumbnail rounded" style="width:150px;height:auto;max-height:none;">
							</a>
						</div>
					</div>
					<div class="dashline"></div>
					<div class="form-group row mb-2">
						<label class="form-label col-md-3">Favicon <span class="text-danger">*)</span></label>
						<div class="col-md-9">
							<input type="hidden" name="file_brand_favicon_old" class="validate">
							<input type="file" name="file_brand_favicon" class="form-control validate" accept=".png, .svg">
							<span class="text-danger">
								<em>*) Image type must .png, and Image size max 5 MB.</em>
							</span>
						</div>
					</div>
					<div class="form-group row mb-2">
						<label class="form-label col-md-3"></label>
						<div class="col-md-9">
							<a id="logo_brand_favicon_preview_fancy" data-fancybox data-src="">
								<img id="logo_brand_favicon_preview" class="img-thumbnail rounded" style="width:70px;height:70px;max-height:none;">
							</a>
						</div>
					</div>
                    <div class="form-group row mb-2">
						<label class="form-label col-md-3">Youtube</label>
						<div class="col-md-9">
							<input type="text" name="youtube" class="form-control validate" placeholder="Youtube ...">
						</div>
					</div>
                    <div class="form-group row mb-2">
						<label class="form-label col-md-3">Facebook</label>
						<div class="col-md-9">
							<input type="text" name="facebook" class="form-control validate" placeholder="Facebook ...">
						</div>
					</div>
                    <div class="form-group row mb-2">
						<label class="form-label col-md-3">Instagram</label>
						<div class="col-md-9">
							<input type="text" name="instagram" class="form-control validate" placeholder="Instagram ...">
						</div>
					</div>
                    <div class="form-group row mb-2">
						<label class="form-label col-md-3">Twitter</label>
						<div class="col-md-9">
							<input type="text" name="twitter" class="form-control validate" placeholder="Twitter ...">
						</div>
					</div>
					<div class="row">
						<div class="col-md-12 right">
							<button type="reset" class="btn btn-secondary btn-sm btn-block me-1"><i class="bx bx-x-circle"></i> Reset</button>
							<button type="submit" class="btn btn-primary btn-sm btn-block"><i class="bx bx-save"></i> Update</button>
						</div>
					</div>
                </form>
			</div>
		</div>
	</div>
</div>

<script>
	var maxFileSize = 5 * 1024 * 1024;
	var className = '/brand';

	$(function() {
		getData();

		$('[name="file_brand"]').change(function() {
			if (this.files[0].size > maxFileSize) {
				$('[name="file_brand"]').val('');
				$('#logo_brand_preview').attr('src', '');
				$('#logo_brand_preview_fancy').attr('data-src', '');
				swalAlert('warning', 'Information', 'Image size cannot be more than 5 mb');
				return false;
			} else {
				$('#logo_brand_preview').attr('src', URL.createObjectURL(this.files[0]));
				$('#logo_brand_preview_fancy').attr('data-src', URL.createObjectURL(this.files[0]));
			}
		});

		$('[name="file_brand_light"]').change(function() {
			if (this.files[0].size > maxFileSize) {
				$('[name="file_brand_light"]').val('');
				$('#logo_brand_light_preview').attr('src', '');
				$('#logo_brand_light_preview_fancy').attr('data-src', '');
				swalAlert('warning', 'Information', 'Image size cannot be more than 5 mb');
				return false;
			} else {
				$('#logo_brand_light_preview').attr('src', URL.createObjectURL(this.files[0]));
				$('#logo_brand_light_preview_fancy').attr('data-src', URL.createObjectURL(this.files[0]));
			};
		});

		$('[name="file_brand_favicon"]').change(function() {
			if (this.files[0].size > maxFileSize) {
				$('[name="file_brand_favicon"]').val('');
				$('#logo_brand_favicon_preview').attr('src', '');
				$('#logo_brand_favicon_preview_fancy').attr('data-src', '');
				swalAlert('warning', 'Information', 'Image size cannot be more than 5 mb');
				return false;
			} else {
				$('#logo_brand_favicon_preview').attr('src', URL.createObjectURL(this.files[0]));
				$('#logo_brand_favicon_preview_fancy').attr('data-src', URL.createObjectURL(this.files[0]));
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
								// syamValidationServer('[name="email"]', 'email', data);
								syamValidationServer('[name="file_brand"]', 'file_brand', data);
								syamValidationServer('[name="file_brand_light"]', 'file_brand_light', data);
								syamValidationServer('[name="file_brand_favicon"]', 'file_brand_favicon', data);
								return false;
							}

							if (data.status) {
								getData();
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
		
		$('.validate').change(function() {
			if ($(this).val() !== '') {
				syamValidationRemove(this);
			}
		});
	});

	function getData() {
		$.ajax({
			type: 'GET',
			url: baseUrl + className + '/show/' + '<?php echo brand()->id ?>',
			cache: false,
			dataType: 'JSON',
			success: function(data) {
				if (data.status) {
					$('#name_brand').text(data.data.name);
					$('#email_brand').text(data.data.email);
					$('#address_brand').text(data.data.address);
					$('#link_phone_brand').attr('href', 'tel:' + data.data.phone);

					if (data.data.logo !== null) {
						$('#logo_brand').attr('src', data.data.logo_light);
					}

					$('[name="id"]').val(data.data.id);
					$('[name="name"]').val(data.data.name);
					$('[name="email"]').val(data.data.email);
					$('[name="address"]').val(data.data.address);
					$('[name="phone"]').val(data.data.phone);
					$('[name="youtube"]').val(data.data.youtube);
					$('[name="facebook"]').val(data.data.facebook);
					$('[name="instagram"]').val(data.data.instagram);
					$('[name="twitter"]').val(data.data.twitter);
					
					$('[name="file_brand_old"]').val(data.data.logo);
					if (data.data.logo !== null) {
						$('#logo_brand_preview').attr('src', data.data.logo);
						$('#logo_brand_preview_fancy').attr('data-src', data.data.logo);
					}
					$('[name="file_brand_light_old"]').val(data.data.logo_light);
					if (data.data.logo_light !== null) {
						$('#logo_brand_light_preview').attr('src', data.data.logo_light);
						$('#logo_brand_light_preview_fancy').attr('data-src', data.data.logo_light);
					}
					$('[name="file_brand_favicon_old"]').val(data.data.favicon);
					if (data.data.favicon !== null) {
						$('#logo_brand_favicon_preview').attr('src', data.data.favicon);
						$('#logo_brand_favicon_preview_fancy').attr('data-src', data.data.favicon);
					}
				}
			},
			error: function(e) {
				toastrAlert('error', e.status, e.statusText);
			}
		});
	}
</script>
@endsection