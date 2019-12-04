!function($, Routing, JSON) {

    window.Handlebars.registerHelper('select', function( value, options ){
        var $el = $('<select />').html( options.fn(this) );
        $el.find('[value="' + value + '"]').attr({'selected':'selected'});
        return $el.html();
    });

    var Menu = function(element) {
        this.element = element;
    };

    Menu.prototype.load = function(data, success) {
        var element = this.element;
        var menu = this;

        $.ajax({
            type: 'POST',
            url: Routing.generate('integrated_website_menu_render'),
            data: data,
            success: function(result) {
                element.data('options', result.options);
                element.html(result.html);

                element.find('.integrated-website-menu-list, .integrated-website-menu-item').each(function() {
                    var item = $(this);

                    if (item.attr('data-json')) {
                        item.data('data', $.parseJSON(item.attr('data-json')));
                        item.removeAttr('data-json');
                    }
                });

                element.find('.integrated-website-menu-list').sortable({
                    tolerance: 'intersect',
                    items: '[data-action="integrated-website-menu-item-edit"]',
                    connectWith: '.integrated-website-menu-list',
                    placeholder: 'integrated-website-menu-placeholder',
                    cursor: 'move',
                    cursorAt: { top: 5, left: 10 },
                    scroll: false,
                    helper: function() {
                        return $('<div>').css('width', '20px').css('height', '10px');
                    }
                    //update: function(e, ui) {
                    //    menu.refresh();
                    //}
                });

                element.find('.dropdown').droppable({
                    accept: '.integrated-website-menu-item',
                    tolerance: 'fit',
                    greedy: true
                    //over: function(e, ui) {
                    //    $(this).addClass('open');
                    //    $(this).closest('.integrated-website-menu-placeholder').hide();
                    //},
                    //out: function(e, ui) {
                    //    $(this).removeClass('open');
                    //    $(this).closest('.integrated-website-menu-placeholder').show();
                    //}
                });

                if ($.isFunction(success)) {
                    success();
                }
            },
            error: function (result) {
                // @todo error handling (INTEGRATED-420)
                alert('An error has occurred!');
                console.log(result.responseText);
            }
        });
    };

    Menu.prototype.refresh = function(id) {
        this.load(JSON.stringify({
            data: this.getData(id),
            options: this.getOptions()
        }));
    };

    Menu.prototype.getData = function(id) {
        var menu = this.element.closestChildren('.integrated-website-menu-list');
        var data = menu.data('data');

        data.children = [];

        menu.closestChildren('.integrated-website-menu-item').each(function() {
            var item = new Item($(this));
            var result = item.getData(id);

            if (result) {
                data.children.push(result);
            }
        });

        return data;
    };

    Menu.prototype.getOptions = function() {
        return this.element.data('options');
    };

    var Item = function(element) {
        this.element = element;
        this.menu = new Menu(this.element.closest('.integrated-website-menu'));
    };

    Item.prototype.getMenu = function() {
        return this.menu;
    };

    Item.prototype.getData = function(id, item) {
        item = item || this.element;

        var data = item.data('data');

        if (id && data.id == id || 'integrated-website-menu-item-edit' == item.attr('data-action')) {
            var items = item.closestChildren('.integrated-website-menu-item');

            if (items.length) {
                var children = [];

                for (var i = 0; i < items.length; i++) {
                    var result = this.getData(id, $(items[i]));

                    if (result) {
                        children.push(result);
                    }
                }

                data.children = children;
            }

            return data;
        }
    };

    Item.prototype.getValue = function(key) {
        return this.element.data('data')[key];
    };

    Item.prototype.update = function(json) {
        var data = this.element.data('data');

        $.each(json, function(key, value) {
            data[key] = value;
        });

        this.element.data('data', data);
        this.menu.refresh(data.id);
    };

    Item.prototype.remove = function() {
        this.element.remove();
        this.menu.refresh();
    };

    $.extend(true, Integrated, {
        Menu: {
            create: function(element) {
                return new Menu(element);
            },
            updateLinkType: function() {
                if (jQuery('#typeLinkUri').prop("checked")) {
                    jQuery('#integrated-row-uri').show();
                    jQuery('#integrated-row-searchSelection').hide();
                    jQuery('#integrated-row-maxItems').hide();
                }

                if (jQuery('#typeLinkSearchSelection').prop("checked")) {
                    jQuery('#integrated-row-uri').hide();
                    jQuery('#integrated-row-searchSelection').show();
                    jQuery('#integrated-row-maxItems').show();
                }
            }
        }
    });

    $('.integrated-website-menu').each(function() {
        var element = $(this);

        var menu = new Menu(element);
        var script = element.find('script[type="text/json"]');

        menu.load(script.html(), function() {
            script.remove();
        });
    });

    $('.integrated-website-menu').on('mouseover', '.integrated-website-menu-item', function(e) {
        $(this).addClass('open');
    });

    $('.integrated-website-menu').on('mouseout', '.integrated-website-menu-item', function(e) {
        $(this).removeClass('open');
    });

    $('.integrated-website-menu').on('click', '[data-action="integrated-website-menu-item-add"]', function(e) {
        e.preventDefault();
        e.stopPropagation();

        var item = new Item($(this));
        var template = Handlebars.compile($('#integrated_website_template_modal_menu_edit').html());
        var html = $(template({
            typeLinkUri: true
        }));

        bootbox.dialog({
            title: 'Add menu item',
            message: html,
            buttons: {
                success: {
                    label: 'Save',
                    callback: function() {
                        item.update({
                            typeLink: $('input[name=typeLink]:checked').val(),
                            name:     $('#name').val(),
                            uri:      $('#uri').val(),
                            searchSelection: $('#searchSelection').val(),
                            maxItems: $('#maxItems').val()
                        });
                    }
                }
            }
        });

        Integrated.Menu.updateLinkType();
    });

    $('.integrated-website-menu').on('click', '[data-action="integrated-website-menu-item-edit"]', function(e) {
        e.preventDefault();
        e.stopPropagation();

        var item = new Item($(this));
        var template = Handlebars.compile($('#integrated_website_template_modal_menu_edit').html());

        var html = $(template({
            name:     item.getValue('name'),
            uri:      item.getValue('uri'),
            typeLinkUri: (typeof(item.getValue('typeLink')) == 'undefined' || item.getValue('typeLink') == 0) ? true : false,
            typeLinkSearchSelection: (item.getValue('typeLink') == 1) ? true : false,
            searchSelection: item.getValue('searchSelection'),
            maxItems: item.getValue('maxItems')
        }));

        bootbox.dialog({
            title: 'Edit menu item',
            message: html,
            buttons: {
                danger: {
                    label: 'Remove',
                    className: 'btn-danger pull-left',
                    callback: function() {
                        item.remove();
                    }
                },
                success: {
                    label: 'Save',
                    callback: function() {
                        item.update({
                            typeLink: $('input[name=typeLink]:checked').val(),
                            name: $('#name').val(),
                            uri:  $('#uri').val(),
                            searchSelection: $('#searchSelection').val(),
                            maxItems: $('#maxItems').val()
                        });
                    }
                }
            }
        });

        Integrated.Menu.updateLinkType();
    });

    $.fn.closestChildren = function(selector) {
        var children = this.children(selector);

        if (children.length) {
            return children;
        }

        children = this.children();

        if (children.length) {
            return children.closestChildren(selector);
        }

        return $();
    };

}(window.jQuery, window.Routing, JSON);

var Integrated = Integrated || {};
