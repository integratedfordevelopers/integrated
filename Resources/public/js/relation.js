/* add select 2 for each relations input */
$(".relation-items").each(function() {
    var multiple = $(this).data('multiple');
    var relation_id = $(this).attr('id');

    $(this).select2({
        multiple: multiple,
        initSelection: function(element, callback) {
            var url = Routing.generate("integrated_content_content_index")
                + '?relation=' + relation_id
                + '&limit=5&q='
                + '&sort=title&_format=json';

            $.ajax({
                url: url,
                dataType: 'JSON',
                success: function (data) {
                    var ids = $('[data-relation="' + relation_id + '"]').val().split(',');

                    /* add already selected items */
                    var items = [];
                    if ('items' in data) {
                        for (var k in data.items) {
                            var item = data.items[k];

                            if ($.inArray(item.id, ids) >= 0) {
                                items.push({id: item.id, text: item.title});
                            }
                        }
                    }
                    callback(multiple ? items : items[0]);

                    /* add create button */
                    var addTemplateSource = $('#add-template').html();
                    var addTemplate = Handlebars.compile(addTemplateSource);
                    element.after(addTemplate(data));
                }
            });
        },
        ajax: {
            multiple: multiple,
            url: function (params) {
                return Routing.generate("integrated_content_content_index")
                    + '?relation=' + relation_id
                    + '&limit=5&q=' + params
                    + '&sort=title&_format=json';
            },
            processResults: function (data) {
                var items = [];

                if ('items' in data) {
                    for (var k in data.items) {
                        items.push({id: data.items[k].id, text: data.items[k].title});
                    }
                }

                return { results: items };
            }
        }
    });

    if (multiple) $(this).select2('val', [], true);

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