var Relation = function(id, url, type) {

    this.id = id;
    this.url = url;
    this.loadedSelected = false;
    this.modal = false;
    this.type = type;

    var relation = this;

    this.getModal = function() {
        if (relation.modal === false) {
            relation.modal = $('<div class="modal bs-example-modal-lg" tabindex="-1" role="dialog" aria-labelledby="myLargeModalLabel" aria-hidden="true" />');
            relation.modal.append(
                '<div class="modal-dialog modal-lg">' +
                '<div class="modal-content">' +
                '<div class="modal-header">' +
                '<button type="button" class="close" data-dismiss="modal"><span aria-hidden="true">×</span><span class="sr-only">Close</span></button>' +
                '<h4 class="modal-title">Add</h4>' +
                '</div>' +
                '<iframe data-id="' +  this.id + '" width="100%" height="10" style="width: 100%; min-height: 10px; border: none;">Loading</iframe>' +
                '</div>' +
                '</div>'
            );

            relation.modal.on('hide.bs.modal', function(ev) {
                relation.getIFrame().hide();
            });
        }

        return relation.modal;
    }

    this.getIFrame = function() {
        var modal = this.getModal();
        return modal.find('iframe');
    }

    this.resizeIFrame = function() {
        var height = this.getIFrame().contents().height();
        var margin = 120;

        if (height > ($(window).height() - margin)) {
            height = $(window).height() - margin;
        }

        this.getIFrame().attr('height', height);
    }

    this.handleOptions = function(data) {
        var optionsTemplateSource = $('#options-template').html(), optionsTemplate = Handlebars.compile(optionsTemplateSource);
        var paginationTemplateSource = $('#pagination-template').html(), paginationTemplate = Handlebars.compile(paginationTemplateSource);
        var addTemplateSource = $('#add-template').html(), addTemplate = Handlebars.compile(addTemplateSource);

        data.title = relation.getTitle();
        data.selected = relation.getSelected();

        var container = relation.getOptionsContainer().html(optionsTemplate(data)).find('.options-cnt').append(paginationTemplate(data));
        container.find('.pagination a').click(function(ev){
            ev.preventDefault();
            relation.loadOptions($(this).attr('href'));
        });

        container.append(addTemplate(data));

        container.append(relation.getModal());
        container.find('a[data-modal]').click(function(ev){

            ev.preventDefault();
            relation.modal.find('.modal-title').text($(this).data('title'));

            var iFrame =  relation.getIFrame();
            iFrame.css('display', 'block').attr('src', $(this).attr('href') + '&_format=iframe.html').load(function(ev){

                iFrame.show();
                relation.modal.modal('show');
                relation.resizeIFrame();

                iFrame.contents().find('*[data-dismiss="modal"]').click(function(ev){
                    ev.preventDefault();
                    relation.modal.modal('hide');
                });

                iFrame.unbind('load');

            });
        });

        container.find('input').click(function() {
            if ($(this).is(':checked')) {
                relation.addOption($(this).val());
            } else {
                relation.removeOption($(this).val());
            }
        });

        container.find('a[data-value]').click(function(ev) {
            ev.preventDefault();
            relation.addOption($(this).data('value'));
        });

        if (relation.loadedSelected === false) {
            relation.loadSelected();
            relation.loadedSelected = true;
        }
    }

    this.handleSelected = function(data) {
        var optionsTemplateSource = $('#selected-template').html(), optionsTemplate = Handlebars.compile(optionsTemplateSource);
        var paginationTemplateSource = $('#pagination-template').html(), paginationTemplate = Handlebars.compile(paginationTemplateSource);

        data.title = relation.getTitle();
        data.selected = relation.getSelected();

        var container = relation.getSelectedContainer().html(optionsTemplate(data)).append(paginationTemplate(data));
        container.find('.pagination a').click(function(ev){
            ev.preventDefault();
            relation.loadSelected($(this).attr('href'));
        });
        container.find('*[data-remove]').click(function(ev){
            ev.preventDefault();
            relation.removeOption($(this).data('remove'));
        })
    }

    this.loadOptions = function(url) {

        this.getOptionsContainer().find('.pagination a').unbind('click').click(function(ev){
            ev.preventDefault();
        })

        if (url == undefined) {
            url = this.getOptionsUrl();
        }
        $.ajax({
            url: url,
            success: this.handleOptions
        }).done(function() {
            relation.triggerResize();
        });
    }

    this.refreshOptions = function() {
        var selected = this.getSelected();
        $('div[data-relation="' + this.id + '"] input:checked').each(function(){
            if ($.inArray($(this).val(), selected) < 0) {
                $(this).attr('checked', false);
            }
        });

        this.triggerResize();
    }

    this.loadSelected = function(url) {

        this.getSelectedContainer().find('.pagination a').unbind('click').click(function(ev){
            ev.preventDefault();
        })

        if (url == undefined) {
            url = this.getSelectedUrl();
        }
        $.ajax({
            url: url,
            type: 'POST',
            data: {
                id: this.getSelected()
            },
            success: this.handleSelected
        }).done(function() {
            relation.triggerResize();
        });
    }

    this.getTitle = function() {
        return this.getInputElement().data('title');
    }

    this.getOptionsUrl = function() {
        return this.url + '?relation=' + this.id + '&limit=5&q=' + $('#relations-q').val() + '&sort=title&_format=json';
    }

    this.getSelectedUrl = function() {
        return this.url + '?limit=5&sort=title&_format=json';
    }

    this.getSelected = function() {
        if (this.getInputElement().val() != undefined) {
            var selected = this.getInputElement().val().split(',');
            selected = $.grep(selected, function (n) {
                return (n);
            });

            return selected;
        }
    }

    this.getOptionsContainer = function() {
        if ($('#relations-result div[data-relation="' + this.id + '"]').length > 0) {
            return $('#relations-result div[data-relation="' + this.id + '"]');
        } else {
            var container = $('<div class="item_row" data-relation="' + this.id +'"></div>');
            $('#relations-result').append(container);
            return container;
        }
    }

    this.getSelectedContainer = function() {
        if ($('#relations-selected li[data-relation="' + this.id + '"]').length > 0) {
            return $('#relations-selected li[data-relation="' + this.id + '"]');
        } else {
            var container = $('<li data-relation="' + this.id +'"></li>');
            $('#relations-selected').append(container);
            return container;
        }
    }

    this.addOption = function(id) {
        var selected = this.getSelected();

        if (this.getMultiple() === false) {
            selected = [];
        }

        selected.push(id);

        this.getInputElement().val(selected.join(','));
        this.refreshOptions();
        this.loadSelected();
    }

    this.removeOption = function(id) {
        var selected = this.getSelected();
        if ($.inArray(id, selected) >= 0) {
            selected.splice($.inArray(id, selected), 1);
        }

        this.getInputElement().val(selected.join(','));
        this.refreshOptions();
        this.loadSelected();
    }

    this.getInputElement = function () {
        return $('input[data-relation="' + this.id + '"]');
    }

    this.getMultiple = function() {
        return (this.getInputElement().data('multiple') == 1);
    }

    this.triggerResize = function() {
        if (window.location != window.parent.location) {
            window.parent.postMessage({ resizeModal: this.type }, '*');
        }
    }
}
