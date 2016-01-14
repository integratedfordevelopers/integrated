$(function() {
    $('.integrated_ajax_select2').select2({
        ajax: {
            processResults: function (data) {
                return {
                    //todo show result as html with image
                    results: $.map(data.items, function(obj) {
                        return { id: obj.id, text: obj.title };
                    })
                };
            }
        }
    });
});