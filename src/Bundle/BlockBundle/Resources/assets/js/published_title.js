$(function () {
    var block = $("#integrated_block_block");
    var use_title = $('.use-title', block);
    var title = $(".main-title", block);
    var published_title = $('.published-title', block);
    var published_form_row = published_title.closest(".form-group");
    var use_title_form_row = use_title.closest('.form-group');

    var compare_titles = function (at_start) {
        if (title.val() === published_title.val()) {
            use_title.prop('checked', true);
            use_title_form_row.show();
            published_form_row.hide();
        }
        else if (at_start) {
            use_title_form_row.hide();
        }
    };

    compare_titles(true);

    use_title.on('change', function () {
        /* user can do uncheck only */
        published_form_row.show();
        use_title_form_row.hide();
    });
});