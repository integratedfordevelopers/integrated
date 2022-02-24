$(function() {
    initContentChoice();
});

function initContentChoice() {
    $('.integrated_content_choice').select2({
        ajax: {
            processResults: function (data) {
                return {
                    //todo show result as html with image
                    results: $.map(data.items, function(obj) {
                        if (!obj.id || ! obj.title) {
                            console.log('api should return at least id and title');
                        }
                        return { id: obj.id, text: obj.title };
                    })
                };
            }
        }
    });
}