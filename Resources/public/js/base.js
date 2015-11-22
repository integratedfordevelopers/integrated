function typeheadSearch() {
    var query = new Bloodhound({
        datumTokenizer: function(d) { return Bloodhound.tokenizers.whitespace(d.title); },
        queryTokenizer: Bloodhound.tokenizers.whitespace,
        limit: 5,
        remote: 'data/example_collection.json?q=%QUERY'
    });

    query.initialize();

    $('.typehead_search').typeahead(null, {
        name: 'search',
        displayKey: 'name',
        source: query.ttAdapter(),
        templates: {
            suggestion:Handlebars.compile(
                '<h3 class="typehead_item_title"><a href="{{url}}">{{title}}</a></h3>' +
                    '<ul class="typehead_item_info_list">' +
                    '<li>News</li>' +
                    '<li>Status: {{status}}</li>' +
                    '<li>{{date}}</li>' +
                    '</ul>'
            )
        }
    });
}


function showMore() {
    $('.btn_show_more').on('click', function(e){
        e.preventDefault();
        $(this).closest('.item_row').find('.to_show').slideToggle(200);
        $(this).hide();
    });
}


function fluidSearchField() {
    $('header .typehead_search').focus(function(){
        if ($(window).width() >= 768) {
            var elemWidth = $('header .typehead_search').outerWidth();
            $(this).closest('.search_field_holder').stop().animate({width: "100%"}, 300, function(){
                $('header .typehead_search').blur(function(){
                    $(this).closest('.search_field_holder').stop().animate({width: elemWidth}, 300);
                });
            });
        }
    });
}


$(document).ready(function(){

    typeheadSearch();

    fluidSearchField();

    showMore();

    $('.popover_trigger').popover();

});




$(window).load(function(){


});




$(window).resize(function(){

});