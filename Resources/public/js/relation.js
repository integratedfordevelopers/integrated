/* add select 2 for each relations input */
$(".relation-items").each(function() {
    var multiple = $(this).data('multiple');
    var relation_id = $(this).attr('id');

    var $relation = $(this);
    var $formControl = $('[data-relation="' + relation_id + '"]');
    var defaultValues = $.parseJSON($('#default_references').val());

    if (defaultValues.length && defaultValues[relation_id].length) {
        $.each(defaultValues[relation_id], function() {
            $relation.append('<option value="'+this.id+'">'+this.title+'</option>');
        });
    }

    $relation.select2({
        minimumInputLength: 4,
        multiple: multiple,
        ajax: {
            type: 'GET',
            url: Routing.generate("integrated_content_content_index"),
            dataType: 'json',
            data: function(param) {
                return {
                    relation: relation_id,
                    limit: 5,
                    sort: 'title',
                    _format: 'json',
                    q: param.term
                };
            },
            processResults: function (data) {
                var items = [];

                console.log(data);

                if ('items' in data) {
                    for (var k in data.items) {
                        items.push({id: data.items[k].id, text: data.items[k].title});
                    }
                }

                return { results: items };
            }
        }
    });

    $(this).on('change', function () {
        $('[data-relation="' + relation_id + '"]').val( $(this).val() );
    });
});

var resizeIFrame = function(height, iFrame) {
    if (height <= 0) {
        height = $(window).height() - 120;
    }

    if ((height + 20) >= $(window).height()) {
        height = $(window).height() - 120;
    }

    console.log(iFrame, 'resizeIframe');

    iFrame.attr('height', height);
};

$('.relations').on('click', 'a[data-modal]', function(e){
    e.preventDefault();
    var modal = $(this).next('.bs-example-modal-lg');
    var iFrame = modal.find('iframe');

    modal.find('.modal-title').text($(this).data('title'));

    iFrame.css('display', 'block').attr('src', $(this).attr('href') + '&_format=iframe.html').load(function(e){

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


            var result = modal.modal('hide');

            console.log(result, 'modal');
        });

        iFrame.unbind('load');
    });
});