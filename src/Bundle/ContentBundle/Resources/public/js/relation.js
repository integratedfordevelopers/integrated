/* add select 2 for each relations input */
$(".relation-items").each(function() {
    var multiple = $(this).data('multiple');
    var relation_id = $(this).attr('id');

    var $relation = $(this);
    var defaultValues = $.parseJSON($('#default_references').val());
    var $addWrapper = $relation.next('[data-add="1"]');

    if (defaultValues[relation_id] !== undefined && defaultValues[relation_id].length) {
        $.each(defaultValues[relation_id], function() {
            $relation.append('<option selected value="'+this.id+'" data-image="' + (this.image ? this.image : '') + '">'+this.title+'</option>');
        });
    }

    $relation.select2({
        multiple: multiple,
        allowClear: !multiple,
        placeholder: '',
        ajax: {
            type: 'GET',
            url: Routing.generate("integrated_content_content_index", {'_format': 'json'}),
            dataType: 'json',
            data: function(param) {
                return {
                    relation: relation_id,
                    limit: 100,
                    sort: 'title',
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
                            if (item.path) {
                                item.text = item.path + ' > ' + item.text;
                            }
                        }
                        items.push(item);
                    }
                }

                return { results: items };
            }
        },
        templateResult: function (state) {
            if (!state.id) {
                return state.text;
            }

            var image = state.image ? '<img src="' + state.image + '" class="select2-dropdown-image" />' : '';

            return $('<span>' + image + state.text + '</span>');
        },
        templateSelection: function (data) {
            if (!data.id) {
                return data.text;
            }

            var image = '';
            if (data.image) {
                image = data.image;
            } else if (data.element && $(data.element).data('image')) {
                image = $(data.element).data('image');
            }

            if (image) {
                image = '<img src="' + image + '"/>'
            }

            return $('<div class="select2-selected">' + image + data.text + '</div>');
        }
    });

    $(this).on('change', function () {
        $('[data-relation="' + relation_id + '"]').val( $(this).val() );
    });

    var source = $("#add-template").html();
    var template = Handlebars.compile(source);

    var contentRelation = [];
    $.each($relation.data('types'), function() {
        contentRelation.push({
            'name': this.name,
            'href': Routing.generate('integrated_content_content_new', {type: this.type, relation: relation_id, '_format': 'iframe.html'})
        });
    });

    var context = {relations: contentRelation};
    var html = template(context);
    $addWrapper.html(html);
});

var resizeIFrame = function(height, iFrame) {
    if (height <= 0) {
        height = $(window).height() - 120;
    }

    if ((height + 20) >= $(window).height()) {
        height = $(window).height() - 120;
    }

    iFrame.attr('height', height);
};

$('.relations').on('click', '[data-modal]', function(e){
    e.preventDefault();
    if ($(this).parents('.add-relation').length) {
        var modal = $(this).parents('.add-relation').next('#relation-add-modal');
    } else {
        var modal = $(this).next('#relation-add-modal');
    }
    var iFrame = modal.find('iframe');

    modal.find('.modal-title').text($(this).data('title'));

    iFrame.css('display', 'block').attr('src', $(this).data('href')).load(function(e){

        iFrame.show();
        modal.modal('show');

        var height = $(window).height() - 120;
        if ((iFrame.contents().height() + 20) < $(window).height()) {
            // todo: this does not work in IE
            height = iFrame.contents().height() -100;
        }

        resizeIFrame(height, iFrame);

        iFrame.contents().find('*[data-dismiss="modal"]').click(function(ev){
            ev.preventDefault();
        });

        iFrame.unbind('load');
    });
});
