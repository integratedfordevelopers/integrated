!function($, Routing) {

    var success = 0;
    var target = null;

    $('[data-action="integrated-website-page-save"]').click(function(e) {
        e.preventDefault();

        success = 0;
        target = $(this).data('target');

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
            success: handleSuccess,
            error: handleError
        });
    };

    var saveGrids = function (pageId) {
        var grids = $('.integrated-website-grid').integratedSortable('serialize').get();

        $.ajax({
            type: 'POST',
            url: Routing.generate('integrated_website_grid_save'),
            data: JSON.stringify({
                'grids': grids,
                'page': pageId
            }),
            success: handleSuccess,
            error: handleError
        });
    };

    var handleSuccess = function () {
        if (2 === ++success && target) {
            window.location.replace(target);
        }
    };

    var handleError = function (result) {
        // @todo error handling (INTEGRATED-420)
        alert('An error has occurred saving the grid(s)!');
        console.log(result.responseText);
    }

}(window.jQuery, window.Routing);

var Integrated = Integrated || {};
