$(document).ready(function(){
    // feature detection for drag&drop upload
    var isAdvancedUpload = function()
    {
        var div = document.createElement( 'div' );
        return ( ( 'draggable' in div ) || ( 'ondragstart' in div && 'ondrop' in div ) ) && 'FormData' in window && 'FileReader' in window;
    }();

    var $dropZones =  $('.integrated-dropzone');

    $.each($dropZones, function() {
        var $dropZone = $(this);

        // drag&drop files if the feature is available
        if( isAdvancedUpload )
        {

            $dropZone.addClass( 'has-advanced-upload' ) // letting the CSS part to know drag&drop is supported by the browser
                .on( 'drag dragstart dragend dragover dragenter dragleave drop', function( e )
                {
                    // preventing the unwanted behaviours
                    e.preventDefault();
                    e.stopPropagation();
                })
                .on( 'dragover dragenter', function() //
                {
                    $dropZone.addClass( 'is-dragover' );
                })
                .on( 'dragleave dragend drop', function()
                {
                    $dropZone.removeClass( 'is-dragover' );
                })
                .on( 'drop', function( e )
                {
                    $("input[type='file']", $dropZone).prop("files",  e.originalEvent.dataTransfer.files); // the files that were dropped
                    $("input[type='file']", $dropZone).trigger('change');
                });
        }

        var filer_default_opts = {
            showThumbs: true,
            limit: 1,
            appendTo: ".integrated-dropzone",
            afterRender: function (item, box) {
                if (this.files && this.files.length) {
                    $(box).hide();
                }
            },
            afterShow: function (item, box, a) {
                $(box).hide();
                $dropZone.find('.remove-file').val(0);
            },
            onEmpty: function (box) {
                $(box).show();
                $dropZone.find('.remove-file').val(1);
            },
            theme: "dragdropbox",
            templates: {
                box: '<ul class="jFiler-items-list jFiler-items-grid"></ul>',
                item: '<li class="jFiler-item">\
						<div class="jFiler-item-container">\
							<div class="jFiler-item-inner">\
								<div class="jFiler-item-thumb">\
									<div class="jFiler-item-status"></div>\
									<div class="jFiler-item-thumb-overlay">\
										<div class="jFiler-item-info">\
											<div style="display:table-cell;vertical-align: middle;">\
												<span class="jFiler-item-title"><b title="{{fi-name}}">{{fi-name}}</b></span>\
												<span class="jFiler-item-others">{{fi-size2}}</span>\
											</div>\
										</div>\
									</div>\
									{{fi-image}}\
								</div>\
								<div class="jFiler-item-assets jFiler-row">\
									<ul class="list-inline pull-right">\
										<li><a class="icon-jfi-trash jFiler-item-trash-action"></a></li>\
									</ul>\
								</div>\
							</div>\
						</div>\
					</li>',
                itemAppend: '<li class="jFiler-item">\
                                <div class="jFiler-item-container">\
                                    <div class="jFiler-item-inner">\
                                        <div class="jFiler-item-thumb">\
                                            <div class="jFiler-item-status"></div>\
                                            <div class="jFiler-item-thumb-overlay">\
        										<div class="jFiler-item-info">\
        											<div style="display:table-cell;vertical-align: middle;">\
        												<span class="jFiler-item-title"><b title="{{fi-name}}">{{fi-name}}</b></span>\
        											</div>\
        										</div>\
        									</div>\
                                            {{fi-image}}\
                                        </div>\
                                        <div class="jFiler-item-assets jFiler-row">\
                                            <ul class="list-inline pull-right">\
                                                <li><a href="{{fi-name}}" title="Download" target="_blank"><span class="glyphicon glyphicon-download-alt"></span></a></li>\
                                                <li><a class="icon-jfi-trash jFiler-item-trash-action" title="Delete"></a></li>\
                                            </ul>\
                                        </div>\
                                    </div>\
                                </div>\
                            </li>',
                canvasImage: true,
                removeConfirmation: true,
                _selectors: {
                    list: '.jFiler-items-list',
                    item: '.jFiler-item',
                    progressBar: '.bar',
                    remove: '.jFiler-item-trash-action'
                }
            }
        };

        //merge default options with options passed in view
        $.extend(filer_default_opts, $dropZone.data('options'));

        $('input[type="file"]', $dropZone).filer(filer_default_opts);

        //validate form
        $dropZone.closest('form').submit(function () {
            if ($dropZone.hasClass('required') && $dropZone.find('.dropzone-empty').is(':visible')) {
                $dropZone.addClass('empty-error');
                $('html, body').animate({
                    scrollTop: $dropZone.offset().top
                }, 1000);
                return false;
            }
        });
    });
});
