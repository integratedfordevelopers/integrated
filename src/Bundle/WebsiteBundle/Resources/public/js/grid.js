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
     * @param {jQuery} $element
     */
    var initChannelBlockGrid = function($element) {
        $('[data-block-type="channel"]', $element).each(function () {
            $(this).prepend(createChannelBlockButtons());
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
            url: Routing.generate('integrated_block_block_show', { 'id': $block.data('id') }),
            dataType: 'json',
            success: function(data) {
                $block.html(data.html);
                if ($block.data('block-type') == 'channel') {
                    $block.prepend(createChannelBlockButtons());
                } else {
                    $block.prepend(createBlockButtons());
                }
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
        var $element = $('<div>')
            .addClass('integrated-website-col integrated-website-droppable col-sm-' + size)
            .attr('data-block-type', 'column')
            .data('size', size)
            .append(createColumnButtons());

        initSortable($element);

        return $element;
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
     * @return {jQuery}
     */
    var createChannelBlockButtons = function() {
        return $('<div class="integrated-website-block-options">')
            .append('<a href="javascript:;" class="integrated-website-helper-icon" data-action="integrated-website-block-edit" title="Edit block"><span class="glyphicon glyphicon-pencil"></span></a>');
    };

    /**
     * Init grid buttons on page load
     */
    $('.integrated-website-grid').each(function() {
        initGrid($(this));
    });

    $('.integrated-website-channel-block-grid').each(function() {
        initChannelBlockGrid($(this));
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

        $('.modal').modal('hide');
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

        var blockId = $(this).closest('[data-block-type="block"],[data-block-type="channel"]').data('id');
        createIframe(Routing.generate('integrated_block_block_edit', { 'id': blockId, '_format': 'iframe.html'}), 'Edit block');
    });

    /**
     * Handle new block button
     */
    $(document).on('click', '[data-action="integrated-website-block-new"]', function(e) {
        e.preventDefault();

        $('.modal').modal('hide');
        createIframe($(this).attr('href'), 'Add block');
    });

    /**
     * Handle new channel block button
     */
    $(document).on('click', '[data-action="integrated-website-block-new-channel-block"]', function(e) {
        e.preventDefault();

        button = e.target;
        id = $(button).data('id');
        name = $(button).data('name');
        className = $(button).data('class');
        csrfToken = $(button).data('csrf-token');

        $blockTarget = $('#create-channel-block-'+id);

        $.post(
            Routing.generate('integrated_block_block_new_channel_block'),
            { 'id': id, 'name': name, 'class': className, 'csrf_token': csrfToken },
            function() {
                $('.modal').modal('hide');
                createIframe(Routing.generate('integrated_block_block_edit', { 'id': $(button).data('id'), '_format': 'iframe.html'}), 'Edit block');
            }
        );
    });

    /**
     * Handle new textblock button
     */
    $(document).on('click', '[data-action="integrated-website-textblock-add"]', function(e) {
        e.preventDefault();

        $blockTarget = $(this).parent();

        var pageId = $('[data-action="integrated-website-page-save"]').data('id');

        $('.modal').modal('hide');

        createIframe(Routing.generate('integrated_block_inline_text_block_create', { 'id': pageId}), 'New block');
    });

    /**
     * @param url
     * @param title
     */
    var createIframe = function(url, title) {
        var iFrame = $('<iframe frameborder="0" style="width: 100%; max-height:100%; display: auto;">')
            .attr('src', url)
            .on('load', function(e){
                var windowHeight = $(window).height() - 160;
                var iframeHeight = e.target.contentWindow.document.body.scrollHeight;

                $(this).height(windowHeight > iframeHeight && iframeHeight ? iframeHeight : windowHeight);

                $(this).show();
                $(this).siblings('.iframe-loading').hide();

                //handle cancel button in iframe
                $(this).contents().find('.integrated-cancel-button').click(function () {
                    $('.modal').modal('hide');
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
     * @param $element
     */
    var initSortable = function($element) {
        $element.sortable({
            connectWith: '.integrated-website-droppable',
            placeholder: 'integrated-website-item-placeholder',
            items: '.integrated-website-sortable',
            scroll: false,
            opacity: 0.7,
            cursor: 'move',
            cursorAt: { top: 20, left: 20 },
            tolerance: 'pointer',
            helper: function(e, helper) {
                return helper.clone().css({'width': '300px'});
            },
            stop: function(e, ui) {
                var $item = $(ui.item);
                //buttons should always come behind
                $item.after($item.prev('.block-buttons'));
            }
        });
    };

    /**
     * Handle block edit in iframe
     */
    document.addEventListener('block-added', function (e) {
        $('.modal').modal('hide');

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
                        $($oldColumns[i]).attr('class', 'integrated-website-col integrated-website-droppable col-sm-' + size).data('size', size);
                    } else {
                        $row.append(createColumn(size));
                    }
                }
            }

            //if there were more columns, remove the last columns
            $oldColumns
                .slice(i)
                .sortable('destroy')
                .remove();

            $row.append(createRowButtons());
        } else {
            //no count, so remove entire row
            $row.remove();
        }
    });

    /**
     * Make grid sortable
     */
    initSortable($('.integrated-website-droppable'));

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
