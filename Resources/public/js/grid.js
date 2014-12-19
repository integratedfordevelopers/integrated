!function ($) {

    $(document).on('click', '[data-action="add"]', function(e) {

        e.preventDefault();

        var element = $(this);

        var collection = $('#' + element.attr('data-collection-id'));

        var index = parseInt(element.attr('data-index')) || collection.children().length;

        var prototype = element.attr('data-prototype');
        var prototypeName = element.attr('data-prototype-name');

        var form = prototype.replace(new RegExp(prototypeName, 'g'), index);

        collection.append(form);

        element.attr('data-index', ++index);
    });

    $(document).on('click', '[data-action="remove"]', function(e) {

        e.preventDefault();

        $('#' + $(this).attr('data-element-id')).remove();
    });

}(window.jQuery);
