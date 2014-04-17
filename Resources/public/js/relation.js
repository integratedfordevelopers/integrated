var Relation = function(id, url) {

    this.id = id;
    this.url = url;

    var parent = this;

    this.handleOptions = function(data) {
        var optionsTemplateSource = $('#options-template').html(), optionsTemplate = Handlebars.compile(optionsTemplateSource);
        var paginationTemplateSource = $('#pagination-template').html(), paginationTemplate = Handlebars.compile(paginationTemplateSource);
        data.selected = parent.getSelected();

        var items = parent.getOptionsContainer().find('.items').html(optionsTemplate(data)).append(paginationTemplate(data));
        items.find('.pagination a').click(function(ev){
            ev.preventDefault();
            parent.loadOptions($(this).attr('href'));
        });
        items.find('input').click(function(){
            if ($(this).is(':checked')) {
                parent.addOption($(this).val());
            } else {
                parent.removeOption($(this).val());
            }
        })

        parent.loadSelected();
    }

    this.handleSelected = function(data) {
        var optionsTemplateSource = $('#selected-template').html(), optionsTemplate = Handlebars.compile(optionsTemplateSource);
        var paginationTemplateSource = $('#pagination-template').html(), paginationTemplate = Handlebars.compile(paginationTemplateSource);
        data.selected = parent.getSelected();

        var items = parent.getSelectedContainer().find('.items').html(optionsTemplate(data)).append(paginationTemplate(data));
        items.find('.pagination a').click(function(ev){
            ev.preventDefault();
            parent.loadSelected($(this).attr('href'));
        });
        items.find('a[data-remove]').click(function(ev){
            ev.preventDefault();
            parent.removeOption($(this).data('remove'));
        })
    }

    this.loadOptions = function(url) {
        if (url == undefined) {
            url = this.getOptionsUrl();
        }
        $.ajax({
            url: url,
            success: this.handleOptions
        });
    }

    this.loadSelected = function(url) {
        if (url == undefined) {
            url = this.getSelectedUrl();
        }
        $.ajax({
            url: url,
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
        return this.url + '?id[]=' + this.getSelected().join('&id[]=') + '&limit=5&_format=json';
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
            var container = $('<div data-relation="' + this.id +'"><h4>' + this.getTitle() + '</h4><div class="items"></div></div>');
            $('#relations-result').append(container);
            return container;
        }
    }

    this.getSelectedContainer = function() {
        if ($('#relations-selected div[data-relation="' + this.id + '"').length > 0) {
            return $('#relations-selected div[data-relation="' + this.id + '"');
        } else {
            var container = $('<div data-relation="' + this.id +'"><h4>' + this.getTitle() + '</h4><div class="items"></div></div>');
            $('#relations-selected').append(container);
            return container;
        }
    }

    this.addOption = function(id) {
        var selected = this.getSelected();
        selected.push(id);
        this.getInputElement().val(selected.join(','));
        this.loadSelected();
    }

    this.removeOption = function(id) {
        var selected = this.getSelected();
        if ($.inArray(id, selected) >= 0) {
            selected.splice($.inArray(id, selected), 1);
        }

        this.getInputElement().val(selected.join(','));
        this.loadSelected();
    }

    this.getInputElement = function () {
        return $('input[data-relation="' + this.id + '"]');
    }
}