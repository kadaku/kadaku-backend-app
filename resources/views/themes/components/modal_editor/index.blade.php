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
          <button type="button" class="btn btn-primary btn-label" onclick="storeTextEditableContent()"><i class="fas fa-save label-icon align-middle fs-16 me-2"></i>Update</button>
        </div>
      </form>
		</div>
	</div>
</div>

<div class="modal fade" id="modal_media_editor" data-bs-backdrop="static">
	<div class="modal-dialog modal-dialog-scrollable">
		<div class="modal-content">
      <div class="modal-header">
        <h5 class="modal-title" id="modal_form_components_label"><strong>Media</strong></h5>
        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
      </div>
      <div class="modal-body">
        <form action="" id="#form_media_editor">
          @csrf
        <input type="hidden" id="theme_id_editor">
        <input type="hidden" id="section_id_editor">
        <div class="row">
          <div class="col-lg-12">
            <ul class="nav nav-pills animation-nav nav-justified gap-2 mb-3" role="tablist">
              <li class="nav-item waves-effect waves-light">
                <a class="nav-link" data-bs-toggle="tab" href="#animation-gallery" role="tab">Gallery</a>
              </li>
              <li class="nav-item waves-effect waves-light">
                <a class="nav-link active" data-bs-toggle="tab" href="#animation-assets" role="tab">Assets</a>
              </li>
            </ul>
            <div class="tab-content text-muted">
              <div class="tab-pane" id="animation-gallery" role="tabpanel">
                
              </div>
              <div class="tab-pane active" id="animation-assets" role="tabpanel">
                <div class="form-group mb-3">
                  <select id="category_asset_media" class="form-select">
                    @if (isset($categories_asset_media) && $categories_asset_media)
                      @foreach ($categories_asset_media as $row)
                        <option value="{{ $row }}">{{ $row }}</option>
                      @endforeach
                    @endif
                  </select>
                </div>
                <div class="list_media_assets asset-media-editor" style="display:flex; flex-wrap: wrap;"></div>
              </div>
            </div>
          </div>
        </div>
      </div>
      <div class="modal-footer">
        <button type="button" class="btn btn-secondary btn-label" data-bs-dismiss="modal"><i class="fas fa-circle-xmark label-icon align-middle fs-16 me-2"></i>Cancel</button>
        <button type="button" class="btn btn-primary btn-label" onclick="storeMediaEditableContent()"><i class="fas fa-save label-icon align-middle fs-16 me-2"></i>Update</button>
        </form>
      </div>
		</div>
	</div>
</div>

<style>
  .asset-media-editor input[type=radio]:checked+label {
    border: 2px solid #ff6767;
  }
</style>

<script>
  $(function () {
    $('#category_asset_media').change(function() {
      getListMediaAssets()
    })
  })

  function rgbToHex(rgb) {
    var rgbValues = rgb.match(/\d+/g);
    return '#' + rgbValues.map(function (value) {
      return ('0' + parseInt(value).toString(16)).slice(-2);
    }).join('');
  }

  function getListMediaAssets() {
    $.ajax({
			type: 'GET',
			url: baseUrl + '/asset-media/list',
			data: 'page=1&is_all=1&category=' + $('#category_asset_media').val(),
			cache: false,
			dataType: 'JSON',
			beforeSend: function() {
				showLoader();
        $('.list_media_assets').empty();
			},
			success: function(data) {
				if (data.status) {
          $.each(data.data.list, function(i, v) {
            var html = `
              <input type="radio" name="asset_media_source" id="asset-${v.id}" data-id="${v.id}" class="input-hidden" style="display: contents;" value="${v.url_file}">
              <label for="asset-${v.id}"
                style="
                  border-radius: 14px;
                  cursor: pointer;
                  height: 110px;
                  margin-bottom: 0;
                  padding: 4px;
                  width: 33.333%;"
              >
                <img src="${v.url_file}" alt="${v.name}" title="${v.name}" style="
                  border-radius: .6rem;
                  height: 100%;
                  -o-object-fit: contain;
                  object-fit: contain;
                  transition: all .1s;
                  width: 100%;
                ">
              </label>
            `;

            $('.list_media_assets').append(html);
          });
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