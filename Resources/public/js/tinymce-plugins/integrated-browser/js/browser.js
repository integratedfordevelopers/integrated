$(document).ready(function() {

    /**
     * tinimce instance from the top level window object
     * @type object
     */
    var tinymce     = top.tinymce;

    /**
     * window modal object created by tinymce object
     * @type object
     */
    var mcemodal    = tinymce.activeEditor.windowManager.getWindows()[0];

    /**
     * @type {Object}
     */
    var options = tinymce.activeEditor.windowManager.getParams();

    /**
     * @type object|null
     */
    var previousCall = null;


    /**
     * Get the query parameter passed from the plugin loader
     * @param  string name  query parameter name
     * @return string       query parameter value
     */
    function getQuery(name) {
        name = name.replace(/[\[]/, "\\[").replace(/[\]]/, "\\]");
        var regex = new RegExp("[\\?&]" + name + "=([^&#]*)"),
            results = regex.exec(location.search);
        return results === null ? "" : decodeURIComponent(results[1].replace(/\+/g, " "));
    }

    /**
     * Render image lists into thumbnail container
     * @param  object images    images collection retreived from the API
     * @return void
     */
    function renderImage(images){
        var rowTemplate = '<div class="row">{{thumbnails}}</div>';
        var thumbnailTemplate =
            '<div class="col-sm-3">'+
            '<div class="thumbnail">'+
            '<div class="thumbnail-img">'+
            '<img src="{{img-source}}" class="img-responsive btn-insert-image" title="{{img-title}}" alt="{{img-alt}}" data-integrated-id="{{img-id}}" />'+
            '</div>'+
            '</div>'+
            '</div>';

        var container       = $('#thumbnail-container');
        var temporaryHtml   = '';
        var temporaryThumb  = '';

        for(var i = 0; i < images.length; i++){
            temporaryThumb += thumbnailTemplate.replace(
                /\{\{img-source\}\}/g,
                Routing.generate(
                    'app_storage_redirect',
                    {
                        id: images[i].id,
                        type: images[i].type,
                        ext: images[i].extension
                    },
                    true
                )
            ).replace(
                /\{\{img-alt\}\}/g,
                images[i].title
            ).replace(
                /\{\{img-id\}\}/g,
                images[i].id
            ).replace(
                /\{\{img-title\}\}/g,
                images[i].title
            ).replace(
                /\{\{img-alt\}\}/g,
                images[i].alternate
            );

            if(i % 4 == 3 || i == images.length - 1){
                temporaryHtml  += rowTemplate.replace(/\{\{thumbnails\}\}/g, temporaryThumb);
                temporaryThumb  = '';
            }
        }

        if(images.length == 0){
            temporaryHtml = '<p class="text-center">No images found.</p>';
        }

        container.html(temporaryHtml);
    }

    /**
     * Render pagination part of the image browser
     * @param  object data  Pagination data retreived from the API
     * @return void
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
        if(page.pageCount == 1){
            container.html('');
            thumbnailContainer.css('height', '520px');
            return;
        }else{
            thumbnailContainer.removeAttr('style');
        }

        /** process the template to render pagination link */
        for(
            var i = page.page - 2 - (page.pageCount - page.page < 2 ? 2 - (page.pageCount - page.page) : 0);
            i <= page.page + 2 + (page.page <= 2 ? 3 - page.page : 0);
            i++
        ){
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

    refreshImages = function() {
        $('#thumbnail-container').loader('show');

        var params = {
            "contenttypes[]": $('#type-search').val(),
            "q": $('#txt-search').val(),
            "_format": "json"
        };

        if(previousCall !== null){
            previousCall.abort();
            previousCall = null;
        }

        previousCall =
            $.get(Routing.generate('integrated_content_content_index', params), function(data) {
                renderImage(data.items);
                renderPagination(data.pagination);
            }, 'json')
                .error(function(xhr, status){
                    if(status !== 'abort'){
                        $('#thumbnail-container').html('<p class="text-center">Error occured while loading image</p>');
                    }
                }).done(function () {
                $('#thumbnail-container').loader('hide');
            })
        ;
    };

    /**
     * Type ahead search handler
     */
    $(document).on('keyup', '#txt-search', function () {
        refreshImages();
    });

    $(document).on('change', '#type-search', function () {
        refreshImages();
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
                renderImage(data.items);
                renderPagination(data.pagination);
            }, 'json')
                .error(function(){
                    $('#thumbnail-container').html('<p class="text-center">Error occured while loading image</p>')
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
    $('#thumbnail-container').on('click', '.btn-insert-image', function(e){
        e.preventDefault();
        var image = $(this);
        image.removeClass('btn-insert-image');

        // Inject the image
        tinymce.activeEditor.insertContent(image[0].outerHTML);
        mcemodal.close();
    });

    /**
     * Here the plugin begins
     */
    $.ajax({
        url: Routing.generate(
            'integrated_content_content_media_types', {'filter': options['mode']}
        ),
        dataType: 'json',
        success: function (images) {

            var buttonHtml = '';
            if (images.length == 1) {
                var image = images[0];
                buttonHtml = '<a target="_blank" href="'+image.path+'" class="btn btn-primary" role="button">Upload new '+image.name+'</a>';
            } else if (images.length > 1) {
                buttonHtml = '<button type="button" class="btn btn-primary dropdown-toggle" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">' +
                    'Upload new image <span class="caret"></span></button>' +
                    '<ul class="dropdown-menu">';
                for (var i in images) {
                    var image = images[i];
                    buttonHtml += '<li><a target="_blank" href="'+image.path+'">'+image.name+'</a></li>';
                }
                buttonHtml += '</ul>';
            }
            $('#button_wrap').html(buttonHtml);

            var searchHtml = '';
            for (var i in images) {
                var image = images[i];
                searchHtml += '<option value="'+image.id+'">'+image.name+'</option>';
            }

            $('#type-search').html(searchHtml);
            refreshImages();
            $('#add_image_wrapper').css('opacity',1);
        }
    });
});
