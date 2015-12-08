!function($, Routing) {

    $('[data-action="integrated-website-page-save"]').click(function(e) {
        e.preventDefault();

        var menus = [];

        $('.integrated-website-menu').each(function() {
            menus.push(Integrated.Menu.create($(this)).getData());
        });

        $.ajax({
            type: 'POST',
            url: Routing.generate('integrated_website_menu_save'),
            data: JSON.stringify({
                'menu': menus
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
