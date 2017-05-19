!function($, Routing) {
    $('[data-action="integrated-website-page-save"]').click(function(e) {
        e.preventDefault();

        saveMenus();
        saveGrids($(this).data('id'));
    });

    var saveMenus = function () {
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
                alert('An error has occurred saving the menu(s)!');
                console.log(result.responseText);
            }
        });
    };

    var saveGrids = function (pageId) {
        var grids = $('.integrated-website-grid').jsortable('serialize').get();

        $.ajax({
            type: 'POST',
            url: Routing.generate('integrated_website_grid_save'),
            data: JSON.stringify({
                'grids': grids,
                'page': pageId
            }),
            error: function(result) {
                // @todo error handling (INTEGRATED-420)
                alert('An error has occurred saving the grid(s)!');
                console.log(result.responseText);
            }
        });
    };

}(window.jQuery, window.Routing);

var Integrated = Integrated || {};
