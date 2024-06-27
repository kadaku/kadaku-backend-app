<div class="modal fade" id="modal_editor_component" data-bs-backdrop="static">
	<div class="modal-dialog">
		<div class="modal-content">
      <form action="" id="#form_editor_component">
        @csrf
        <div class="modal-header">
          <h5 class="modal-title" id="modal_form_components_label"><strong>Edit Data Components</strong></h5>
          <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
        </div>
        <div class="modal-body">
          <input type="hidden" id="theme_id_editor">
          <input type="hidden" id="section_id_editor">
          <div class="row">
            <div class="col-lg-12">
              <div class="form-group mb-2">
                <label for="textContent" class="form-label">Text Content</label>
                <textarea type="text" id="textContent" rows="6" cols="10" class="form-control"></textarea>
              </div>
              <div class="row">
                <div class="col-lg-4 col-4">
                  <div class="form-group mb-2">
                    <div class="form-check">
                      <input class="form-check-input" type="checkbox" id="fontBold">
                      <label for="fontBold" class="form-check-label">Bold</label>
                    </div>
                  </div>
                </div>
                <div class="col-lg-4 col-4">
                  <div class="form-group mb-2">
                    <div class="form-check">
                      <input class="form-check-input" type="checkbox" id="fontItalic">
                      <label for="fontItalic" class="form-check-label">Italic</label>
                    </div>
                  </div>
                </div>
                <div class="col-lg-4 col-4">
                  <div class="form-group mb-2">
                    <label for="textColor" class="form-label">Text Color</label>
                    <br>
                    <input type="color" id="textColor">
                  </div>
                </div>
              </div>
              <div class="row">
                <div class="col-lg-4">
                  <div class="form-group mb-2">
                    <label for="fontSize" class="form-label">Font Size</label>
                    <input type="number" id="fontSize" min="1" maxlength="5" value="1" class="form-control">
                  </div>
                </div>
              </div>
            </div>
          </div>
        </div>
        <div class="modal-footer">
          <button type="button" class="btn btn-secondary btn-label" data-bs-dismiss="modal"><i class="fas fa-circle-xmark label-icon align-middle fs-16 me-2"></i>Cancel</button>
          <button type="button" class="btn btn-primary btn-label" onclick="storeEditableContent()"><i class="fas fa-save label-icon align-middle fs-16 me-2"></i>Update</button>
        </div>
      </form>
		</div>
	</div>
</div>

<script>
  function rgbToHex(rgb) {
    var rgbValues = rgb.match(/\d+/g);
    return '#' + rgbValues.map(function (value) {
      return ('0' + parseInt(value).toString(16)).slice(-2);
    }).join('');
  }
</script>

{{-- modal upload background section --}}
<div class="modal fade" id="modal_upload_backround_section" data-bs-backdrop="static">
	<div class="modal-dialog">
		<div class="modal-content">
      <form action="" id="#form_upload_background_section" enctype="multipart/form-data">
        @csrf
        <div class="modal-header">
          <h5 class="modal-title"><strong>Edit Background Section</strong></h5>
          <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
        </div>
        <div class="modal-body">
          <div class="row">
            <div class="col-lg-12">
              <div class="group_background_section">
                <div class="form-group row mb-0">
                  <label class="form-label col-md-12 text-center">Background Section</label>
                  <div class="col-md-12">
                    <input type="hidden" name="background_section_old">
                    <div class="d-flex" style="gap:1rem">
                      <button type="button" style="display:none" class="btn btn-danger btn_background_section"><i class="fas fa-trash-alt"></i></button>
                      <div class="d-flex w-100" style="flex-direction:column">
                        <input type="file" name="background_section" class="form-control validate" onchange="uploadBackgroundComponent('store')" accept=".png, .jpg, .jpeg">
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
                <div class="form-group row mb-2 background_section_preview">
                  <label class="form-label col-md-12"></label>
                  <div class="col-md-12">
                    <img id="background_section_preview" width="100%" class="img-fluid rounded d-block img-thumbnail">
                  </div>
                </div>
              </div>
            </div>
          </div>
        </div>
      </form>
		</div>
	</div>
</div>

<script>
  $(function() {
    $('.background_section_preview').hide();
		$('.btn_background_section').hide();

    $('[name="background_section"]').change(function() {
			if (this.files[0].size > maxFileSize) {
				$('[name="background_section"]').val('');
				$('#background_section_preview').attr('src', '');
				swalAlert('warning', 'Information', 'Background section size cannot be more than 2 mb');
				return false;
			} else {
				$('.background_section_preview').show();
				$('#background_section_preview').attr('src', URL.createObjectURL(this.files[0]));
			}
		});
  })
</script>