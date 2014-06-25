var Relation = function(id, url) {

    this.id = id;
    this.url = url;
    this.loadedSelected = false;

    var parent = this;

    this.handleOptions = function(data) {
        var optionsTemplateSource = $('#options-template').html(), optionsTemplate = Handlebars.compile(optionsTemplateSource);
        var paginationTemplateSource = $('#pagination-template').html(), paginationTemplate = Handlebars.compile(paginationTemplateSource);

        data.title = parent.getTitle();
        data.selected = parent.getSelected();

        var container = parent.getOptionsContainer().html(optionsTemplate(data)).append(paginationTemplate(data));
        container.find('.pagination a').click(function(ev){
            ev.preventDefault();
            parent.loadOptions($(this).attr('href'));
        });

        container.find('input').click(function() {
            if ($(this).is(':checked')) {
                parent.addOption($(this).val());
            } else {
                parent.removeOption($(this).val());
            }
        });

        container.find('a[data-value]').click(function(ev) {
            ev.preventDefault();
            parent.addOption($(this).data('value'));
        });

        if (parent.loadedSelected === false) {
            parent.loadSelected();
            parent.loadedSelected = true;
        }
    }

    this.handleSelected = function(data) {
        var optionsTemplateSource = $('#selected-template').html(), optionsTemplate = Handlebars.compile(optionsTemplateSource);
        var paginationTemplateSource = $('#pagination-template').html(), paginationTemplate = Handlebars.compile(paginationTemplateSource);

        data.title = parent.getTitle();
        data.selected = parent.getSelected();

        var container = parent.getSelectedContainer().html(optionsTemplate(data)).append(paginationTemplate(data));
        container.find('.pagination a').click(function(ev){
            ev.preventDefault();
            parent.loadSelected($(this).attr('href'));
        });
        container.find('*[data-remove]').click(function(ev){
            ev.preventDefault();
            parent.removeOption($(this).data('remove'));
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
        return this.url + '?relation=' + this.id + '&limit=5&q=' + $('#relations-q').val() + '&_format=json';
    }

    this.getSelectedUrl = function() {
        return this.url + '?limit=5&_format=json';
    }

    this.getSelected = function() {
        var selected = this.getInputElement().val().split(',');
        selected = $.grep(selected,function(n){
            return(n);
        });

        return selected;
    }

    this.getOptionsContainer = function() {
        if ($('#relations-result div[data-relation="' + this.id + '"').length > 0) {
            return $('#relations-result div[data-relation="' + this.id + '"');
        } else {
            var container = $('<div data-relation="' + this.id +'"></div>');
            $('#relations-result').append(container);
            return container;
        }
    }

    this.getSelectedContainer = function() {
        if ($('#relations-selected li[data-relation="' + this.id + '"').length > 0) {
            return $('#relations-selected li[data-relation="' + this.id + '"');
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