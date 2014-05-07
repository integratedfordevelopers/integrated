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
    $('.btn_more').on('click', function(e){
        e.preventDefault();
        $(this).toggleClass('up').prev().find('.to_show').slideToggle();
    });
}




$(document).ready(function(){

    typeheadSearch();

    showMore();

});




$(window).load(function(){


});




$(window).resize(function(){

});