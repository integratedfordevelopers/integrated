function implementSelect2() {
    /* add select 2 for each relations input */
    $(".relation-items").each(function () {
        $(this).select2({
            multiple: $(this).data('multiple'),
            ajax: {
                type: 'GET',
                url: Routing.generate("integrated_content_content_index"),
                dataType: 'json',
                data: function (param) {
                    return {
                        relation: $(this).data('id'),
                        limit: 5,
                        sort: 'title',
                        _format: 'json',
                        q: typeof param.term != 'undefined' ? param.term + '*' : ''
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
    });
}