var Relation = function(id, url) {

    this.id = id;
    this.url = url;
    this.loadedSelected = false;
    this.modal = false;

    var element = this;

    this.getModal = function() {
        if (element.modal === false) {
            element.modal = $('<div class="modal fade bs-example-modal-lg" tabindex="-1" role="dialog" aria-labelledby="myLargeModalLabel" aria-hidden="true" />');
            element.modal.append(
                '<div class="modal-dialog modal-lg">' +
                '<div class="modal-content">' +
                '<div class="modal-header">' +
                '<button type="button" class="close" data-dismiss="modal"><span aria-hidden="true">×</span><span class="sr-only">Close</span></button>' +
                '<h4 class="modal-title">Add</h4>' +
                '</div>' +
                '<iframe frameborder="none" width="100%" height="400" src="">Loading</iframe>' +
                '</div>' +
                '</div>'
            );

            element.modal.on('hide.bs.modal', function(ev) {
                element.modal.find('iframe').hide();
                console.log('close it');
            });

            element.modal.on('show.bs.modal', function(ev) {
                console.log(ev);
                console.log(element);
                console.log('open it');
            });
        }

        return element.modal;
    }

    this.handleOptions = function(data) {
        var optionsTemplateSource = $('#options-template').html(), optionsTemplate = Handlebars.compile(optionsTemplateSource);
        var paginationTemplateSource = $('#pagination-template').html(), paginationTemplate = Handlebars.compile(paginationTemplateSource);
        var addTemplateSource = $('#add-template').html(), addTemplate = Handlebars.compile(addTemplateSource);

        data.title = element.getTitle();
        data.selected = element.getSelected();

        var container = element.getOptionsContainer().html(optionsTemplate(data)).find('.options-cnt').append(paginationTemplate(data));
        container.find('.pagination a').click(function(ev){
            ev.preventDefault();
            element.loadOptions($(this).attr('href'));
        });

        container.append(addTemplate(data));

        container.append(element.getModal());
        container.find('a[data-modal]').click(function(ev){

            ev.preventDefault();
            element.modal.find('.modal-title').text($(this).data('title'));

            var iFrame =  element.modal.find('iframe');
            iFrame.hide().attr('src', $(this).attr('href') + '&_format=iframe.html').load(function(ev){

                iFrame.unbind('load');
                console.log('LOAD IFRAME');
                console.log(ev);
                element.modal.modal('show');
                $(this).show();

                var height = $(window).height() - 120;
                if (($(this).contents().height() + 20) < $(window).height()) {
                    height = $(this).contents().height() -100;
                }

                iFrame.attr('height', height);

                iFrame.contents().find('*[data-dismiss="modal"]').click(function(ev){
                    ev.preventDefault();
                    element.modal.modal('hide');
                });
            });
        });

        container.find('input').click(function() {
            if ($(this).is(':checked')) {
                element.addOption($(this).val());
            } else {
                element.removeOption($(this).val());
            }
        });

        container.find('a[data-value]').click(function(ev) {
            ev.preventDefault();
            element.addOption($(this).data('value'));
        });

        if (element.loadedSelected === false) {
            element.loadSelected();
            element.loadedSelected = true;
        }
    }

    this.handleSelected = function(data) {
        var optionsTemplateSource = $('#selected-template').html(), optionsTemplate = Handlebars.compile(optionsTemplateSource);
        var paginationTemplateSource = $('#pagination-template').html(), paginationTemplate = Handlebars.compile(paginationTemplateSource);

        data.title = element.getTitle();
        data.selected = element.getSelected();

        var container = element.getSelectedContainer().html(optionsTemplate(data)).append(paginationTemplate(data));
        container.find('.pagination a').click(function(ev){
            ev.preventDefault();
            element.loadSelected($(this).attr('href'));
        });
        container.find('*[data-remove]').click(function(ev){
            ev.preventDefault();
            element.removeOption($(this).data('remove'));
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
        });
    }

    this.refreshOptions = function() {
        var selected = this.getSelected();
        $('div[data-relation="' + this.id + '"] input:checked').each(function(){
            if ($.inArray($(this).val(), selected) < 0) {
                $(this).attr('checked', false);
            }
        })
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
}