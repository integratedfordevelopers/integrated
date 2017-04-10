/* add select 2 for each relations input */
$(".relation-items").each(function () {
    var multiple = $(this).data('multiple');
    var relation_id = $(this).data('id');
    var $relation = $(this);

    $relation.select2({
        multiple: multiple,
        ajax: {
            type: 'GET',
            url: Routing.generate("integrated_content_content_index"),
            dataType: 'json',
            data: function (param) {
                return {
                    relation: relation_id,
                    limit: 1000,
                    sort: 'title',
                    _format: 'json',
                    q: typeof param.term != 'undefined' ? param.term + '*' : ''
                };
            },
            processResults: function (data) {
                var items = [];

                if ('items' in data) {
                    for (var k in data.items) {
                        var item = data.items[k];
                        if (!item.text) {
                            item.text = item.title;
                        }
                        items.push(item);
                    }
                }

                return {results: items};
            }
        }
    });
});

