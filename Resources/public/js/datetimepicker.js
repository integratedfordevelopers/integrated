$(function() {
    $('.form_datetime').each(function(index, elm){
        var $input = $(elm).children('input').eq(0),
            $clearButton = $(elm).find('.glyphicon-remove');

        $.datetimepicker.setLocale($(elm).data('locale'));

        $input.datetimepicker({
            format:  $(elm).data('dateFormat')
        });

        $clearButton.click(function(){
            $input.datetimepicker('reset'); //support hide,show and destroy command
        });

    });
});