$(document).ready(function(){

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
                    '<div class="thumbnail-img"><img src="{{img-source}}" alt="{{img-alt}}"></div>'+
                    '<div class="caption">'+
                        '<p class="text-center"><a href="{{img-source}}" class="btn btn-primary btn-sm btn-insert-image">Insert</a></p>'+
                    '</div>'+
                '</div>'+
            '</div>';

        var container       = $('#thumbnail-container');
        var temporaryHtml   = '';
        var temporaryThumb  = '';

        for(var i = 0; i < images.length; i++){

            temporaryThumb += thumbnailTemplate.replace(
                /\{\{img-source\}\}/g,
                images[i].image == null ? 'image/thumbnail.svg' : images[i].image
            ).replace(
                /\{\{img-alt\}\}/g,
                images[i].title
            );

            if(i % 4 == 3 || i == images.length - 1){
                temporaryHtml  += rowTemplate.replace(/\{\{thumbnails\}\}/g, temporaryThumb);
                temporaryThumb  = '';
            }
        }

        if(images.length == 0){
            temporaryHtml = '<p class="text-center">No images found!</p>';
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
     * The search handler
     */
    $('#btn-search').click(function(){
        var keyword = $('#txt-search').val();
        var url     = Routing.generate('integrated_content_content_index', {"contenttypes[]": "image", "_format": "json", "q": keyword});

        $('#thumbnail-container').loader('show');
        $.get(url, function(data){
            renderImage(data.items);
            renderPagination(data.pagination);
            $('#thumbnail-container').loader('hide');

        }, 'json')
        .error(function(){
            $('#thumbnail-container').html('<p class="text-center">Error occured while loading image</p>')
        });
    });

    /**
     * Type ahead search handler
     */
    var previousCall = null;
    $('#txt-search').keyup(function(){
        var keyword = $(this).val();
        var url     = Routing.generate('integrated_content_content_index', {"contenttypes[]": "image", "_format": "json", "q": keyword});

        $('#thumbnail-container').loader('show');
        if(previousCall !== null){
            previousCall.abort();
            previousCall = null;
        }

        previousCall = $.get(url, function(data){
            renderImage(data.items);
            renderPagination(data.pagination);
            $('#thumbnail-container').loader('hide');

        }, 'json')
        .error(function(xhr, status){
            if(status !== 'abort'){
                $('#thumbnail-container').html('<p class="text-center">Error occured while loading image</p>');
            }
        });
    });

    /**
     * Disable default search form handler
     */
    $('#form-search').submit(function(e){
        e.preventDefault();
    });

    /**
     * Pagiation link click handler
     */
    $('#pagination-container').on('click', 'a', function(e){
        e.preventDefault();
        $('#thumbnail-container').loader('show');

        var href = $(this).attr('href');

        if(href !== '#'){
            $.get(href, function(data){
                renderImage(data.items);
                renderPagination(data.pagination);
                $('#thumbnail-container').loader('hide');
            }, 'json')
            .error(function(){
                $('#thumbnail-container').html('<p class="text-center">Error occured while loading image</p>')
            });
        }
    });

    /**
     * Image thumbnail click handler
     * insert the image into editor and close the window
     */
    $('#thumbnail-container').on('click', '.btn-insert-image', function(e){
        e.preventDefault();
        var image = $(this).attr('href');

        tinymce.activeEditor.insertContent('<img src="'+image+'" class="img-responsive" />');
        mcemodal.close();
    });

    /**
     * Initial image rendering process
     */
    $('#thumbnail-container').loader('show');

    $.get(Routing.generate('integrated_content_content_index', {"contenttypes[]": "image", "_format": "json"}), function(data){
        renderImage(data.items);
        renderPagination(data.pagination);
        $('#thumbnail-container').loader('hide');
    }, 'json')
    .error(function(){
        $('#thumbnail-container').html('<p class="text-center">Error occured while loading image</p>')
    });
});