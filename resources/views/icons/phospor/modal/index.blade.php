<div class="modal fade" id="modal_phospor_icons" data-bs-backdrop="static">
	<div class="modal-dialog modal-xl modal-dialog-scrollable">
		<div class="modal-content">
			<div class="modal-header">
				<h5 class="modal-title">Choose Phospor Icons...</h5>
				<button type="button" class="btn-close" data-bs-dismiss="modal"></button>
			</div>
			<div class="modal-body center">
        <div class="row mb-2">
          <div class="col-lg-6"></div>
          <div class="col-lg-6">
            <div class="custom-search">
              <input type="text" class="form-search" id="keyword_phospor_icon_search" placeholder="Parameter Search...">
            </div>
          </div>
        </div>
        <div class="row">
          <div class="col-lg-12">
            <div id="list_phospor_icons" class="container"></div>
          </div>
        </div>
			</div>
		</div>
	</div>
</div>

<script>
	$(function() {
    $('.search_phosphor_icons').click(function() {
      $('#modal_phospor_icons').modal('show');
		});

    $('#keyword_phospor_icon_search').keyup(function() {
			getListDataPhosporIcons();
		});

    getListDataPhosporIcons()
	});

  function getListDataPhosporIcons(page = 1) {
		$.ajax({
			type: 'GET',
			url: baseUrl + '/phospor-icon/list',
			data: 'page=' + page + '&keyword=' + $('#keyword_phospor_icon_search').val() + '&is_all=1',
			cache: false,
			dataType: 'JSON',
			beforeSend: function() {
				// showLoader();
        $('#list_phospor_icons').empty();
			},
			success: function(data) {
				$.each(data.data.list, function(i, v) {
					var html = `
            <div class="icon-box qp0pm4-0 pByfF" onclick="chooseIcons(this)" data-id="${v.id}">
              <div class="icon-box-inner">
                <div class="icon-base">
                  <i class="fadeIn animated icon ph-fill ${v.icon}"></i>
                </div>
                <div class="icon-box-name">${v.name}</div>
              </div>
            </div>  
          `;

					$('#list_phospor_icons').append(html);
				});
			},
			complete: function() {
				// hideLoader();
			},
		});
	}

	// function chooseIcons(el) {
	// 	var id = $(el).attr('data-id') - 1;
	// 	var iconBase = $(el).children().children()[0];
	// 	var iconBase2 = $(iconBase)[0].children[0].className;
	// 	var splitIconName = iconBase2.split(' ');
		
	// 	$('#icon_phosphor_icons').removeClass();
	// 	$('#icon_phosphor_icons').addClass(splitIconName[2] + ' ' + splitIconName[3]);
	// 	$('.input_phosphor_icons').val(splitIconName[2] + ' ' + splitIconName[3]);
	// 	$('#modal_phospor_icons').modal('hide');
	// }
</script>