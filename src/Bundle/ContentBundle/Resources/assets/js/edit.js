import './main';
import './handlebars.helpers';
import './relation';
import './unlock_article';

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

    $('[data-prototype]').each(function(index, elm) {
        $(elm).data('index', $(elm).find('ul li').length);

        $(elm).find('ul li').each(function() {
            addTagFormDeleteLink($(this));
        });

        $(elm).find('[data-addfield="collection"]').click(function(ev) {
            addFormToCollection($(elm));
        });
    });

    function addFormToCollection($collection) {
        let index = $collection.data('index');

        let newForm = $collection.data('prototype');
        newForm = newForm.replace(/__name__/g, index);
        newForm = newForm.replace(/__id__/g, $collection.attr('id') + '_' + index);

        $collection.data('index', index + 1);

        let $newFormLi = $('<li></li>').append(newForm);

        addTagFormDeleteLink($newFormLi);

        $collection.find('ul').append($newFormLi);
    }

    function addTagFormDeleteLink($tagFormLi) {
        let $removeFormButton = $($tagFormLi).find('[data-removefield]');
        $removeFormButton.on('click', function(e) {
            $tagFormLi.remove();
        });
    }
});
