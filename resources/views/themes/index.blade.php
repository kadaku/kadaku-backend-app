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

<style>
	.colorbox {
    width: 50px;
    height: 40px;
    border: 1px solid lightgray;
    border-radius: 5px;
    margin-right: 5px;
	}
</style>

<div class="modal fade" id="modal_form">
	<div class="modal-dialog" style="min-width:100%;">
		<div class="modal-content">
			<div class="modal-header">
				<h5 class="modal-title" id="modal_form_label"><strong>Form Add Data</strong></h5>
				<button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
			</div>
			<div class="modal-body">
				<form action="" id="form_add" enctype="multipart/form-data">
					@csrf
				<input type="hidden" name="id" class="validate">
				<div class="row">	
					<div class="col-md-4">
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
								<select name="layout" class="form-select">
									<option value="first-layout">First Layout</option>
								</select>
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
						<div style="border-bottom: 1px dashed lightgray;margin:1rem 0;"></div>
						<div class="form-group row mb-2">
							<label class="form-label col-md-3">Font Google</label>
							<div class="col-md-9">
								<textarea type="text" name="font" class="form-control validate" rows="4" placeholder="Font ..."></textarea>
							</div>
						</div>
						<div class="form-group row mb-2">
							<label class="form-label col-md-3">Font Base</label>
							<div class="col-md-9">
								<input type="text" name="font_base" maxlength="200" class="form-control validate" placeholder="'Inter', sans-serif ...">
							</div>
						</div>
						<div class="form-group row mb-2">
							<label class="form-label col-md-3">Font Accent</label>
							<div class="col-md-9">
								<input type="text" name="font_accent" maxlength="200" class="form-control validate" placeholder="'Inter', sans-serif ...">
							</div>
						</div>
						<div class="form-group row mb-2">
							<label class="form-label col-md-3">Font Latin</label>
							<div class="col-md-9">
								<input type="text" name="font_latin" maxlength="200" class="form-control validate" placeholder="'Inter', sans-serif ...">
							</div>
						</div>
						<div class="form-group row mb-2">
							<label class="form-label col-md-3">Inv Bg</label>
							<div class="col-md-9 d-flex align-items-center">
								<div id="colorbox_inv_bg" class="colorbox"></div>
								<input type="text" name="inv_bg" maxlength="200" class="form-control validate" onkeyup="updateColorBox(this.value, 'colorbox_inv_bg')" placeholder="#000000 ...">
							</div>
						</div>
						<div class="form-group row mb-2">
							<label class="form-label col-md-3">Inv Base</label>
							<div class="col-md-9 d-flex align-items-center">
								<div id="colorbox_inv_base" class="colorbox"></div>
								<input type="text" name="inv_base" maxlength="200" class="form-control validate" onkeyup="updateColorBox(this.value, 'colorbox_inv_base')" placeholder="#000000 ...">
							</div>
						</div>
						<div class="form-group row mb-2">
							<label class="form-label col-md-3">Inv Accent</label>
							<div class="col-md-9 d-flex align-items-center">
								<div id="colorbox_inv_accent" class="colorbox"></div>
								<input type="text" name="inv_accent" maxlength="200" class="form-control validate" onkeyup="updateColorBox(this.value, 'colorbox_inv_accent')" placeholder="#000000 ...">
							</div>
						</div>
						<div class="form-group row mb-2">
							<label class="form-label col-md-3">Inv Border</label>
							<div class="col-md-9 d-flex align-items-center">
								<div id="colorbox_inv_border" class="colorbox"></div>
								<input type="text" name="inv_border" maxlength="200" class="form-control validate" onkeyup="updateColorBox(this.value, 'colorbox_inv_border')" placeholder="#000000 ...">
							</div>
						</div>
						<div class="form-group row mb-2">
							<label class="form-label col-md-3">Menu Bg</label>
							<div class="col-md-9 d-flex align-items-center">
								<div id="colorbox_menu_bg" class="colorbox"></div>
								<input type="text" name="menu_bg" maxlength="200" class="form-control validate" onkeyup="updateColorBox(this.value, 'colorbox_menu_bg')" placeholder="#000000 ...">
							</div>
						</div>
						<div class="form-group row mb-2">
							<label class="form-label col-md-3">Menu Inactive</label>
							<div class="col-md-9 d-flex align-items-center">
								<div id="colorbox_menu_inactive" class="colorbox"></div>
								<input type="text" name="menu_inactive" maxlength="200" class="form-control validate" onkeyup="updateColorBox(this.value, 'colorbox_menu_inactive')" placeholder="#000000 ...">
							</div>
						</div>
						<div class="form-group row mb-2">
							<label class="form-label col-md-3">Menu Active</label>
							<div class="col-md-9 d-flex align-items-center">
								<div id="colorbox_menu_active" class="colorbox"></div>
								<input type="text" name="menu_active" maxlength="200" class="form-control validate" onkeyup="updateColorBox(this.value, 'colorbox_menu_active')" placeholder="#000000 ...">
							</div>
						</div>
						<div class="form-group row mb-2">
							<label class="form-label col-md-3">Button Color</label>
							<div class="col-md-9 d-flex align-items-center">
								<div id="colorbox_btn_color" class="colorbox"></div>
								<input type="text" name="btn_color" maxlength="200" class="form-control validate" onkeyup="updateColorBox(this.value, 'colorbox_btn_color')" placeholder="#000000 ...">
							</div>
						</div>
					</div>
					<div class="col-md-8">
						<div class="row">
							<div class="col-lg-12">
								<div class="form-group row mb-2">
									<label class="form-label col-md-12" style="text-align:left">Music</label>
									<div class="col-md-12">
										<input type="text" name="music_id" id="music_id" class="select2">
									</div>
								</div>
							</div>
						</div>
						<div class="row">
							<div class="col-lg-5">
								{{-- thumbnail --}}
								<div class="group_thumbnail">
									<div class="form-group row mb-0">
										<label class="form-label col-md-12" style="text-align:left">Thumbnail</label>
										<div class="col-md-12">
											<input type="hidden" name="thumbnail_old">
											<div class="d-flex" style="gap:1rem">
												<button type="button" style="display:none" class="btn btn-danger btn_thumbnail"><i class="fas fa-trash-alt"></i></button>
												<div class="d-flex w-100" style="flex-direction:column">
													<input type="file" name="thumbnail" class="form-control validate" accept=".jpg, .jpeg, .png">
												</div>
											</div>
										</div>
									</div>
									<div class="form-group row mb-2">
										<label class="form-label col-md-12"></label>
										<div class="col-md-12">
											<small class="text-danger">
												<em>*) Thumbnail type must .jpg, .jpeg, .png Maximum size 2 MB.</em>
											</small>
										</div>
									</div>
									<div class="form-group row mb-2 thumbnail_preview">
										<label class="form-label col-md-12"></label>
										<div class="col-md-12">
											<img id="thumbnail_preview" width="200" class="img-fluid rounded d-block img-thumbnail">
										</div>
									</div>
								</div>
							</div>
							<div class="col-lg-7">
								{{-- thumbnail xs --}}
								<div class="group_thumbnail_xs">
									<div class="form-group row mb-0">
										<label class="form-label col-md-12" style="text-align:left">Thumb XS</label>
										<div class="col-md-12">
											<input type="hidden" name="thumbnail_xs_old">
											<div class="d-flex" style="gap:1rem">
												<button type="button" style="display:none" class="btn btn-danger btn_thumbnail_xs"><i class="fas fa-trash-alt"></i></button>
												<div class="d-flex w-100" style="flex-direction:column">
													<input type="file" name="thumbnail_xs" class="form-control validate" accept=".jpg, .jpeg, .png">
												</div>
											</div>
										</div>
									</div>
									<div class="form-group row mb-2">
										<label class="form-label col-md-12"></label>
										<div class="col-md-12">
											<small class="text-danger">
												<em>*) Thumbnail xs type must .jpg, .jpeg, .png Maximum size 2 MB.</em>
											</small>
										</div>
									</div>
									<div class="form-group row mb-2 thumbnail_xs_preview">
										<label class="form-label col-md-12"></label>
										<div class="col-md-12">
											<img id="thumbnail_xs_preview" width="100" class="img-fluid rounded d-block img-thumbnail">
										</div>
									</div>
								</div>
							</div>
						</div>
						<div class="row">
							<div class="col-lg-4">
								<div class="group_frame_top_left">
									<div class="form-group row mb-0">
										<label class="form-label col-md-12" style="text-align:left">Frame Top Left</label>
										<div class="col-md-12">
											<input type="hidden" name="frame_top_left_old">
											<div class="d-flex" style="gap:1rem">
												<button type="button" style="display:none" class="btn btn-danger btn_frame_top_left"><i class="fas fa-trash-alt"></i></button>
												<div class="d-flex w-100" style="flex-direction:column">
													<input type="file" name="frame_top_left" class="form-control validate" accept=".png">
												</div>
											</div>
										</div>
									</div>
									<div class="form-group row mb-2">
										<label class="form-label col-md-12"></label>
										<div class="col-md-12">
											<small class="text-danger">
												<em>*) Top Left type must .png Maximum size 2 MB.</em>
											</small>
										</div>
									</div>
									<div class="form-group row mb-2 frame_top_left_preview">
										<label class="form-label col-md-12"></label>
										<div class="col-md-12">
											<img id="frame_top_left_preview" width="200" class="img-fluid rounded d-block img-thumbnail">
										</div>
									</div>
								</div>
							</div>
							<div class="col-lg-4">
								<div class="group_frame_top_center">
									<div class="form-group row mb-0">
										<label class="form-label col-md-12 text-center">Frame Top Center</label>
										<div class="col-md-12">
											<input type="hidden" name="frame_top_center_old">
											<div class="d-flex" style="gap:1rem">
												<button type="button" style="display:none" class="btn btn-danger btn_frame_top_center"><i class="fas fa-trash-alt"></i></button>
												<div class="d-flex w-100" style="flex-direction:column">
													<input type="file" name="frame_top_center" class="form-control validate" accept=".png">
												</div>
											</div>
										</div>
									</div>
									<div class="form-group row mb-2">
										<label class="form-label col-md-12"></label>
										<div class="col-md-12">
											<small class="text-danger">
												<em>*) Top Center type must .png Maximum size 2 MB.</em>
											</small>
										</div>
									</div>
									<div class="form-group row mb-2 frame_top_center_preview">
										<label class="form-label col-md-12"></label>
										<div class="col-md-12">
											<img id="frame_top_center_preview" width="200" class="img-fluid rounded d-block img-thumbnail">
										</div>
									</div>
								</div>
							</div>
							<div class="col-lg-4">
								<div class="group_frame_top_right">
									<div class="form-group row mb-0">
										<label class="form-label col-md-12">Frame Top Right</label>
										<div class="col-md-12">
											<input type="hidden" name="frame_top_right_old">
											<div class="d-flex" style="gap:1rem">
												<button type="button" style="display:none" class="btn btn-danger btn_frame_top_right"><i class="fas fa-trash-alt"></i></button>
												<div class="d-flex w-100" style="flex-direction:column">
													<input type="file" name="frame_top_right" class="form-control validate" accept=".png">
												</div>
											</div>
										</div>
									</div>
									<div class="form-group row mb-2">
										<label class="form-label col-md-12"></label>
										<div class="col-md-12">
											<small class="text-danger">
												<em>*) Top Right type must .png Maximum size 2 MB.</em>
											</small>
										</div>
									</div>
									<div class="form-group row mb-2 frame_top_right_preview">
										<label class="form-label col-md-12"></label>
										<div class="col-md-12">
											<img id="frame_top_right_preview" width="200" class="img-fluid rounded d-block img-thumbnail">
										</div>
									</div>
								</div>
							</div>
						</div>
						<div class="row">
							<div class="col-lg-4">
								<div class="group_frame_side_left">
									<div class="form-group row mb-0">
										<label class="form-label col-md-12" style="text-align:left">Frame Side Left</label>
										<div class="col-md-12">
											<input type="hidden" name="frame_side_left_old">
											<div class="d-flex" style="gap:1rem">
												<button type="button" style="display:none" class="btn btn-danger btn_frame_side_left"><i class="fas fa-trash-alt"></i></button>
												<div class="d-flex w-100" style="flex-direction:column">
													<input type="file" name="frame_side_left" class="form-control validate" accept=".png">
												</div>
											</div>
										</div>
									</div>
									<div class="form-group row mb-2">
										<label class="form-label col-md-12"></label>
										<div class="col-md-12">
											<small class="text-danger">
												<em>*) Side Left type must .png Maximum size 2 MB.</em>
											</small>
										</div>
									</div>
									<div class="form-group row mb-2 frame_side_left_preview">
										<label class="form-label col-md-12"></label>
										<div class="col-md-12">
											<img id="frame_side_left_preview" width="200" class="img-fluid rounded d-block img-thumbnail">
										</div>
									</div>
								</div>
							</div>
							<div class="col-lg-4">
								<div class="group_background">
									<div class="form-group row mb-0">
										<label class="form-label col-md-12 text-center">Background</label>
										<div class="col-md-12">
											<input type="hidden" name="background_old">
											<div class="d-flex" style="gap:1rem">
												<button type="button" style="display:none" class="btn btn-danger btn_background"><i class="fas fa-trash-alt"></i></button>
												<div class="d-flex w-100" style="flex-direction:column">
													<input type="file" name="background" class="form-control validate" accept=".png, .jpg, .jpeg">
												</div>
											</div>
										</div>
									</div>
									<div class="form-group row mb-2">
										<label class="form-label col-md-12"></label>
										<div class="col-md-12">
											<small class="text-danger">
												<em>*) Background type must .png, .jpg, .jpeg Maximum size 2 MB.</em>
											</small>
										</div>
									</div>
									<div class="form-group row mb-2 background_preview">
										<label class="form-label col-md-12"></label>
										<div class="col-md-12">
											<img id="background_preview" width="200" class="img-fluid rounded d-block img-thumbnail">
										</div>
									</div>
								</div>
							</div>
							<div class="col-lg-4">
								<div class="group_frame_side_right">
									<div class="form-group row mb-0">
										<label class="form-label col-md-12">Frame Side Right</label>
										<div class="col-md-12">
											<input type="hidden" name="frame_side_right_old">
											<div class="d-flex" style="gap:1rem">
												<button type="button" style="display:none" class="btn btn-danger btn_frame_side_right"><i class="fas fa-trash-alt"></i></button>
												<div class="d-flex w-100" style="flex-direction:column">
													<input type="file" name="frame_side_right" class="form-control validate" accept=".png">
												</div>
											</div>
										</div>
									</div>
									<div class="form-group row mb-2">
										<label class="form-label col-md-12"></label>
										<div class="col-md-12">
											<small class="text-danger">
												<em>*) Side Right type must .png Maximum size 2 MB.</em>
											</small>
										</div>
									</div>
									<div class="form-group row mb-2 frame_side_right_preview">
										<label class="form-label col-md-12"></label>
										<div class="col-md-12">
											<img id="frame_side_right_preview" width="200" class="img-fluid rounded d-block img-thumbnail">
										</div>
									</div>
								</div>
							</div>
						</div>
						<div class="row">
							<div class="col-lg-4">
								<div class="group_frame_bottom_left">
									<div class="form-group row mb-0">
										<label class="form-label col-md-12" style="text-align:left">Frame Bottom Left</label>
										<div class="col-md-12">
											<input type="hidden" name="frame_bottom_left_old">
											<div class="d-flex" style="gap:1rem">
												<button type="button" style="display:none" class="btn btn-danger btn_frame_bottom_left"><i class="fas fa-trash-alt"></i></button>
												<div class="d-flex w-100" style="flex-direction:column">
													<input type="file" name="frame_bottom_left" class="form-control validate" accept=".png">
												</div>
											</div>
										</div>
									</div>
									<div class="form-group row mb-2">
										<label class="form-label col-md-12"></label>
										<div class="col-md-12">
											<small class="text-danger">
												<em>*) Bottom Left type must .png Maximum size 2 MB.</em>
											</small>
										</div>
									</div>
									<div class="form-group row mb-2 frame_bottom_left_preview">
										<label class="form-label col-md-12"></label>
										<div class="col-md-12">
											<img id="frame_bottom_left_preview" width="200" class="img-fluid rounded d-block img-thumbnail">
										</div>
									</div>
								</div>
							</div>
							<div class="col-lg-4">
								<div class="group_frame_bottom_center">
									<div class="form-group row mb-0">
										<label class="form-label col-md-12 text-center">Frame Bottom Center</label>
										<div class="col-md-12">
											<input type="hidden" name="frame_bottom_center_old">
											<div class="d-flex" style="gap:1rem">
												<button type="button" style="display:none" class="btn btn-danger btn_frame_bottom_center"><i class="fas fa-trash-alt"></i></button>
												<div class="d-flex w-100" style="flex-direction:column">
													<input type="file" name="frame_bottom_center" class="form-control validate" accept=".png">
												</div>
											</div>
										</div>
									</div>
									<div class="form-group row mb-2">
										<label class="form-label col-md-12"></label>
										<div class="col-md-12">
											<small class="text-danger">
												<em>*) Bottom Center type must .png Maximum size 2 MB.</em>
											</small>
										</div>
									</div>
									<div class="form-group row mb-2 frame_bottom_center_preview">
										<label class="form-label col-md-12"></label>
										<div class="col-md-12">
											<img id="frame_bottom_center_preview" width="200" class="img-fluid rounded d-block img-thumbnail">
										</div>
									</div>
								</div>
							</div>
							<div class="col-lg-4">
								<div class="group_frame_bottom_right">
									<div class="form-group row mb-0">
										<label class="form-label col-md-12">Frame Bottom Right</label>
										<div class="col-md-12">
											<input type="hidden" name="frame_bottom_right_old">
											<div class="d-flex" style="gap:1rem">
												<button type="button" style="display:none" class="btn btn-danger btn_frame_bottom_right"><i class="fas fa-trash-alt"></i></button>
												<div class="d-flex w-100" style="flex-direction:column">
													<input type="file" name="frame_bottom_right" class="form-control validate" accept=".png">
												</div>
											</div>
										</div>
									</div>
									<div class="form-group row mb-2">
										<label class="form-label col-md-12"></label>
										<div class="col-md-12">
											<small class="text-danger">
												<em>*) Bottom Right type must .png Maximum size 2 MB.</em>
											</small>
										</div>
									</div>
									<div class="form-group row mb-2 frame_bottom_right_preview">
										<label class="form-label col-md-12"></label>
										<div class="col-md-12">
											<img id="frame_bottom_right_preview" width="200" class="img-fluid rounded d-block img-thumbnail">
										</div>
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
				</form>
			</div>
		</div>
	</div>
</div>

@include('themes.components.index')
@include('icons.phospor.modal.index')

<script src="{{ asset('extend/js/code-editor-shortcut-keys.js') }}"></script>

<script>
	var premium = `
		<svg width="22" height="19" viewBox="0 0 22 19" fill="none" xmlns="http://www.w3.org/2000/svg">
			<path d="M10.9997 5.8022L6.05467 8.43956L1.39819 5.8022L3.44479 15.2967H18.5551L20.6017 5.8022L15.9448 8.43956L10.9997 5.8022Z" fill="#FDCB5A"/>
			<path fill-rule="evenodd" clip-rule="evenodd" d="M2.61536 18C2.61536 17.4477 3.06307 17 3.61536 17H18.3846C18.9369 17 19.3846 17.4477 19.3846 18C19.3846 18.5523 18.9369 19 18.3846 19H3.61536C3.06307 19 2.61536 18.5523 2.61536 18Z" fill="#FDCB5A"/>
			<path d="M2 4C3.10457 4 4 3.10457 4 2C4 0.895431 3.10457 0 2 0C0.895431 0 0 0.895431 0 2C0 3.10457 0.895431 4 2 4Z" fill="#FDCB5A"/>
			<path d="M19.7803 4C20.8848 4 21.7803 3.10457 21.7803 2C21.7803 0.895431 20.8848 0 19.7803 0C18.6757 0 17.7803 0.895431 17.7803 2C17.7803 3.10457 18.6757 4 19.7803 4Z" fill="#FDCB5A"/>
			<path d="M11 4C12.1046 4 13 3.10457 13 2C13 0.895431 12.1046 0 11 0C9.89543 0 9 0.895431 9 2C9 3.10457 9.89543 4 11 4Z" fill="#FDCB5A"/>
		</svg>
	`;

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

		$('#music_id').select2({
      // minimumInputLength: 3,
			maximumSelectionSize: 1,
      multiple: true,
      placeholder: 'Music ...',
      ajax: {
        type: 'GET',
        url: baseUrl + '/musics/list?blank=1',
        dataType: 'JSON',
        quietMillis: 100,
        data: function(term, page) {
          return {
            keyword: term,
            page: page,
          };
        },
        results: function(data, page) {
          var more = (page * 20) < data.data.total;

          return {
            results: data.data.list,
            more: more
          };
        }
      },
      formatResult: function(data) {
        var markup = '<b>' + data.name + '</b><br>' + data.categories;
        return markup;
      },
      formatSelection: function(data) {
        return data.name;
      }
    });

		$('[name="frame_top_left"]').change(function() {
			if (this.files[0].size > maxFileSize) {
				$('[name="frame_top_left"]').val('');
				$('#frame_top_left_preview').attr('src', '');
				swalAlert('warning', 'Information', 'Frame top left size cannot be more than 2 mb');
				return false;
			} else {
				$('.frame_top_left_preview').show();
				$('#frame_top_left_preview').attr('src', URL.createObjectURL(this.files[0]));
			}
		});
		
		$('[name="frame_top_center"]').change(function() {
			if (this.files[0].size > maxFileSize) {
				$('[name="frame_top_center"]').val('');
				$('#frame_top_center_preview').attr('src', '');
				swalAlert('warning', 'Information', 'Frame top center size cannot be more than 2 mb');
				return false;
			} else {
				$('.frame_top_center_preview').show();
				$('#frame_top_center_preview').attr('src', URL.createObjectURL(this.files[0]));
			}
		});

		$('[name="frame_top_right"]').change(function() {
			if (this.files[0].size > maxFileSize) {
				$('[name="frame_top_right"]').val('');
				$('#frame_top_right_preview').attr('src', '');
				swalAlert('warning', 'Information', 'Frame top right size cannot be more than 2 mb');
				return false;
			} else {
				$('.frame_top_right_preview').show();
				$('#frame_top_right_preview').attr('src', URL.createObjectURL(this.files[0]));
			}
		});

		$('[name="frame_side_left"]').change(function() {
			if (this.files[0].size > maxFileSize) {
				$('[name="frame_side_left"]').val('');
				$('#frame_side_left_preview').attr('src', '');
				swalAlert('warning', 'Information', 'Frame side left size cannot be more than 2 mb');
				return false;
			} else {
				$('.frame_side_left_preview').show();
				$('#frame_side_left_preview').attr('src', URL.createObjectURL(this.files[0]));
			}
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

		$('[name="frame_side_right"]').change(function() {
			if (this.files[0].size > maxFileSize) {
				$('[name="frame_side_right"]').val('');
				$('#frame_side_right_preview').attr('src', '');
				swalAlert('warning', 'Information', 'Frame side right size cannot be more than 2 mb');
				return false;
			} else {
				$('.frame_side_right_preview').show();
				$('#frame_side_right_preview').attr('src', URL.createObjectURL(this.files[0]));
			}
		});

		$('[name="frame_bottom_left"]').change(function() {
			if (this.files[0].size > maxFileSize) {
				$('[name="frame_bottom_left"]').val('');
				$('#frame_bottom_left_preview').attr('src', '');
				swalAlert('warning', 'Information', 'Frame bottom left size cannot be more than 2 mb');
				return false;
			} else {
				$('.frame_bottom_left_preview').show();
				$('#frame_bottom_left_preview').attr('src', URL.createObjectURL(this.files[0]));
			}
		});
		
		$('[name="frame_bottom_center"]').change(function() {
			if (this.files[0].size > maxFileSize) {
				$('[name="frame_bottom_center"]').val('');
				$('#frame_bottom_center_preview').attr('src', '');
				swalAlert('warning', 'Information', 'Frame bottom center size cannot be more than 2 mb');
				return false;
			} else {
				$('.frame_bottom_center_preview').show();
				$('#frame_bottom_center_preview').attr('src', URL.createObjectURL(this.files[0]));
			}
		});

		$('[name="frame_bottom_right"]').change(function() {
			if (this.files[0].size > maxFileSize) {
				$('[name="frame_bottom_right"]').val('');
				$('#frame_bottom_right_preview').attr('src', '');
				swalAlert('warning', 'Information', 'Frame bottom right size cannot be more than 2 mb');
				return false;
			} else {
				$('.frame_bottom_right_preview').show();
				$('#frame_bottom_right_preview').attr('src', URL.createObjectURL(this.files[0]));
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
								syamValidationServer('[name="category_id"]', 'category_id', data);
								syamValidationServer('[name="type_id"]', 'type_id', data);
								syamValidationSelect2Server('[name="music_id"]', 'music_id', data);
								syamValidationServer('[name="name"]', 'name', data);
								syamValidationServer('[name="layout"]', 'layout', data);
								
								syamValidationServer('[name="font"]', 'font', data);
								syamValidationServer('[name="font_base"]', 'font_base', data);
								syamValidationServer('[name="font_accent"]', 'font_accent', data);
								syamValidationServer('[name="font_latin"]', 'font_latin', data);
								syamValidationServer('[name="inv_bg"]', 'inv_bg', data);
								syamValidationServer('[name="inv_base"]', 'inv_base', data);
								syamValidationServer('[name="inv_accent"]', 'inv_accent', data);
								syamValidationServer('[name="inv_border"]', 'inv_border', data);
								syamValidationServer('[name="menu_bg"]', 'menu_bg', data);
								syamValidationServer('[name="menu_inactive"]', 'menu_inactive', data);
								syamValidationServer('[name="menu_active"]', 'menu_active', data);
								syamValidationServer('[name="btn_color"]', 'btn_color', data);

								syamValidationServer('[name="thumbnail"]', 'thumbnail', data);
								syamValidationServer('[name="thumbnail_xs"]', 'thumbnail_xs', data);
								syamValidationServer('[name="frame_top_right"]', 'frame_top_right', data);
								syamValidationServer('[name="frame_top_center"]', 'frame_top_center', data);
								syamValidationServer('[name="frame_top_left"]', 'frame_top_left', data);
								syamValidationServer('[name="frame_side_left"]', 'frame_side_left', data);
								syamValidationServer('[name="background"]', 'background', data);
								syamValidationServer('[name="frame_side_right"]', 'frame_side_right', data);
								syamValidationServer('[name="frame_bottom_right"]', 'frame_bottom_right', data);
								syamValidationServer('[name="frame_bottom_center"]', 'frame_bottom_center', data);
								syamValidationServer('[name="frame_bottom_left"]', 'frame_bottom_left', data);
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

		$('.select2').change(function() {
      if ($(this).val() !== '') {
        syamValidationSelect2Remove(this);
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

					var thumbnail = ``;
					if (v.url_thumbnail !== null) {
						thumbnail = `
							<a data-fancybox data-src="${v.url_thumbnail}" data-caption="${v.name}">
								<img src="${v.url_thumbnail}" class="shadow" style="border-radius:1rem" width="100px">
							</a>
						`;
					}
					var thumbnail_xs = ``;
					if (v.url_thumbnail_xs !== null) {
						thumbnail_xs = `
							<a data-fancybox data-src="${v.url_thumbnail_xs}" data-caption="${v.name}">
								<img src="${v.url_thumbnail_xs}" class="shadow" style="border-radius:1rem" width="100px">
							</a>
						`;
					}

					var html = '<tr>' +
						'<td class="center">' + no + '</td>' +
						'<td>' + v.name + '</td>' +
						'<td>' + v.category + '</td>' +
						'<td>' + v.type + '</td>' +
						'<td class="center">' + thumbnail + '</td>' +
						'<td class="center">' + thumbnail_xs + '</td>' +
						'<td class="center">' + (v.is_premium == 1 ? premium : '') + '</td>' +
						'<td class="nowrap">' + status + '</td>' +
						'<td class="right nowrap">' +
              '<button type="button" class="btn btn-primary btn-sm" title="Add Theme Components" onclick="showThemeComponents(' + v.id + ')"><i class="fas fa-plus"></i></button> ' +
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
		$('.select2').select2('val', '');
		$('a .select2-chosen').html('Choose ...');
		$('.form-control').prop('readonly', false);
		syamValidationRemove('.form-control, .form-select');
		syamValidationSelectRemove('.form-select');
		$('.thumbnail_preview').hide();
		$('.thumbnail_xs_preview').hide();
		
		$('.frame_top_left_preview').hide();
		$('.btn_frame_top_left').hide();
		$('.frame_top_center_preview').hide();
		$('.btn_frame_top_center').hide();
		$('.frame_top_right_preview').hide();
		$('.btn_frame_top_right').hide();
		$('.frame_side_left_preview').hide();
		$('.btn_frame_side_left').hide();
		$('.background_preview').hide();
		$('.btn_background').hide();
		$('.frame_side_right_preview').hide();
		$('.btn_frame_side_right').hide();
		$('.frame_bottom_left_preview').hide();
		$('.btn_frame_bottom_left').hide();
		$('.frame_bottom_center_preview').hide();
		$('.btn_frame_bottom_center').hide();
		$('.frame_bottom_right_preview').hide();
		$('.btn_frame_bottom_right').hide();
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

					if (data.data.music != null) {
						var music = data.data.music
						var musicId = new Array();
						if (music.length > 0) {
							$.each(music, function(i, v) {
								musicId.push(v.id);
							});
							$('#music_id').select2('data', music);
							$('#music_id').val(musicId).change();
						}
					}

					$('[name="name"]').val(data.data.name);
					$('[name="description"]').val(data.data.description);
					$('[name="layout"]').val(data.data.layout);

					if (data.data.is_premium == 1) {
						$('#is_premium').prop('checked', true);
					} else {
						$('#is_premium').prop('checked', false);
					}

					$('[name="thumbnail_old"]').val(data.data.thumbnail);
					if (data.data.url_thumbnail !== null) {
						$('.btn_thumbnail').show()
						$('.btn_thumbnail').attr('onclick', 'removeImage('+data.data.id+', "thumbnail")')

						$('.thumbnail_preview').show();
						$('#thumbnail_preview').attr('src', data.data.url_thumbnail)
					}

					$('[name="thumbnail_xs_old"]').val(data.data.thumbnail_xs);
					if (data.data.url_thumbnail_xs !== null) {
						$('.btn_thumbnail_xs').show()
						$('.btn_thumbnail_xs').attr('onclick', 'removeImage('+data.data.id+', "thumbnail_xs")')
						
						$('.thumbnail_xs_preview').show();
						$('#thumbnail_xs_preview').attr('src', data.data.url_thumbnail_xs)
					}

					// TODO: styles
					$('[name="font"]').val(data.data.fonts_import)

					if (data.data.styles_root) {
						var styles_root = JSON.parse(data.data.styles_root)

						$('[name="font_base"]').val(styles_root.font_base)
						$('[name="font_accent"]').val(styles_root.font_accent)
						$('[name="font_latin"]').val(styles_root.font_latin)
						
						$('[name="inv_bg"]').val(styles_root.inv_bg)
						updateColorBox(styles_root.inv_bg, 'colorbox_inv_bg')
						$('[name="inv_base"]').val(styles_root.inv_base)
						updateColorBox(styles_root.inv_base, 'colorbox_inv_base')
						$('[name="inv_accent"]').val(styles_root.inv_accent)
						updateColorBox(styles_root.inv_accent, 'colorbox_inv_accent')
						$('[name="inv_border"]').val(styles_root.inv_border)
						updateColorBox(styles_root.inv_border, 'colorbox_inv_border')
						$('[name="menu_bg"]').val(styles_root.menu_bg)
						updateColorBox(styles_root.menu_bg, 'colorbox_menu_bg')
						$('[name="menu_inactive"]').val(styles_root.menu_inactive)
						updateColorBox(styles_root.menu_inactive, 'colorbox_menu_inactive')
						$('[name="menu_active"]').val(styles_root.menu_active)
						updateColorBox(styles_root.menu_active, 'colorbox_menu_active')
						$('[name="btn_color"]').val(styles_root.btn_color)
						updateColorBox(styles_root.btn_color, 'colorbox_btn_color')
					}

					// TODO: frame
					$('[name="frame_top_left_old"]').val(data.data.frame_top_left);
					if (data.data.url_frame_top_left !== null) {
						$('.btn_frame_top_left').show()
						$('.btn_frame_top_left').attr('onclick', 'removeImage('+data.data.id+', "frame_top_left")')

						$('.frame_top_left_preview').show();
						$('#frame_top_left_preview').attr('src', data.data.url_frame_top_left)
					}

					$('[name="frame_top_center_old"]').val(data.data.frame_top_center);
					if (data.data.url_frame_top_center !== null) {
						$('.btn_frame_top_center').show()
						$('.btn_frame_top_center').attr('onclick', 'removeImage('+data.data.id+', "frame_top_center")')

						$('.frame_top_center_preview').show();
						$('#frame_top_center_preview').attr('src', data.data.url_frame_top_center)
					}

					$('[name="frame_top_right_old"]').val(data.data.frame_top_right);
					if (data.data.url_frame_top_right !== null) {
						$('.btn_frame_top_right').show()
						$('.btn_frame_top_right').attr('onclick', 'removeImage('+data.data.id+', "frame_top_right")')

						$('.frame_top_right_preview').show();
						$('#frame_top_right_preview').attr('src', data.data.url_frame_top_right)
					}

					$('[name="frame_side_left_old"]').val(data.data.frame_side_left);
					if (data.data.url_frame_side_left !== null) {
						$('.btn_frame_side_left').show()
						$('.btn_frame_side_left').attr('onclick', 'removeImage('+data.data.id+', "frame_side_left")')
						
						$('.frame_side_left_preview').show();
						$('#frame_side_left_preview').attr('src', data.data.url_frame_side_left)
					}

					$('[name="background_old"]').val(data.data.background);
					if (data.data.url_background !== null) {
						$('.btn_background').show()
						$('.btn_background').attr('onclick', 'removeImage('+data.data.id+', "background")')
						
						$('.background_preview').show();
						$('#background_preview').attr('src', data.data.url_background)
					}

					$('[name="frame_side_right_old"]').val(data.data.frame_side_right);
					if (data.data.url_frame_side_right !== null) {
						$('.btn_frame_side_right').show()
						$('.btn_frame_side_right').attr('onclick', 'removeImage('+data.data.id+', "frame_side_right")')

						$('.frame_side_right_preview').show();
						$('#frame_side_right_preview').attr('src', data.data.url_frame_side_right)
					}

					$('[name="frame_bottom_left_old"]').val(data.data.frame_bottom_left);
					if (data.data.url_frame_bottom_left !== null) {
						$('.btn_frame_bottom_left').show()
						$('.btn_frame_bottom_left').attr('onclick', 'removeImage('+data.data.id+', "frame_bottom_left")')

						$('.frame_bottom_left_preview').show();
						$('#frame_bottom_left_preview').attr('src', data.data.url_frame_bottom_left)
					}

					$('[name="frame_bottom_center_old"]').val(data.data.frame_bottom_center);
					if (data.data.url_frame_bottom_center !== null) {
						$('.btn_frame_bottom_center').show()
						$('.btn_frame_bottom_center').attr('onclick', 'removeImage('+data.data.id+', "frame_bottom_center")')

						$('.frame_bottom_center_preview').show();
						$('#frame_bottom_center_preview').attr('src', data.data.url_frame_bottom_center)
					}

					$('[name="frame_bottom_right_old"]').val(data.data.frame_bottom_right);
					if (data.data.url_frame_bottom_right !== null) {
						$('.btn_frame_bottom_right').show()
						$('.btn_frame_bottom_right').attr('onclick', 'removeImage('+data.data.id+', "frame_bottom_right")')

						$('.frame_bottom_right_preview').show();
						$('#frame_bottom_right_preview').attr('src', data.data.url_frame_bottom_right)
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

	function removeImage(id, field) {
		$.ajax({
			type: 'DELETE',
			url: baseUrl + className + '/destroy-image',
			data: {
				id: id,
				field: field,
			},
			cache: false,
			dataType: 'JSON',
			beforeSend: function() {
				showLoader();
			},
			success: function(data) {
				if (data.status) {
					editData(id, 1)
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

	function updateColorBox(value, id) {
		if (value == '') {
			$('#' + id).css('background-color', '#ffffff');
		} else {
			$('#' + id).css('background-color', value);
		}
	}
</script>
@endsection