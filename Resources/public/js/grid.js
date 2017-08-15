!function($, Routing, JSON) {

    /**
     * @type object|null
     */
    var previousCall = null;

    var $blockTarget = null;

    /**
     * @param {jQuery} $element
     */
    var initGrid = function($element) {
        $('[data-block-type="row"]', $element).each(function () {
            $(this).append(createRowButtons());
        });

        $('[data-block-type="column"]', $element).each(function () {
            $(this).append(createColumnButtons());
        });

        $element.append(createColumnButtons());

        $('[data-block-type="block"]', $element).each(function () {
            $(this).prepend(createBlockButtons());
        });
    };

    /**
     * @param {string} blockId
     * @param {jQuery} $before
     */
    var addBlock = function(blockId, $before) {
        var $block = $('<div>')
            .attr('data-id', blockId)
            .attr('data-block-type', 'block')
            .addClass('integrated-website-sortable');

        $before.before($block);

        refreshBlock($block);
    };

    /**
     * @param {jQuery} $block
     */
    var refreshBlock = function($block) {
        $.ajax({
            url: Routing.generate('integrated_block_block_show', { 'id': $block.data('id'), '_format': 'json' }),
            dataType: 'json',
            success: function(data) {
                $block.html(data.html);
                $block.prepend(createBlockButtons());
            },
            error: function (result) {
                // @todo error handling (INTEGRATED-420)
                alert('An error occurred rendering the block!');
                console.log(result.responseText);
            }
        });
    };
    /**
     * @return {jQuery}
     */
    var createRow = function () {
        return $('<div>')
            .addClass('row integrated-website-sortable')
            .attr('data-block-type', 'row');
    };

    /**
     * @param size
     */
    var createColumn = function(size) {
        return $('<div>')
            .addClass('integrated-website-col integrated-website-droppable col-sm-' + size)
            .attr('data-block-type', 'column')
            .data('size', size)
            .append(createColumnButtons());
    };

    /**
     * @return {jQuery}
     */
    var createRowButtons = function () {
        return $('<div class="row-buttons">')
            .append('<a href="javascript:;" class="integrated-website-helper-icon" data-action="integrated-website-cols-config" title="Configure columns"><span class="glyphicon glyphicon-wrench"></span></a>');
    };

    /**
     * @return {jQuery}
     */
    var createColumnButtons = function () {
        return $('<div class="block-buttons">')
            .append('<a href="javascript:;" class="integrated-website-helper-icon" data-action="integrated-website-block-add" title="Add block"><span class="glyphicon glyphicon-plus"></span></a>')
            .append('<a href="javascript:;" class="integrated-website-helper-icon" data-action="integrated-website-textblock-add" title="Add textblock"><span class="glyphicon glyphicon-font"></span></a>')
            .append('<a href="javascript:;" class="integrated-website-helper-icon" data-action="integrated-website-cols-add" title="Add columns"><span class="glyphicon glyphicon-th-large"></span></a>');

    };

    /**
     * @return {jQuery}
     */
    var createBlockButtons = function() {
        return $('<div class="integrated-website-block-options">')
            .append('<a href="javascript:;" class="integrated-website-helper-icon" data-action="integrated-website-block-edit" title="Edit block"><span class="glyphicon glyphicon-pencil"></span></a>')
            .append('<a href="javascript:;" class="integrated-website-helper-icon" data-action="integrated-website-block-remove" title="Remove block"><span class="glyphicon glyphicon-remove"></span></a>');
    };

    /**
     * Init grid buttons on page load
     */
    $('.integrated-website-grid').each(function() {
        initGrid($(this));
    });

    /**
     * Handle add block button
     */
    $(document).on('click', '[data-action="integrated-website-block-add"]', function(e) {
        e.preventDefault();

        $blockTarget = $(this).parent();

        $.ajax({
            url: Routing.generate('integrated_block_block_index', { '_format': 'json', 'limit': 10}),
            dataType: 'json',
            success: function(data) {

                var template = Handlebars.compile($('#integrated_website_template_modal_block_add').html());

                var dialog = bootbox.dialog({
                    title: 'Add block',
                    message: $(template(data))
                });
            }
        });
    });

    /**
     * Handle choose block button
     */
    $(document).on('click', '[data-action="integrated-website-block-choose"]', function(e) {
        addBlock($(this).attr('data-id'), $blockTarget);

        $('.modal.in').modal('hide');
    });

    /**
     * Handle remove block button
     */
    $(document).on('click', '[data-action="integrated-website-block-remove"]', function(e) {
        e.preventDefault();

        if (confirm('Are you sure?')) {
            $(this).closest('[data-block-type="block"]').remove();
        }
    });

    /**
     * Handle edit block button
     */
    $(document).on('click', '[data-action="integrated-website-block-edit"]', function(e) {
        e.preventDefault();

        $blockTarget = null;

        var blockId = $(this).closest('[data-block-type="block"]').data('id');
        createIframe(Routing.generate('integrated_block_block_edit', { 'id': blockId, '_format': 'iframe.html'}), 'Edit block');
    });

    /**
     * Handle new block button
     */
    $(document).on('click', '[data-action="integrated-website-block-new"]', function(e) {
        e.preventDefault();

        $('.modal.in').modal('hide');
        createIframe($(this).attr('href'), 'Add block');
    });

    /**
     * Handle new textblock button
     */
    $(document).on('click', '[data-action="integrated-website-textblock-add"]', function(e) {
        e.preventDefault();

        $blockTarget = $(this).parent();

        var pageId = $('[data-action="integrated-website-page-save"]').data('id');

        $('.modal.in').modal('hide');

        createIframe(Routing.generate('integrated_block_inline_text_block_create', { 'id': pageId}), 'New block');
    });

    /**
     * @param url
     * @param title
     */
    var createIframe = function(url, title) {
        var iFrame = $('<iframe frameborder="0" style="width: 100%; max-height:100%; display: none;">')
            .attr('src', url)
            .load(function(){
                var windowHeight = $(window).height() - 160;
                var iframeHeight = $(this).context.contentWindow.document.body.scrollHeight;

                $(this).height(windowHeight > iframeHeight && iframeHeight ? iframeHeight : windowHeight);

                $(this).show();
                $(this).siblings('.iframe-loading').hide();

                //handle cancel button in iframe
                $(this).contents().find('.integrated-cancel-button').click(function () {
                    $('.modal.in').modal('hide');
                });
            });

        var dialog = bootbox.dialog({
            title: title,
            message: $('<div>')
                .append('<div class="text-center iframe-loading"><i class="fa fa-spin fa-spinner"></i> Loading...</div>')
                .append(iFrame),
            size: 'large'
        });
    };

    /**
     * Handle block edit in iframe
     */
    document.addEventListener('block-added', function (e) {
        $('.modal.in').modal('hide');

        if ($blockTarget) {
            //new block inserted
            addBlock(e.detail, $blockTarget);
        } else {
            //update block
            $('[data-id="' + e.detail + '"]').each(function () {
                refreshBlock($(this));
            });
        }
    }, false);

    /**
     * Add columns
     */
    $(document).on('click', '[data-action="integrated-website-cols-add"]', function(e) {
        e.preventDefault();

        var total = parseInt(prompt('How many columns do you want?'));

        if (total) {
            var $row = createRow();

            for (var i = 0; i < total; i++) {
                var size = parseInt(prompt('Size of column ' + (i+1) + '?'));

                if (size) {
                    $row.append(createColumn(size));
                }
            }

            $row.append(createRowButtons());

            $(this).parent().before($row);
        }
    });

    /**
     * Update row and columns
     */
    $(document).on('click', '[data-action="integrated-website-cols-config"]', function(e) {
        e.preventDefault();

        var $row = $(this).closest('[data-block-type="row"]');
        var $oldColumns = $row.children('[data-block-type="column"]');

        var total = parseInt(prompt('How many columns do you want?', $oldColumns.length));

        if (total) {
            $row.children('.row-buttons').remove();

            for (var i = 0; i < total; i++) {
                var oldSize = '';

                if ($oldColumns[i]) {
                    oldSize = $($oldColumns[i]).data('size');
                }

                var size = parseInt(prompt('Size of column ' + (i+1) + '?', oldSize));

                if (size) {
                    if ($oldColumns[i]) {
                        $($oldColumns[i]).attr('class', 'col-sm-' + size).data('size', size);
                    } else {
                        $row.append(createColumn(size));
                    }
                }
            }

            //if there were more columns, remove the last columns
            $oldColumns.slice(i).remove();

            $row.append(createRowButtons());
        } else {
            //no count, so remove entire row
            $row.remove();
        }
    });

    /**
     * Make grid sortable and make sure serialized data is returned in correct way
     */
    $('.integrated-website-grid').integratedSortable({
        containerSelector: '.integrated-website-droppable',
        itemSelector: '.integrated-website-sortable',
        placeholder: '<div class="integrated-website-item-placeholder" style="height: 50px;"></div>',
        tolerance: 100,
        delay: 100,
        vertical: false,
        onDrop: function ($item, container, _super, event) {
            $item.removeClass(container.group.options.draggedClass).removeAttr("style");
            $("body").removeClass(container.group.options.bodyClass);

            //buttons should always come behind
            $item.after($item.prev('.block-buttons'));
        },
        serialize: function (parent, children) {
            if (parent.hasClass('integrated-website-grid')) {
                return {
                    'id': parent.data('id'),
                    'items': children
                }
            }
            if ('block' === parent.data('blockType')) {
                return {'block': parent.data('id')};
            } else if ('row' === parent.data('blockType')) {
                return {'row': {'columns': children}};
            } else if ('column' === parent.data('blockType')) {
                var data =  {'size': parent.data('size')};

                if (children) {
                    data.items = children;
                }

                return data;
            }
        }
    });

    /**
     * Modal eventlisteners
     */

    $(document).on('change', '#add_block_filters_form input, #add_block_filters_form select', function() {
        $('#add_block_filters_form').submit();
    });

    $(document).on('submit', '#add_block_filters_form', function(e) {
        e.preventDefault();

        refreshBlockData($(this).attr('action') + '&' + $(this).serialize());

        return false;
    });


    $(document).on('click', '#add_block_pagination a', function(e) {
        e.preventDefault();

        refreshBlockData($(this).attr('href'));

        return false;
    });

    function refreshBlockData(url) {
        $('#add_block_results').html('Loading blocks...');

        if(previousCall !== null){
            previousCall.abort();
            previousCall = null;
        }

        previousCall = $.ajax({
            url: url,
            dataType: 'json',
            success: function(data) {

                data.type = $('[name="integrated_block_filter[type][]"]').val();
                data.q = $('[name="integrated_block_filter[q]"]').val();
                data.channels = $('[name="integrated_block_filter[channels][]"]').val();

                var template = Handlebars.compile($('#integrated_website_template_modal_block_add').html());

                var html = $(template(data));

                $('#add_block_results').parent().replaceWith(html);
            }
        });
    }
}(window.jQuery, window.Routing, JSON);

var Integrated = Integrated || {};
