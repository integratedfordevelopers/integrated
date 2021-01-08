!function($, Routing) {

    var success = 0;
    var target = null;

    document.querySelectorAll('[data-action="integrated-website-page-save"]').forEach(item =>
        item.addEventListener('click', function(e) {
            e.preventDefault();

            success = 0;
            target = $(this).data('target');

            saveMenus();
            saveGrids($(this).data('id'));
        })
    );

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

    var getGridItems = function ($element) {
        var items = [];

        $element.each(function(i, child) {
            var $child = $(child);

            if ('block' === $child.data('block-type')) {
                items.push({
                    'block': $child.data('id')
                });
            } else if ('row' === $child.data('block-type')) {
                items.push({
                    'row': {
                        'columns': getGridItems($child.children('.integrated-website-col'))
                    }
                });
            } else if ('column' === $child.data('block-type')) {
                items.push({
                    'size': $child.data('size'),
                    'items': getGridItems($child.children('.integrated-website-sortable'))
                });
            }
        });

        return items;
    };

    var saveGrids = function (pageId) {
        var grids = [];

        $('.integrated-website-grid').each(function() {
            grids.push({
                'id': $(this).data('id'),
                'items': getGridItems($(this).children('.integrated-website-sortable'))
            });
        });

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
