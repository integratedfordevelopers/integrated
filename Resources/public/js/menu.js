!function($) {

    $('.integrated-website-menu').each(function(i, element) {
        var menu = $(element);
        var script = menu.find('script[type="text/json"]');

        $.ajax({
            type: 'POST',
            url: Routing.generate('integrated_website_menu_render'),
            data: script.html(),
            success: function(result) {
                menu.data('data', result.data);
                menu.data('options', result.options);
                menu.html(result.html);

                script.remove();
            },
            error: function(result) {
                // @todo error handling (INTEGRATED-420)
                alert('An error has occurred!');
                console.log(result.responseText);
            }
        });
    });

    $(document).on('click', '[data-action="integrated-website-menu-item-add"]', function(e) {
        e.preventDefault();

        var item = $(this);
        var menu = item.closest('.integrated-website-menu');

        bootbox.dialog({
                title: 'Add menu item',
                message: '<div class="row">' +
                    '<div class="col-md-12">' +
                        '<form class="form-horizontal">' +
                            '<div class="form-group">' +
                                '<label class="col-md-4 control-label" for="name">Name</label>' +
                                '<div class="col-md-8"> ' +
                                    '<input id="name" name="name" type="text" class="form-control">' +
                                '</div>' +
                            '</div>' +
                            '<div class="form-group">' +
                                '<label class="col-md-4 control-label" for="name">URI</label>' +
                                '<div class="col-md-8"> ' +
                                    '<input id="uri" name="uri" type="text" class="form-control">' +
                                '</div>' +
                            '</div>' +
                        '</form>' +
                    '</div>' +
                '</div>',
                buttons: {
                    success: {
                        label: 'Save',
                        callback: function() {
                            var name = $('#name').val();
                            var uri = $('#uri').val();
                            var data = menu.data('data');
                            var options = menu.data('options');
                            var id = item.attr('data-id');

                            replaceItem(data, id, name, uri);

                            // remove add item links
                            removeItem(data, getAddItem(menu, id));

                            $.ajax({
                                type: 'POST',
                                url: Routing.generate('integrated_website_menu_render'),
                                data: JSON.stringify({
                                    'data': data,
                                    'options': options
                                }),
                                success: function(result) {
                                    menu.data('data', result.data);
                                    menu.data('options', result.options);
                                    menu.html(result.html);
                                },
                                error: function(result) {
                                    // @todo error handling (INTEGRATED-420)
                                    alert('An error has occurred!');
                                    console.log(result.responseText);
                                }
                            });
                        }
                    }
                }
            }
        );
    });

    function replaceItem(data, id, name, uri)
    {
        if (data.id == id) {
            data.name = name;
            data.uri = uri;

        } else if (data.children != undefined) {
            $.each(data.children, function(i, child) {
                replaceItem(child, id, name, uri);
            });
        }
    }

    function getAddItem(menu, id)
    {
        var ids = [];

        $.each(menu.find('[data-action="integrated-website-menu-item-add"]'), function(i, link) {
            var id2 = $(link).attr('data-id');

            if (id2 != id) {
                ids.push(id2);
            }
        });

        return ids;
    }

    function removeItem(data, ids)
    {
        var index = $.inArray(data.id, ids);

        if (index !== -1) {
            delete ids[index];
            delete data.id;
            delete data.name;
            delete data.uri;
            delete data.children;
        }

        if (data.children != undefined) {
            $.each(data.children, function(i, child) {
                removeItem(child, ids);
            });
        }
    }

    function getItem(data, id)
    {
        if (data.children != undefined) {
            for (var i in data.children) {
                var child = data.children[i];
                if (child.id == id || (child = getItem(child, id))) {
                    return child;
                }
            }
        }

        return false;
    }

    $(document).on('click', '[data-action="integrated-website-menu-item-edit"]', function(e) {
        e.preventDefault();

        var item = $(this);
        var menu = item.closest('.integrated-website-menu');
        var data = menu.data('data');
        var options = menu.data('options');
        var id = item.attr('data-id');
        var item2 = getItem(data, id);

        bootbox.dialog({
                title: 'Edit menu item',
                message: '<div class="row">' +
                    '<div class="col-md-12">' +
                        '<form class="form-horizontal">' +
                            '<div class="form-group">' +
                                '<label class="col-md-4 control-label" for="name">Name</label>' +
                                '<div class="col-md-8"> ' +
                                    '<input id="name" name="name" type="text" value="' + item2.name + '" class="form-control">' +
                                '</div>' +
                            '</div>' +
                            '<div class="form-group">' +
                                '<label class="col-md-4 control-label" for="name">URI</label>' +
                                '<div class="col-md-8"> ' +
                                    '<input id="uri" name="uri" type="text" value="' + item2.uri + '" class="form-control">' +
                                '</div>' +
                            '</div>' +
                        '</form>' +
                    '</div>' +
                '</div>',
                buttons: {
                    danger: {
                        label: 'Remove',
                        className: 'btn-danger pull-left',
                        callback: function() {
                            var ids = getAddItem(menu, id);

                            ids.push(id);

                            removeItem(data, ids);

                            $.ajax({
                                type: 'POST',
                                url: Routing.generate('integrated_website_menu_render'),
                                data: JSON.stringify({
                                    'data': data,
                                    'options': options
                                }),
                                success: function(result) {
                                    menu.data('data', result.data);
                                    menu.data('options', result.options);
                                    menu.html(result.html);
                                },
                                error: function(result) {
                                    // @todo error handling (INTEGRATED-420)
                                    alert('An error has occurred!');
                                    console.log(result.responseText);
                                }
                            });
                        }
                    },
                    success: {
                        label: 'Save',
                        callback: function() {
                            var name = $('#name').val();
                            var uri = $('#uri').val();

                            replaceItem(data, id, name, uri);

                            // remove add item links
                            removeItem(data, getAddItem(menu, id));

                            $.ajax({
                                type: 'POST',
                                url: Routing.generate('integrated_website_menu_render'),
                                data: JSON.stringify({
                                    'data': data,
                                    'options': options
                                }),
                                success: function(result) {
                                    menu.data('data', result.data);
                                    menu.data('options', result.options);
                                    menu.html(result.html);
                                },
                                error: function(result) {
                                    // @todo error handling (INTEGRATED-420)
                                    alert('An error has occurred!');
                                    console.log(result.responseText);
                                }
                            });
                        }
                    }
                }
            }
        );
    });

    $(document).on('click', '[data-action="integrated-website-page-save"]', function(e) {
        e.preventDefault();

        var menus = [];

        $('.integrated-website-menu').each(function(i, element) {
            var menu = $(element);
            var data = menu.data('data');

            // remove add item links
            removeItem(data, getAddItem(menu));

            menus.push(data);
        });

        $.ajax({
            type: 'POST',
            url: Routing.generate('integrated_website_page_edit', { 'id': $(this).attr('data-id') }),
            data: JSON.stringify({
                'menus': menus
            }),
            success: function(result) {
                //console.log(result);
            },
            error: function(result) {
                // @todo error handling (INTEGRATED-420)
                alert('An error has occurred!');
                console.log(result.responseText);
            }
        });

        $('#' + $(this).attr('data-element-id')).submit();
    });

}(window.jQuery);
