/* Disable BACK|FORWARD buttons in browser */
history.pushState(null, null, location.href);
window.addEventListener('popstate', function(event) {
    history.pushState(null, null, location.href);
});

var form = $('form.content-form');
var modal = $("#content-edit-modal");

/* observe form for changes */
form.on('change', function () {
    form.data('changed', true);
});

/* ask user before leave page via href links and unlock article */
$('a:not(form.content-form a)').on('click', function (e) {
    var url = $(this).attr('href');

    if (url && url != '#') {
        e.preventDefault();

        if (form.data('changed')) {
            $('.live-page', modal).data('return_url', url);
            modal.modal();
        }
        else {
            window.onbeforeunload = null;
            $('.return-url', form).val(url);
            $('[name*=cancel]', form).trigger('click');
        }
    }
});

/* handle "leave page button" in modal */
$('.live-page', modal).on('click', function() {
    var url = $(this).data('return_url');
    $('.return-url', form).val(url);

    window.onbeforeunload = null;
    $('[name*=cancel]', form).trigger('click');
    modal.modal('hide');
});

/**/
window.onbeforeunload = function () {
    return 'You have unsaved changes. When you leave this page your changes will be lost.'
};