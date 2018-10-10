$(document).ready(function() {
    /**
     * TinyMCE instance from the top level window object
     * @type object
     */
    var tinymce = parent.tinymce;

    /**
     * Window modal object created by tinymce object
     * @type {Object}
     */
    var mcemodal = tinymce.activeEditor.windowManager.getWindows()[0];

    /**
     * @type {Object}
     */
    var options = tinymce.activeEditor.windowManager.getParams();

    /**
     * @type object|null
     */
    var previousCall = null;

    /**
     * Render result lists into thumbnail container
     * @param {object} images Collection retreived from the API
     */
    function render (images) {
        var regex = /\{\{[a-z]+\}\}/gi;
        var rowTemplate = '<div class="row">{{thumbnails}}</div>';
        var container       = $('#thumbnail-container');
        var temporaryHtml   = '';
        var temporaryThumb  = '';

        for(var i = 0; i < images.length; i++){
            // Grab the item html
            var item = renderItem(images[i]);

            // Grab the route
            images[i].source = Routing.generate(
                'integrated_storage_file',
                {
                    id: images[i].id,
                    ext: images[i].extension
                }
            );

            // Parse values
            var match = [];
            while (null != (match = regex.exec(item))) {
                item = item.replace(
                    match[0],
                    images[i][match[0].substr(2, (match[0].length-4))]
                );
            }

            // Add it
            temporaryThumb += item;

            if(i % 4 == 3 || i == images.length - 1){
                temporaryHtml  += rowTemplate.replace(/\{\{thumbnails\}\}/g, temporaryThumb);
                temporaryThumb  = '';
            }
        }

        if(images.length == 0){
            temporaryHtml = '<p class="text-center">No content found.</p>';
        }

        container.html(temporaryHtml);
    }

    /**
     * @param {object} item
     * @returns {string}
     */
    function renderItem(item)
    {
        var html = '<div class="col-sm-3"><div class="thumbnail"><div class="thumbnail-img">';

        if (item.mimeType.match('^video\/(.*)$')) {
            html += '<video poster="{{poster}}" controls class="img-responsive click-insert" data-integrated-id="{{id}}"><source src="{{source}}" type="{{mimeType}}"></video>';
        } else if (item.mimeType.match('^image\/(.*)$')) {
            html += '<img src="{{source}}" class="img-responsive click-insert" title="{{title}}" alt="{{title}}" data-integrated-id="{{id}}" />';
        } else {
            html += '<p>Not supported content type</p>'
        }

        html += '</div></div></div>';

        return html;
    }

    /**
     * Render pagination part of the image browser
     * @param {object} page Pagination data retrieved from the API
     */
    function renderPagination(page){
        var paginationTemplate =
            '<nav class="pull-right">'+
            '<ul class="pagination">'+
            '{{page-prev}}'+
            '{{page-num}}'+
            '{{page-next}}'+
            '</ul>'+
            '</nav>';

        var container = $('#pagination-container');
        var thumbnailContainer = $('#thumbnail-container');
        var temporaryHtml = '';
        var pageNum = '';

        /** hide the pagination if only one page found and return immediately */
        if(page.pageCount == 1) {
            container.html('');
            thumbnailContainer.css('height', '520px');
            return;
        } else {
            thumbnailContainer.removeAttr('style');
        }

        /** process the template to render pagination link */
        for(
            var i = page.page - 2 - (page.pageCount - page.page < 2 ? 2 - (page.pageCount - page.page) : 0);
            i <= page.page + 2 + (page.page <= 2 ? 3 - page.page : 0);
            i++
        ) {
            if(typeof page.pages[i] != 'undefined'){
                pageNum +=  '<li '+(i == page.page ? 'class="active"' : '')+'>'+
                    '<a href="'+(i == page.page ? '#' : page.pages[i].href)+'">'+i+'</a>'+
                    '</li>';
            }
        }

        temporaryHtml = paginationTemplate.replace(
            '{{page-prev}}',
            '<li '+(page.previous == null ? 'class="disabled"' : '')+'>'+
            '<a href="'+(page.previous == null ? '#' : page.previous.href)+'" aria-label="Previous">'+
            '<span aria-hidden="true">&laquo;</span>'+
            '</a>'+
            '</li>'
        ).replace(
            '{{page-next}}',
            '<li '+(page.next == null ? 'class="disabled"' : '')+'>'+
            '<a href="'+(page.next == null ? '#' : page.next.href)+'" aria-label="Next">'+
            '<span aria-hidden="true">&raquo;</span>'+
            '</a>'+
            '</li>'
        ).replace(
            '{{page-num}}', pageNum
        );

        container.html(temporaryHtml);
    }

    /**
     * Refresh the content
     */
    function refresh () {
        $('#thumbnail-container').loader('show');

        var params = {
            "q": $('#txt-search').val(),
            "_format": "json"
        };

        var selectedContentTypes = getSelectedContentTypes();

        for (var i in selectedContentTypes) {
            params["contenttypes[" + i + "]"] = selectedContentTypes[i];
        }

        if(previousCall !== null){
            previousCall.abort();
        }

        previousCall =
            $.get(Routing.generate('integrated_content_content_index', params), function(data) {
                render(data.items);
                renderPagination(data.pagination);
            }, 'json')
                .error(function(xhr, status){
                    if(status !== 'abort'){
                        $('#thumbnail-container').html('<p class="text-center">Error occured while loading content</p>');
                    }
                }).done(function () {
                $('#thumbnail-container').loader('hide');
            })
        ;
    }

    /**
     *
     * @returns {[string]}
     */
    function getSelectedContentTypes() {
        var selected = $('#type-search').val();

        if (selected) {
            return [selected];
        } else {

            var options = $('#type-search option');

            return $.map(options ,function(option) {
                if (option.value) {
                    return option.value;
                }
            });
        }
    }

    /**
     * Type ahead search handler
     */
    $(document).on('keyup', '#txt-search', function () {
        refresh();
    });

    /**
     * Content type switcher
     */
    $(document).on('change', '#type-search', function () {
        refresh();
    });

    /**
     * Pagination link click handler
     */
    $('#pagination-container').on('click', 'a', function(e){
        e.preventDefault();
        $('#thumbnail-container').loader('show');

        var href = $(this).attr('href');

        if(href !== '#'){
            $.get(href, function(data){
                render(data.items);
                renderPagination(data.pagination);
            }, 'json')
                .error(function(){
                    $('#thumbnail-container').html('<p class="text-center">Error occured while loading content</p>')
                })
                .done(function() {
                    $('#thumbnail-container').loader('hide');
                });
        }
    });

    /**
     * Image thumbnail click handler
     * insert the image into editor and close the window
     */
    $('#thumbnail-container').on('click', '.click-insert', function(e){
        e.preventDefault();

        var item = $(this);
        item.removeClass('click-insert');

        // Inject the image
        tinymce.activeEditor.insertContent(item[0].outerHTML);
        mcemodal.close();
    });

    /**
     * Here the plugin begins
     */
    $.get(Routing.generate('integrated_content_content_media_types', {'filter': options['mode']}), function (contentTypes) {
        var buttonHtml = '';

        if (contentTypes.length == 1) {
            buttonHtml = '<a target="_blank" href="'+ contentTypes[0].path +'" class="btn btn-primary btn-content-add" role="button">Upload new '+ contentTypes[0].name +'</a>';
        } else if (contentTypes.length > 1) {
            buttonHtml =
                '<button type="button" class="btn btn-primary dropdown-toggle" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">' +
                'Upload new ' + options['mode'] + '<span class="caret"></span></button>' +
                '<ul class="dropdown-menu">';

            for (var i in contentTypes) {
                buttonHtml += '<li><a target="_blank" class="btn-content-add" href="'+ contentTypes[i].path +'">'+ contentTypes[i].name +'</a></li>';
            }

            buttonHtml += '</ul>';
        }

        $('#button_wrap').html(buttonHtml);

        var searchHtml = '';
        if (contentTypes.length) {
            if (contentTypes.length > 1) {
                searchHtml += '<option value="">All content types</option>';
            }

            for (var i in contentTypes) {
                searchHtml += '<option value="'+ contentTypes[i].id +'">'+ contentTypes[i].name +'</option>';
            }
        } else {
            searchHtml += '<option>No content types available</option>';
        }


        $('#type-search').html(searchHtml);
        refresh();

        $('.btn-content-add').click(function(e) {
            e.preventDefault();

            tinymce.activeEditor.windowManager.open({
                title: 'Add a new '+ $(this).text(),
                url: $(this).attr('href') + '&_format=iframe.html',
                width: 800,
                height: 600
            }).on('close', function () {
                refresh();
            });
        });
    }).error(function() {
        $('#add_image_wrapper').html('<p>Failed to retrieve content types.</p>');
    }).done(function (){
        $('#add_image_wrapper').css('opacity',1);
    });
});
