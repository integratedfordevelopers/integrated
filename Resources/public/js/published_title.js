$(function () {
    var block = $("#integrated_block_block");
    var use_title = $('.use-title', block);
    var title = $(".main-title", block);
    var published_title = $('.published-title', block);
    var published_form_row = published_title.closest(".form-group");
    var use_title_form_row = use_title.closest('.form-group');

    var compare_titles = function () {
        title.val() === published_title.val() ? use_title_form_row.show() : use_title_form_row.hide();
    };

    title.on('keyup', compare_titles);
    published_title.on('keyup', compare_titles);

    compare_titles();

    use_title.on('change', function () {
        if ($(this).prop('checked')) {
            published_form_row.hide();
        }
        else {
            published_form_row.show();
            compare_titles();
        }
    });
});