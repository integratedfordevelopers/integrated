import './main';
import './handlebars.helpers';
import './relation';

import 'jquery-datetimepicker';

$(function() {
    $('.form_datetime').each(function(index, elm) {
        let $input = $(elm).children('input').eq(0), $clearButton = $(elm).find('.glyphicon-remove');

        $input.datetimepicker({
            format: $(elm).data('dateFormat'),
            locale: $(elm).data('locale')
        });

        $clearButton.click(function () {
            $input.datetimepicker('reset'); //support hide,show and destroy command
        });
    });
});
