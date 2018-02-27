$(function() {
    var $control = $('.login-visible-control');

    $control.change(function() {
        var $wrap = $('.integrated-user-form');
        var $parent = $(this).parents('.form-group');

        var items = $wrap.find('.form-group').not($parent);
        if ($(this).is(':checked')) {
            items.show();
        } else {
            items.hide();
        }
    }).trigger('change');
});