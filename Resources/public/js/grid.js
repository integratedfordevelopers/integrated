!function ($) {

    $(document).on('click', '[data-action="add"]', function(e) {

        var collection = $('#' + $(this).attr('data-collection-id'));

        var prototype = collection.attr('data-prototype');

        var form = prototype.replace(new RegExp($(this).attr('data-prototype-name'), 'g'), collection.children().length);

        collection.append(form);
    });

    $(document).on('click', '[data-action="remove"]', function(e) {

        $('#' + $(this).attr('data-element-id')).remove();
    });

}(window.jQuery);
