import 'bootstrap-sass';

const form = $('form.content-form');
const modal = $("#content-edit-modal");
const cancelButton = $('[name*=cancel]', form);

/* handle BACK|FORWARD buttons in browser */
history.pushState(null, null, location.href);
window.onpopstate = function(e){
    $(':focus').blur();
    if ($('form.content-form').data('changed')) {
        $('.live-page', modal).data('return_url', document.referrer);
        modal.modal();
    } else {
        leavePage(document.referrer)
    }
    //stay on the page
    history.pushState(null, null, location.href);
};

/* observe form for changes */
form.on('change', function () {
    form.data('changed', true);
});

$(function () {
    if (typeof tinymce !== 'undefined') {
        tinymce.on('AddEditor', function (e) {
            e.editor.on('change', function (e) {
                form.data('changed', true);
            });
        });
    }
});
//var is set in view
if (global.formInvalid) {
    form.data('changed', true);
}

/* ask user before leave page via href links and unlock article */
$('a:not(form.content-form a), form.content-form button[name*=cancel]').on('click', function (e) {
    const url = $(this).attr('href');

    if (!cancelButton.length) {
        return;
    }

    if (url && url !== '#') {
        if ($(this).attr('target') === '_blank') {
            return;
        }

        e.preventDefault();

        if (form.data('changed')) {
            $('.live-page', modal).data('return_url', url);
            modal.modal();
        }
        else {
            leavePage(url);
        }
    } else if ( $(this).is('button[name*=cancel]', form) && form.data('changed')) {
        e.preventDefault();

        modal.modal();
    }
});

/* handle "leave page button" in modal */
$('.live-page', modal).on('click', function() {
    modal.modal('hide');
    leavePage($(this).data('return_url'));
});

function leavePage(returnUrl) {
    $('.return-url', form).val(returnUrl);

    window.onbeforeunload = null;
    form.data('changed', false);
    cancelButton.trigger('click');
}

$('button', form).on('click', function () {
    window.onbeforeunload = null;
});

window.onbeforeunload = function () {
    if (form.data('changed')) {
        return 'You have unsaved changes. When you leave this page your changes will be lost.';
    }
};
