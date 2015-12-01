!function($, Routing, JSON) {

    var Grid = function(element) {
        this.element = element;
    };

    Grid.prototype.load = function(data, success) {
        var element = this.element;

        $.ajax({
            type: 'POST',
            url: Routing.generate('integrated_website_grid_render'),
            data: data,
            success: function(result) {
                element.html(result.html);

                if ($.isFunction(success)) {
                    success();
                }
            },
            error: function(result) {
                // @todo error handling (INTEGRATED-420)
                alert('An error has occurred!');
                console.log(result.responseText);
            }
        });
    };

    $('.integrated-website-grid').each(function() {
        var element = $(this);

        var grid = new Grid(element);
        var script = element.find('script[type="text/json"]');

        grid.load(script.html(), function() {
            script.remove();
        });
    });

}(window.jQuery, window.Routing, JSON);

var Integrated = Integrated || {};
