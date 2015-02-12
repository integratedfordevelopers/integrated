!function($) {

    var createBlock = function(name, index, value, element) {
        var template = Handlebars.compile($('#integrated_website_template_block').html());

        var html = template({
            id: getId(name),
            name: name,
            index: index,
            value: value,
            element: element
        });

        return $(html);
    }

    var addBlock = function(collection, value, element) {
        var index = incrementIndex(collection);
        var name = collection.attr('data-name') + '[' + index + ']';

        var block = createBlock(name, index, value, element);

        addRemoveButton(block);

        collection.append(block);

        updateOrder(collection);
    }

    var removeBlock = function(element) {
        var collection = getCollection(element);

        $('#' + element.attr('data-element-id')).remove();

        updateOrder(collection);
    }

    var addRemoveButton = function(element) {
        element.find('.integrated-website-block-element').before(
            '<a href="javascript:;" class="integrated-website-helper-icon" data-action="integrated-website-block-remove" data-element-id="' + element.attr('id') + '" title="Remove block">' +
                '<span class="glyphicon glyphicon-remove"></span>' +
            '</a>'
        );
    }

    var addButtons = function(collection) {
        collection.after(
            '<a href="javascript:;" class="integrated-website-helper-icon" data-action="integrated-website-block-add" data-collection-id="' + collection.attr('id') + '" title="Add block">' +
                '<span class="glyphicon glyphicon-plus"></span>' +
            '</a>' +
            '<a href="javascript:;" class="integrated-website-helper-icon" data-action="integrated-website-cols-add" data-collection-id="' + collection.attr('id') + '" title="Add columns">' +
                '<span class="glyphicon glyphicon-th-large"></span>' +
            '</a>'
        );
    }

    var addConfigButton = function(row) {
        row.append(
            '<a href="javascript:;" class="integrated-website-helper-icon" data-action="integrated-website-cols-config" title="Configure columns">' +
                '<span class="glyphicon glyphicon-wrench"></span>' +
            '</a>'
        );
    }

    var getCollection = function(element) {
        var button = element.closest('.integrated-website-section').children('[data-action="integrated-website-block-add"]');

        return $('#' + button.attr('data-collection-id'));
    }

    var getId = function(name) {
        return name.replace(/]\[/g, '_').replace('[', '_').replace(/]$/, '');
    }

    var incrementIndex = function(collection) {
        var index = parseInt(collection.attr('data-index')) || collection.children().length;

        collection.attr('data-index', (index+1));

        return index;
    }

    var updateOrder = function(collection) {
        collection.children().each(function(index, element) {

            $(element).children('input[data-field="integrated-website-item-order"]').val(index);
        });
    }

    $('.integrated-website-sortable').sortable({
        connectWith: '.integrated-website-sortable',
        placeholder: 'integrated-website-item-placeholder',
        forcePlaceholderSize: true,
        scroll: false,
        opacity: 0.8,

        stop: function(e, ui) {
            var collection = getCollection(ui.item);

            if (ui.item.hasClass('integrated-website-block')) {

                var index = incrementIndex(collection);
                var name = collection.attr('data-name') + '[' + index + ']';

                var value = ui.item.find('input[data-field="integrated-website-block"]').val();
                var element = ui.item.find('.integrated-website-block-element').html();

                var block = createBlock(name, index, value, element);

                addRemoveButton(block);

                ui.item.replaceWith(block);
            }

            updateOrder(collection);
        }
    });

    $('.integrated-website-sortable .integrated-website-block').each(function(index, item) {
        addRemoveButton($(item));
    });

    $('.integrated-website-sortable').each(function(index, item) {
        addButtons($(item));
    });

    addConfigButton($('.integrated-website-section .integrated-website-row'));

    $(document).on('click', '[data-action="integrated-website-block-add"]', function(e) {
        e.preventDefault();

        var value = prompt('Please provide the id');

        if (value) {
            var collection = $('#' + $(this).attr('data-collection-id'));
            var element = '<div class="panel panel-default"><div class="panel-heading">[block]</div><div class="panel-body"></div></div>'; // @todo

            addBlock(collection, value, element);
        }
    });

    $(document).on('click', '[data-action="integrated-website-block-remove"]', function(e) {
        e.preventDefault();

        if (confirm('Are you sure?')) {
            removeBlock($(this));
        }
    });

    $(document).on('click', '[data-action="integrated-website-save"]', function(e) {
        e.preventDefault();

        $('#' + $(this).attr('data-element-id')).submit();
    });

    $(document).on('click', '[data-action="integrated-website-cols-add"]', function(e) {
        e.preventDefault();

        var collection = $('#' + $(this).attr('data-collection-id'));
        var index = incrementIndex(collection);
        var name = collection.attr('data-name') + '[' + index + ']';
        var id = getId(name);

        var total = parseInt(prompt('How many columns do you want?'));

        if (total) {

            var data = [];

            for (var i = 0; i < total; i++) {

                var size = parseInt(prompt('Size of column ' + (i+1) + '?'));

                if (size) {

                    data.push({
                        id: id,
                        name: name,
                        index: i,
                        size: size
                    });
                }
            }

            if (total == data.length) {

                var template = Handlebars.compile($('#integrated_website_template_cols').html());

                var html = template({
                    id: id,
                    name: name,
                    index: index,
                    columns: data
                });

                html = $(html);

                collection.append(html);

                updateOrder(collection);

                html.find('.integrated-website-sortable').each(function(index, item) {
                    addButtons($(item));
                });

                addConfigButton(html.closest('.row'));

                //$('.sortable').sortable('refresh');
            }
        }
    });

    $(document).on('click', '[data-action="integrated-website-cols-config"]', function(e) {
        e.preventDefault();

        var oldRow = $(this).closest('.integrated-website-row');
        var oldColumns = oldRow.children('.integrated-website-cols').children('.integrated-website-col');

        var collection = getCollection(oldRow);
        var index = parseInt(oldRow.children('input[data-field="integrated-website-item-order"]').val());
        var name = collection.attr('data-name') + '[' + index + ']';
        var id = getId(name);

        var total = prompt('How many columns do you want?', oldColumns.length);

        if (total == 0) {
            oldRow.remove();

        } else if (total = parseInt(total)) {

            var data = [];

            for (var i = 0; i < total; i++) {

                var oldSize = $(oldColumns[i]).children('input[data-field="integrated-website-col-size"]').val();

                var size = parseInt(prompt('Size of column ' + (i+1) + '?', oldSize));

                if (size) {

                    data.push({
                        id: id,
                        name: name,
                        index: i,
                        size: size
                    });
                }
            }

            if (total == data.length) {

                var template = Handlebars.compile($('#integrated_website_template_cols').html());

                var html = template({
                    id: id,
                    name: name,
                    index: index,
                    columns: data
                });

                html = $(html);

                var newColumns = html.children('.integrated-website-cols').children('.integrated-website-col');

                for (i = 0; i < total; i++) {
                    $(newColumns[i]).children('.integrated-website-section').children('.integrated-website-sortable').append(
                        $(oldColumns[i]).children('.integrated-website-section').children('.integrated-website-sortable').children()
                    );
                }

                oldRow.replaceWith(html);

                updateOrder(collection);

                html.find('.integrated-website-sortable').each(function(index, item) {
                    addButtons($(item));
                });

                addConfigButton(html.closest('.integrated-website-row'));

                //$('.sortable').sortable('refresh');
            }
        }
    });

}(window.jQuery);
