//Substitute SVG with PNG for non-svg browsers
if (!Modernizr.svg) {
    $('.svg-img').each(function () {
        $(this).attr('src', ($(this).attr('data-png-src')));
    });
}

$(document).ready(function () {
    //Placeholders Fix
    $('input, textarea').placeholder();

    $('.search-form .form-control').focus(function () {
        if ($(window).width() >= 768) {
            $(this).closest('.nav-form-inner').addClass('full-width');
        }
    });
    $(".search-form .form-control").blur(function () {
        $(this).closest('.nav-form-inner').removeClass('full-width');
    });

    //Expanded hidden watch list items
    $(".list-watch .watch-item-trigger").click(function (event) {
        event.preventDefault();
        if ($(this).hasClass('active')) {
            $(this).closest('.list-watch').find('.hidden-item').css("display", "none");
            $(this).removeClass('active');
        } else {
            $(this).closest('.list-watch').find('.hidden-item').css("display", "inline-block");
            $(this).addClass('active');
        }
    });

    //Expanded hidden text holder items
    $(".hidden-text-holder .link-more").click(function (event) {
        event.preventDefault();
        if ($(this).hasClass('active')) {
            $(this).closest('.hidden-text-holder').find('.hidden-item').css("display", "none");
            $(this).removeClass('active');
        } else {
            $(this).closest('.hidden-text-holder').find('.hidden-item').css("display", "block");
            $(this).addClass('active');
        }
    });

    //Expanded list action
    $(".list-expanded > li > a").click(function (event) {
        event.preventDefault();
        $(this).next('.list-sub-expanded').slideToggle(250);
        $(this).toggleClass('active');
    });

    if (typeof tinymce !== 'undefined') {
        //Tinymce initial
        tinymce.init({
            selector: "textarea#tinymce-holder"
        });
    }

    //Select 2 initial
    $(".basic-multiple").select2();

    $('select.select2').each(function() {
        $(this).select2({
            placeholder: $(this).data('placeholder')
        });
    });

    $('.btn_show_more').on('click', function(e){
        e.preventDefault();
        $(this).closest('.filters_list').find('.to_show').slideToggle(200);
        $(this).hide();
    });

    $('button[type="submit"]').click(function() {
        if (!$(this).attr('formnovalidate') && $(this).get(0).form && $(this).get(0).form.checkValidity()) {
            $('button[type="submit"]').addClass('button-submitted').off("click").click(function(e) {
                e.preventDefault();
                e.stopImmediatePropagation();
            });
            $(this).prepend('<span class="glyphicon glyphicon-refresh" aria-hidden="true"></span>');
        }
    });
});

$(document).ready(function () {
    var suggestions = new Bloodhound({
        datumTokenizer: Bloodhound.tokenizers.whitespace,
        queryTokenizer: Bloodhound.tokenizers.whitespace,
        remote: {
            url: '/admin/suggestions/%QUERY',
            wildcard: '%QUERY',
            transform: transform
        }
    });

    var elm = $('.search-form .form-control');

    elm.typeahead({highlight: true}, {
        name: 'suggestions',
        source: suggestions,
        limit: Infinity,
        display: display,
        templates: {
            suggestion: Handlebars.compile(
                '{{#if type.suggestion }}' +
                    '<div class="tt-suggestion-term"><div class="tt-suggestion-head">{{data}}</div></div>' +
                '{{/if}}' +
                '{{#if type.result }}' +
                    '<div class="tt-suggestion-result">' +
                        '<div><a href="{{data.url}}">{{data.title}}</a></div>' +
                        '<ul>' +
                            '<li>{{data.type}}</li>' +
                            '<li>{{data.published}}</li>' +
                        '</ul>' +
                    '</div>' +
                '{{/if}}'
            )
        }
    });

    // this event listener will add a divider between terms and results.
    elm.bind('typeahead:render', function (e, suggestions, async, dataset) {
        var first = elm.parent('.twitter-typeahead').find('.tt-dataset-' + dataset + ' .tt-suggestion-term').first();

        if (!first.is(':first-child')) {
            first.before('<div role="separator" class="tt-divider"></div>');
        }
    });

    // redirect to the edit page when a result is selected.
    elm.bind('typeahead:select', function(e, suggestion) {
        if (suggestion.type.result) {
            window.location.href = suggestion.data.url;
        } else {
            elm.parents('form').submit();
        }
    });

    function transform(response) {
        var results = [];

        if ($.isArray(response.results)) {
            $.each(response.results, function () {
                var data = this;

                if (data.published) {
                    data.published = moment(data.published).format('lll');
                }

                results.push({
                    type: { suggestion: false, result: true },
                    data: data
                });
            });
        }

        if ($.isArray(response.suggestions)) {
            $.each(response.suggestions, function (index, value) {
                results.push({
                    type: { suggestion: true, result: false },
                    data: value
                });
            });
        }

        return results;
    }

    function display(item) {
        if (item.type.suggestion) {
            return item.data;
        }

        if (item.type.result) {
            return item.data.title;
        }

        return '';
    }
});
