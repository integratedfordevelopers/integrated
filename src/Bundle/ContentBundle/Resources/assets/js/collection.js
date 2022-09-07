
$('[data-prototype]').each(function(index, elm) {
    init($(this));

    function init($collection) {
        $collection.data('index', $collection.find('ul li').length);

        $collection.find('ul li').each(function() {
            register($(this));
        });

        $collection.find('[data-addfield="collection"]').click(function(event) {
            event.preventDefault();

            append();
        });

        function append() {
            let index = $collection.data('index');
            let prototype = $collection.data('prototype')
                .replace(/__name__/g, index)
                .replace(/__id__/g, $collection.attr('id') + '_' + index);

            let item = $('<li></li>').append(prototype);

            $collection.data('index', index + 1);
            $collection.find('ul').append(item);

            register($collection.find('ul li:last-child'));
        }

        function register(elm) {
            elm.find('[data-removefield="collection"]').on('click', function(event) {
                event.preventDefault();

                elm.remove();
            })
        }
    }
});
