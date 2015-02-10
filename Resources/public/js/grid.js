!function($) {

    var createBlock = function(name, index, value, element) {
        var template = Handlebars.compile($('#block-template').html());

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
        element.find('.helper').prepend('<a href="javascript:;" class="icon" data-action="remove-block" data-element-id="' + element.attr('id') + '" title="Remove block"><span class="glyphicon glyphicon-remove"></span></a>');
    }

    var addButtons = function(collection) {
        collection.parent()
            .append('<a href="javascript:;" class="icon" data-action="add-block" data-collection-id="' + collection.attr('id') + '" title="Add block"><span class="glyphicon glyphicon-plus"></span></a>')
            .append('<a href="javascript:;" class="icon" data-action="add-columns" data-collection-id="' + collection.attr('id') + '" title="Add columns"><span class="glyphicon glyphicon-th-large"></span></a>');
    }

    var addConfigButton = function(row) {
        row.append('<a href="javascript:;" class="icon" data-action="configure-columns" title="Configure columns"><span class="glyphicon glyphicon-wrench"></span></a>');
    }

    var getCollection = function(element) {
        var button = element.closest('.section').children('[data-action="add-block"]');

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

            $(element).children('.helper').children('input[data-field="order"]').val(index);
        });
    }

    $('.sortable').sortable({
        connectWith: '.sortable',
        placeholder: 'placeholder',
        forcePlaceholderSize: true,
        scroll: false,
        opacity: 0.8,

        stop: function(e, ui) {
            var collection = getCollection(ui.item);

            if (ui.item.hasClass('block')) {

                var index = incrementIndex(collection);
                var name = collection.attr('data-name') + '[' + index + ']';

                var value = ui.item.find('input[data-field="block"]').val();
                var element = ui.item.find('.element').html();

                var block = createBlock(name, index, value, element);

                addRemoveButton(block);

                ui.item.replaceWith(block);
            }

            updateOrder(collection);
        }
    });

    $('.sortable .block').each(function(index, item) {
        addRemoveButton($(item));
    });

    $('.sortable').each(function(index, item) {
        addButtons($(item));
    });

    addConfigButton($('.section .row'));

    $(document).on('click', '[data-action="add-block"]', function(e) {
        e.preventDefault();

        var value = prompt('Please provide the id');

        if (value) {
            var collection = $('#' + $(this).attr('data-collection-id'));
            var element = '<div class="panel panel-default"><div class="panel-heading">[block]</div><div class="panel-body"></div></div>'; // @todo

            addBlock(collection, value, element);
        }
    });

    $(document).on('click', '[data-action="remove-block"]', function(e) {
        e.preventDefault();

        if (confirm('Are you sure?')) {
            removeBlock($(this));
        }
    });

    $(document).on('click', '[data-action="save"]', function(e) {
        e.preventDefault();

        $('#' + $(this).attr('data-element-id')).submit();
    });

    $(document).on('click', '[data-action="add-columns"]', function(e) {
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

                var template = Handlebars.compile($('#columns-template').html());

                var html = template({
                    id: id,
                    name: name,
                    index: index,
                    columns: data
                });

                html = $(html);

                collection.append(html);

                updateOrder(collection);

                html.find('.sortable').each(function(index, item) {
                    addButtons($(item));
                });

                addConfigButton(html.closest('.row'));

                //$('.sortable').sortable('refresh');
            }
        }
    });

    $(document).on('click', '[data-action="configure-columns"]', function(e) {
        e.preventDefault();

        var oldRow = $(this).closest('.row');
        var oldColumns = oldRow.children('.columns').children('.col');

        var collection = getCollection(oldRow);
        var index = parseInt(oldRow.children('.helper').children('input[data-field="order"]').val());
        var name = collection.attr('data-name') + '[' + index + ']';
        var id = getId(name);

        var total = prompt('How many columns do you want?', oldColumns.length);

        if (total == 0) {
            oldRow.remove();

        } else if (total = parseInt(total)) {

            var data = [];

            for (var i = 0; i < total; i++) {

                var oldSize = $(oldColumns[i]).children('input[data-field="size"]').val();

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

                var template = Handlebars.compile($('#columns-template').html());

                var html = template({
                    id: id,
                    name: name,
                    index: index,
                    columns: data
                });

                html = $(html);

                var newColumns = html.children('.columns').children('.col');

                for (i = 0; i < total; i++) {
                    $(newColumns[i]).children('.section').children('.sortable').append(
                        $(oldColumns[i]).children('.section').children('.sortable').children()
                    );
                }

                oldRow.replaceWith(html);

                updateOrder(collection);

                html.find('.sortable').each(function(index, item) {
                    addButtons($(item));
                });

                addConfigButton(html.closest('.row'));

                //$('.sortable').sortable('refresh');
            }
        }
    });

}(window.jQuery);
