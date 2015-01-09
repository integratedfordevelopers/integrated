!function ($) {

    $('.sortable').each(function(index, item) {

        var element = $(item);
        element.parent()
            .append('<a href="javascript:;" class="btn btn-primary" data-action="add-block" data-collection-id="' + element.attr('id') + '">Add block</a>')
            .append('<a href="javascript:;" class="btn btn-primary" data-action="add-columns" data-collection-id="' + element.attr('id') + '">Add columns</a>');
    });

    $('.sortable .block').each(function(index, item) {

        var element = $(item);
        element.prepend('<a href="javascript:;" class="btn btn-danger" data-action="remove" data-element-id="' + element.attr('id') + '">Remove block</a>');
    });

    $('.sortable .row').each(function(index, item) {

        var element = $(item);
        element.prepend('<a href="javascript:;" class="btn btn-danger" data-action="remove" data-element-id="' + element.attr('id') + '">Remove row</a>');
    });

    $('.sortable .row [class^="col-"]').each(function(index, item) {

        var element = $(item);
        element.prepend('<a href="javascript:;" class="btn btn-danger" data-action="remove" data-element-id="' + element.attr('id') + '">Remove column</a>');
    });

    $('.sortable').sortable({
        connectWith: '.sortable',

        stop: function(e, ui) {

            ui.item.closest('.sortable').children().each(function(index, item) {

                $(item).children('input[data-field="order"]').val(index);
            });
        }
    });

    $(document).on('click', '[data-action="add-block"]', function(e) {

        e.preventDefault();

        var element = $(this);
        var collection = $('#' + element.attr('data-collection-id'));
        // var index = parseInt(collection.attr('data-index')) || collection.children().length;
        var index = collection.children().length;
        var name = collection.attr('data-name') + '[' + index + ']';

        var template = Handlebars.compile($('#block-template').html());

        var html = template({
            id   : name.replace(/]\[/g, '_').replace('[', '_').replace(/]$/, ''),
            name : name,
            index: index
        });

        collection.append(html);
        //collection.attr('data-index', ++index);
    });

    $(document).on('click', '[data-action="add-columns"]', function(e) {

        e.preventDefault();

        var element = $(this);
        var collection = $('#' + element.attr('data-collection-id'));
        var index = collection.children().length;
        var name = collection.attr('data-name') + '[' + index + ']';

        var total = parseInt(prompt('How many columns do you want?'));

        if (total) {

            var columns = [];

            for (var i = 0; i < total; i++) {

                var size = parseInt(prompt('Size of column ' + i + 1 + '?'));

                columns.push({
                    id   : name.replace(/]\[/g, '_').replace('[', '_').replace(/]$/, ''),
                    name : name,
                    index: i,
                    size: size
                });
            }

            var template = Handlebars.compile($('#columns-template').html());

            var html = template({
                id   : name.replace(/]\[/g, '_').replace('[', '_').replace(/]$/, ''),
                name : name,
                index: index,
                columns: columns
            });

            collection.append(html)
        }
    });

    $(document).on('click', '[data-action="remove"]', function(e) {

        e.preventDefault();
        $('#' + $(this).attr('data-element-id')).remove();
    });

}(window.jQuery);
