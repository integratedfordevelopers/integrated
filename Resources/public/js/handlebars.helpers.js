Handlebars.registerHelper('equals', function(v1, v2, options) {
    if(v1 == v2) {
        return options.fn(this);
    }
    return options.inverse(this);
});

Handlebars.registerHelper('checked', function(id, selected, options) {

    if (selected == undefined) {
        return options.inverse(this);
    }

    if (selected.indexOf(id) >= 0) {
        return options.fn(this);
    } else {
        return options.inverse(this);
    }
});

Handlebars.registerHelper('hasPages', function(pageCount, options) {
    if (pageCount > 1) {
        return options.fn(this);
    } else {
        return options.inverse(this);
    }
});

Handlebars.registerHelper('hasItems', function(numFound, options) {
    if (numFound > 0) {
        return options.fn(this);
    } else {
        return options.inverse(this);
    }
});