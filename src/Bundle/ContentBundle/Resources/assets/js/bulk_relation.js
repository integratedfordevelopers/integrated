function implementSelect2() {
    /* add select 2 for each relations input */
    $(".relation-items").each(function () {
        $(this).select2({
            multiple: $(this).data('multiple'),
            ajax: {
                type: 'GET',
                url: $(this).data('url'),
                dataType: 'json',
                data: function (param) {
                    return {
                        relation: $(this).data('id'),
                        limit:  100,
                        sort: 'title',
                        q: typeof param.term !== 'undefined' ? param.term + '*' : ''
                    };
                },
                processResults: function (data) {
                    return {
                        results: $.map(data.items, function (item) {
                            return {
                                text: item.title,
                                id: item.id
                            }
                        })
                    };
                }
            }
        });

        var value;
        if ($(this).data('value')) {
            value = $(this).data('value');
            for (var i in value) {
                $(this).append('<option selected="selected" value="' + i + '">' + value[i] + '</option>');
            }

            $(this).trigger('change');
        }
    });
}
