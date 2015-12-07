!function($, Routing) {

    $('[data-action="integrated-website-page-save"]').click(function(e) {
        e.preventDefault();

        var menus = [];

        $('.integrated-website-menu').each(function() {
            var menu = Integrated.Menu.create($(this));

            menus.push(menu.getData());
        });

        $.ajax({
            type: 'POST',
            url: Routing.generate('integrated_website_page_edit', { 'id': $(this).attr('data-id') }),
            data: JSON.stringify({
                'menus': menus
            }),
            error: function(result) {
                // @todo error handling (INTEGRATED-420)
                alert('An error has occurred!');
                console.log(result.responseText);
            }
        });

        $('#' + $(this).attr('data-element-id')).submit();
    });

}(window.jQuery, window.Routing);

var Integrated = Integrated || {};
