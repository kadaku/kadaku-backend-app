<div class="modal fade" id="modal_form_components" data-bs-backdrop="static">
	<div class="modal-dialog" style="min-width:100%;">
		<div class="modal-content">
      <form action="" id="#form_add_components" enctype="multipart/form-data">
        @csrf
        <div class="modal-header">
          <h5 class="modal-title" id="modal_form_components_label"><strong>Form Add Data Components</strong></h5>
          <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
        </div>
        <div class="modal-body">
          <input type="hidden" name="theme_id">
          <div class="row">	
            <div class="col-md-9">
              <div class="row">
                <div class="col-lg-12">
                  <div class="form-group mb-2">
                    <label class="form-label">Layout</label>
                    <input type="text" name="layout_id" id="layout_id" class="select2">
                  </div>
                  <div style="height: 495px; overflow-y: scroll; scroll-behavior: smooth;">
                    <div id="list_components"></div>
                  </div>
                </div>
              </div>
            </div>
            <div class="col-md-3 mt-5">
              <h6 class="text-center">Preview Themes</h6>
              <div style="border-bottom: 1px dashed lightgray; margin: 5px 0;"></div>
              <div id="preview_components" style="
                height: 822px; overflow-y: scroll; scroll-behavior: smooth; display: flex; justify-content: center; zoom: 0.6;
                background: lightslategrey; border-radius: 30px;"
              ></div>
            </div>
        </div>
        <div class="modal-footer">

        </div>
      </form>
		</div>
	</div>
</div>

@include('themes.components.modal_editor.index')

{{-- <link href="{{ asset('extend/plugins/animate/animate.min.css') }}" rel="stylesheet" type="text/css"> --}}

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
    height: 400px;
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
    lineNumbers: false, 
    indentWithTabs: true,
    matchBrackets: true,
  }
  var codeEditor = {}

  $(function () {
    $('#layout_id').select2({
      maximumSelectionSize: 1,
      multiple: true,
      placeholder: 'Choose ...',
      ajax: {
        type: 'GET',
        url: baseUrl + '/layouts/list',
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
        var markup = `
          <div class="d-flex align-items-center" style="gap: .6rem;">
            <img src="${data.url_image}" width="60px" height="90px" style="border-radius: .5rem">
            <div>
              <p class="mb-2" style="font-size: 16px;"><b>${data.title}</b></p>
              <p class="mb-1">Category : ${data.category}</p>
              <p class="mb-1 d-flex align-items-center" style="gap: .6rem">
                ${premium} 
                <span>Premium</span>
              </p>
            </div>
          </div>
        `;
        return markup;
      },
      formatSelection: function(data) {
        var dataParam = JSON.stringify(data)

        storeThemeComponents($('[name="theme_id"]').val(), `${dataParam}`)
        return data.title;
      }
    });  
  });

  function showThemeComponents(theme_id, index_component = null) {
    var dataComponents;
		$.ajax({
			type: 'GET',
			url: baseUrl + className + '/show-components/' + theme_id,
			cache: false,
			dataType: 'JSON',
			beforeSend: function() {
        $('[name="theme_id"]').val(theme_id)
        $('#list_components').empty()
        $('#preview_components').empty()
        $('.code_editor').val('')
        $('.CodeMirror').remove()
			},
			success: function(data) {
				if (data.status) {
          var listComponents = `
            <div class="accordion custom-accordionwithicon custom-accordion-border accordion-border-box accordion-secondary" id="accordionBordered">`;
              $.each(data.data.components, function(i, v) {
                dataComponents = data.data.components

                var btnRemoveBackground = '';
                if (v.background != null) {
                  btnRemoveBackground = `
                    <button type="button" class="btn btn-light bg-white text-muted" onclick="destroyBackgroundComponents(${v.id}, ${v.theme_id})"><i class="fas fa-trash-alt me-1"></i>Remove Background</button>
                  `;
                }

                listComponents += `
                  <div class="accordion-item ${i != 0 ? 'mt-2' : ''}">
                    <h2 class="accordion-header" id="accordionbordered${i}">
                      <button class="accordion-button .accordion_button${i}" type="button" data-bs-toggle="collapse" data-bs-target="#accor_bordered_collapse${i}">
                        <div class="d-flex justify-content-between" style="width: 100%; flex-direction: row;">
                          <div class="d-flex align-items-center" style="gap: .6rem;">
                            <span>${v.name}</span>
                          </div>
                          ${v.icon != null ? '<span class="mx-2 text-muted" style="font-size: 26px">' + v.icon + '</span>' : ''}
                        </div>
                      </button>
                    </h2>
                    <div id="accor_bordered_collapse${i}" class="accordion-collapse collapse show" aria-labelledby="accordionbordered${i}" data-bs-parent="#accordionBordered">
                      <div class="accordion-body bg-light">
                        <div class="d-flex flex-column justify-content-between" style="gap: .6rem">
                          <div class="w-100">
                            <input type="text" value="${v.name}" onkeyup="debouncedUpdateComponent(${i}, ${v.id}, ${v.theme_id}, 'name', this.value)" class="form-control">  
                          </div>
                          <div class="d-flex justify-content-end" style="gap: .6rem">
                            <button type="button" class="btn btn-light bg-white text-muted" onclick="chooseIconComponent(${i}, ${v.id}, ${v.theme_id}, 'icon')"><i class="fas fa-icons me-1"></i>Choose Icon</button>   
                            <button type="button" class="btn btn-light bg-white text-muted" onclick="uploadBackgroundComponent('open', ${v.id}, ${v.theme_id}, ${i})"><i class="fas fa-image me-1"></i>Upload Background</button>   
                            ${btnRemoveBackground}
                            <button type="button" class="btn btn-light bg-white text-muted" onclick="destroyThisComponents(${v.id}, ${v.theme_id})"><i class="fas fa-trash-alt me-1"></i>Remove Component</button>     
                          </div>
                        </div>
                        <div class="my-3" style="position: relative">
                          <textarea id="code_editor${i}" rows="10" cols="50" class="form-control code_editor">${v.body?.trim()}</textarea>
                        </div>
                        <button type="button" class="btn btn-primary btn-block" onclick="updateBodyComponent(${i}, ${v.id}, ${v.theme_id}, 'body')">Update</button>
                      </div>
                    </div>
                  </div>
                `;
              });
          listComponents += `
            </div>
          `;
          $('#list_components').html(listComponents)

          // preview components
          showPreviewComponents(data.data)

          $('#modal_form_components').modal('show')
        }
			},
			complete: function() {
        var modal = new bootstrap.Modal(document.getElementById('modal_editor_component'));
        $(function () {
          $('#theme_id_editor').val(theme_id);
          $('.editorbox .text-editable').on('click', function () {
            currentEditable = $(this);
            var dataSection = currentEditable.closest('.editorbox').data('section');
            var textContent = currentEditable.html().replace(/<br\s*\/?>/g, '\n');

            $('#section_id_editor').val(dataSection);
            $('#textContent').val(textContent);
            $('#textColor').val(rgbToHex(currentEditable.css('color')));
            $('#fontSize').val(parseInt(currentEditable.css('font-size')));
            $('#fontBold').prop('checked', currentEditable.css('font-weight') === '700' || currentEditable.css('font-weight') === 'bold');
            $('#fontItalic').prop('checked', currentEditable.css('font-style') === 'italic');
            modal.show();
          });
        })

        if (dataComponents) {
          if (index_component == null) {
            $('#modal_form_components').on('shown.bs.modal', function () {
              $('.CodeMirror').remove()

              $.each(dataComponents, function(i, v) {
                codeEditor[i]  = CodeMirror.fromTextArea(document.getElementById('code_editor' + i), configCodeEditor);
              });
            });
          } else {
            $('.CodeMirror').remove()
            $.each(dataComponents, function(i, v) {
              codeEditor[i] = CodeMirror.fromTextArea(document.getElementById('code_editor' + i), configCodeEditor);
            });
          }
        }

        if (index_component != null) {
          codeEditor[index_component].refresh();
          codeEditor[index_component].focus();
        }
			},
			error: function(e) {
				toastrAlert('error', e.status, e.statusText);
			}
		});
	}

  function storeEditableContent() {
    var updatedText = $('#textContent').val().replace(/\n/g, '<br>'); // Convert newlines to <br> before setting HTML
    currentEditable.html(updatedText);
    // currentEditable.html($('#textContent').val());
    currentEditable.css('color', $('#textColor').val());
    currentEditable.css('font-size', $('#fontSize').val() + 'px');
    currentEditable.css('font-weight', $('#fontBold').prop('checked') ? 'bold' : 'normal');
    currentEditable.css('font-style', $('#fontItalic').prop('checked') ? 'italic' : 'normal');
    
    var dataSection = currentEditable.closest('.editorbox').data('section');
    var htmlContent = $('[data-section="' + dataSection + '"]').html();

    updateDataComponent(0, dataSection, $('#theme_id_editor').val(), 'body', htmlContent)
    $('#modal_editor_component').modal('hide')
  }

  function showPreviewComponents(data) {
    var style = '';
    var style_root = JSON.parse(data.styles_root);
    if (style_root) {
      style = `
        <style>
          ${data.fonts_import}  
  
          :root {
            --inv-bg: ${style_root.inv_bg};
            --inv-base: ${style_root.inv_base};
            --inv-accent: ${style_root.inv_accent};
            --inv-border: ${style_root.inv_border};
            --font-base: ${style_root.font_base};
            --font-accent: ${style_root.font_accent};
            --font-latin: ${style_root.font_latin};
            --menu-bg: ${style_root.menu_bg};
            --menu-inactive: ${style_root.menu_inactive};
            --menu-active: ${style_root.menu_active};
            --btn-color: ${style_root.btn_color};
          }
  
          .theme .frame {
            bottom: 0;
            left: 0;
            position: absolute;
            right: 0;
            top: 0;
          }
  
          .theme .frame-tl {
            left: 0;
            position: absolute;
            top: 0;
            width: 50%;
          }
  
          .theme .frame-br {
            right: 0;
          }
  
          .theme .frame-bl,
          .theme .frame-br {
            bottom: 0;
            position: absolute;
            width: 50%;
          }
  
          .theme .frame-bl,
          .theme .frame-br {
            bottom: 0;
            position: absolute;
            width: 50%;
          }
  
          .theme .frame-bl {
            left: 0;
          }
  
          .theme .frame-tl {
            left: 0;
            position: absolute;
            top: 0;
            width: 50%;
          }
  
          .theme .frame-tr {
            position: absolute;
            right: 0;
            top: 0;
            width: 50%;
          }
  
          .theme .frame-bl,
          .theme .frame-br {
            bottom: 0;
            position: absolute;
            width: 50%;
          }
  
          .theme .frame-bl {
            left: 0;
          }
  
          .theme .frame-br {
            right: 0;
          }
  
          .theme .frame-bl,
          .theme .frame-br {
            bottom: 0;
            position: absolute;
            width: 50%;
          }
  
          .editorbox .color-accent {
            color: var(--inv-accent);
          }
  
          .editorbox .font-accent {
            font-family: var(--font-accent);
          }
  
          .editorbox .font-latin {
            font-family: var(--font-latin);
            font-size: 200%;
          }
  
          .editorbox .font-base {
            font-family: var(--font-base) !important;
          }
  
          .editorbox .btn, 
          .editorbox .btn:active {
            background-color: var(--inv-accent);
            border-color: var(--inv-accent);
            color: var(--btn-color);
          }
  
          .editorbox .text-editable {
            cursor: pointer;
            animation: blink 1s .3s 3 alternate;
            border: 1px dashed transparent
          }
  
          .editorbox .text-editable:hover {
            border: 1px dashed
          }
  
          .editorbox .image-editable {
            animation: opacity 1s .3s 3 alternate;
            cursor: pointer
          }
  
          .editorbox .image-editable:hover {
            opacity: .7
          }
        </style>
      `;
    }
    var styleBackground = '';
    if (data.url_background != null) {
      styleBackground = `background-image: url('${data.url_background}');`;
    }

    var frame = '';
    if (data.url_frame_top_left != null) {
      frame += `<img src="${data.url_frame_top_left}" class="frame-tl animate__animated animate__fadeInTopLeft animate__slow" alt="frame" draggable="false">`
    }
    if (data.url_frame_top_center != null) {
      frame += `<img src="${data.url_frame_top_center}" class="frame-tl w-100 animate__animated animate__fadeInDown animate__slower" alt="frame" draggable="false">`
    }
    if (data.url_frame_top_right != null) {
      frame += `<img src="${data.url_frame_top_right}" class="frame-tr animate__animated animate__fadeInTopRight animate__slow" alt="frame" draggable="false">`
    }
    if (data.url_frame_side_left != null) {
      frame += `<img src="${data.url_frame_side_left}" class="frame-bl h-100 animate__animated animate__fadeInLeft animate__slower" alt="frame" style="width: auto;" draggable="false">`
    }
    if (data.url_frame_side_right != null) {
      frame += `<img src="${data.url_frame_side_right}" class="frame-br h-100 animate__animated animate__fadeInRight animate__slower" alt="frame" style="width: auto;" draggable="false">`
    }
    if (data.url_frame_bottom_left != null) {
      frame += `<img src="${data.url_frame_bottom_left}" class="frame-bl animate__animated animate__fadeInBottomLeft animate__slow" alt="frame" draggable="false">`
    }
    if (data.url_frame_bottom_center != null) {
      frame += `<img src="${data.url_frame_bottom_center}" class="frame-br w-100 animate__animated animate__fadeInUp animate__slower" alt="frame" draggable="false">`
    }
    if (data.url_frame_bottom_right != null) {
      frame += `<img src="${data.url_frame_bottom_right}" class="frame-br animate__animated animate__fadeInBottomRight animate__slow" alt="frame" draggable="false">`
    }

    var htmlPreviewTheme = style;
    if (data.components.length > 0) {
      htmlPreviewTheme += `
        <div>
      `;

      $.each(data.components, function(i, v) {
        var styleBackgroundComponent = '';
        if (v.url_background != null) {
          styleBackgroundComponent = `background-image: url('${v.url_background}');`;
        } 

        htmlPreviewTheme += `
          <div style="${styleBackgroundComponent != '' ? styleBackgroundComponent : styleBackground} width: 458px; height: 822px; position: relative; overflow: hidden; background-position: 100%; background-size: cover; background-color: var(--inv-bg);">
            <div class="theme ${v.name}">
              <div class="frame">
                ${frame}
              </div>
            </div>
            <div class="zoom-content" style="position: absolute; width: 100%; height: 100%; transform: scale(1.13043) translate(0px, 0px);">
              <div style="width: 100%; height: 100%; display: flex; align-items: center; justify-content: center;">
                <div data-section="${v.id}" class="editorbox" style="width: 414px; height: 736px; padding: 30px; position: absolute; color: var(--inv-base) !important; font-family: var(--font-base) !important;">
                  ${v.body}
                </div>
              </div>
            </div>
            <div></div>
          </div>
        `;
      })

      htmlPreviewTheme += `
        </div>
      `;
    }
    
    $('#preview_components').html(htmlPreviewTheme)
  }

  function storeThemeComponents(theme_id, dataParam) {
    var text = 'Are you sure want to add this layout to theme components?';
  
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
        $.ajax({
          type: 'POST',
          url: baseUrl + className + '/store-components',
          data: {
            theme_id: theme_id,
            layout: dataParam,
          },
          cache: false,
          dataType: 'JSON',
          beforeSend: function() {
            showLoader();
          },
          success: function(data) {
            if (data.status) {
              showThemeComponents(theme_id)
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

  function destroyThisComponents(id, theme_id) {
		Swal.fire({
			title: 'Confirmation',
			text: 'Are you sure want to destroy this component ?',
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
					url: baseUrl + className + '/destroy-components',
          data: {
            id: id,
            theme_id: theme_id,
          },
					cache: false,
					dataType: 'JSON',
					beforeSend: function() {
						showLoader();
					},
					success: function(data) {
						if (data.status) {
              showThemeComponents(theme_id)
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

  function debounce(func, delay) {
    let timeout;
    return function(...args) {
      clearTimeout(timeout);
      timeout = setTimeout(() => func.apply(this, args), delay);
    }
  }

  const debouncedUpdateComponent = debounce(function(index, id, themeId, field, value) {
    updateDataComponent(index, id, themeId, field, value);
  }, 1000); 

  function updateBodyComponent(index, id, theme_id, field) {
    var content = codeEditor[index].getValue()
    updateDataComponent(index, id, theme_id, field, content)
  }

  function updateDataComponent(index, id, theme_id, field, value) {
    $.ajax({
      type: 'POST',
      url: baseUrl + className + '/update-components',
      data: {
        id: id,
        theme_id: theme_id,
        field: field,
        value: value,
      },
      cache: false,
      dataType: 'JSON',
      success: function(data) {
        if (data.status) {
          showThemeComponents(theme_id, index)
        }
      },
    });
  }

  function uploadBackgroundComponent(type, id, theme_id, index) {
    if (type == 'open') {
      localStorage.setItem('section_editor_index', index);
      localStorage.setItem('section_editor_id', id);
      localStorage.setItem('theme_editor_id', theme_id);

      $('[name="background_section"]').val('')
      $('#background_section_preview').attr('src', '')

      $('#modal_upload_backround_section').modal('show')
    } else {
      var data = new FormData($('#form_upload_background_section')[0]);
      var backgroundSection = $('[name="background_section"]').prop('files')[0];

      data.append('id', localStorage.getItem('section_editor_id'))
      data.append('theme_id', localStorage.getItem('theme_editor_id'))
      data.append('field', 'background')
      data.append('value', backgroundSection, backgroundSection.name)
      data.append('is_upload', 1)

      $.ajax({
        type: 'POST',
        url: baseUrl + className + '/update-components',
        data: data,
        cache: false,
        contentType: false,
        processData: false,
        dataType: 'JSON',
        beforeSend: function() {
          showLoader();
        },
        success: function(data) {
          if (data.status) {
            $('#modal_upload_backround_section').modal('hide')
            localStorage.setItem('section_editor_id', null);
            localStorage.setItem('theme_editor_id', null);
            
            showThemeComponents(data.data.theme_id, localStorage.getItem('section_editor_index'))
            toastrAlert('success', 'Success', data.message);
          } else {
            $('[name="background_section"]').val('')
            $('#background_section_preview').attr('src', '')
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
  }

  function destroyBackgroundComponents(id, theme_id) {
		Swal.fire({
			title: 'Confirmation',
			text: 'Are you sure want to destroy this background component ?',
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
					url: baseUrl + className + '/destroy-background-components/' + id,
					cache: false,
					dataType: 'JSON',
					beforeSend: function() {
						showLoader();
					},
					success: function(data) {
						if (data.status) {
              showThemeComponents(theme_id)
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

  function chooseIconComponent(index, id, theme_id, field) {
    localStorage.setItem('section_editor_index', index);
    localStorage.setItem('section_editor_id', id);
    localStorage.setItem('theme_editor_id', theme_id);

    $('#modal_phospor_icons').modal('show');
  }

  function chooseIcons(el) {
		var id = $(el).attr('data-id') - 1;
		var iconBase = $(el).children().children()[0];
		var iconBase2 = $(iconBase)[0].children[0].className;
		var splitIconName = iconBase2.split(' ');
		
		$('#icon_phosphor_icons').removeClass();
		$('#icon_phosphor_icons').addClass(splitIconName[3] + ' ' + splitIconName[4]);
		$('.input_phosphor_icons').val(splitIconName[3] + ' ' + splitIconName[4]);
    
    var icon = '<i class="icon ' + splitIconName[3] + ' ' + splitIconName[4] + '" style="color: currentcolor;"></i>';
    updateDataComponent(localStorage.getItem('section_editor_index'), localStorage.getItem('section_editor_id'), localStorage.getItem('theme_editor_id'), 'icon', icon)

    localStorage.setItem('section_editor_index', null);
    localStorage.setItem('section_editor_id', null);
    localStorage.setItem('theme_editor_id', null);

		$('#modal_phospor_icons').modal('hide');
	}
</script>