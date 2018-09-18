(function($) {

    $(function() {
        $('.form-config-fields').each(function() {
            new FormConfigFields(this);
        })
    });

    function FormConfigFields(root) {
        var $root = $(root),
            $id = $root.data('field-id'),
            $modal = $('<div id="form-config-field-modal-container-'+$id+'"></div>'),
            $ajax = null,
            $template = {
                'selected': Handlebars.compile(($root.find('#form-config-field-selected-row-'+$id).html())),
                'available': Handlebars.compile(($root.find('#form-config-field-available-row-'+$id).html())),
                'modal': Handlebars.compile(($root.find('#form-config-field-modal-'+$id).html()))
            };

        $('body').append($modal);

        register();
        update();

        function register() {
            $root.find('.form-config-fields-selected .form-config-fields-remove').click(remove);
            $root.find('.form-config-fields-selected .form-config-fields-up').click(up);
            $root.find('.form-config-fields-selected .form-config-fields-down').click(down);
            $root.find('.form-config-fields-available .form-config-fields-add').click(add);
            $root.find('.form-config-fields-add-custom .form-config-fields-modal').click(modal);
        }

        function remove() {
            var row = $(this).parents('.form-config-fields-row');

            switch (row.data('field-type')) {
                case 'document':
                case 'relation':
                    var html = template('available', row.data('field-name'), row.data('field-type'), row.data('field'));
                        rows = $root.find('.form-config-fields-available .form-config-fields-row').filter(function() {
                            return row.data('field-name') < $(this).data('field-name')
                        });

                    if (rows.size()) {
                        rows.first().before(html);
                    } else {
                        $root.find('.form-config-fields-available .form-config-fields-rows').append(html);
                    }

                    break;
            }

            row.remove();

            update();
        }

        function up() {
            var row = $(this).parents('.form-config-fields-row'),
                prev = row.prev();

            if (prev.size()) {
                prev.before(row);
            }

            update();
        }

        function down() {
            var row = $(this).parents('.form-config-fields-row'),
                next = row.next();

            if (next.size()) {
                next.after(row);
            }

            update();
        }

        function add() {
            var row = $(this).parents('.form-config-fields-row');

            $root.find('.form-config-fields-selected .form-config-fields-rows').append(template(
                'selected',
                row.data('field-name'),
                row.data('field-type'),
                row.data('field')
            ));

            row.remove();

            update();
        }

        function modal() {
            var html = $($template.modal());

            $modal.html(html);

            html.find('.form-config-fields-submit').attr('disabled', true).click(submit);
            html.modal('show');

            abort();

            $ajax = $.ajax({url: $root.data('field-custom-url')});
            $ajax.done(done);

            html.on('hidden.bs.modal', abort);

            function abort() {
                if ($ajax) {
                    $ajax.abort();
                }

                $ajax = null;
            }

            function done(response) {
                $ajax = null;

                if (typeof response === 'object') {
                    html.modal('hide');

                    var form = html.data('field-form');

                    form = form.replace(new RegExp(html.data('field-form-name'), 'g'), id());
                    form = $(form);
                    form = $('<div>').html(form);

                    hydrate(form, response.data.form);

                    response.data.form = form.html();

                    $root.find('.form-config-fields-selected .form-config-fields-rows').append(template(
                        'selected',
                        response.name,
                        response.type,
                        response.data
                    ));

                    update();
                } else {
                    html.find('.modal-body').html(response);
                    html.find('.modal-body input').first().focus();

                    html.find('.form-config-fields-submit').attr('disabled', false);
                }
            }

            function submit() {
                var form = $modal.find('.modal-body form');

                if (!form.size()) {
                    return;
                }

                html.find('.form-config-fields-submit').attr('disabled', true);

                abort();

                $ajax = $.post(form.attr('action') || $root.data('field-custom-url'), form.serializeArray());
                $ajax.done(done);
            }

            function id() {
                return (Math.random().toString(16).substr(2) + new Date().getTime().toString(16)).substr(-13);
            }

            function hydrate(dom, data, prefix) {
                prefix = prefix || '';

                for (var name in data) {
                    if (typeof data[name] === 'object') {
                        hydrate(dom, data[name], '[' + name + ']');
                    } else {
                        var elm = dom.find('[name$="' + prefix + '[' + name + ']"]');

                        if (elm.is(':hidden')) {
                            elm.val(data[name]);
                        } else if (elm.is(':checkbox')) {
                            elm.attr('checked', elm.val() == data[name]);
                        }
                    }
                }
            }
        }

        function template(template, name, type, data) {
            var html = null;

            if (template === 'selected') {
                html = $($template.selected(data));

                html.data('field-name', name);
                html.data('field-type', type);
                html.data('field', data);

                html.addClass('form-config-fields-row');

                html.find('.form-config-fields-remove').click(remove);
                html.find('.form-config-fields-up').click(up);
                html.find('.form-config-fields-down').click(down);

                return html;
            }

            if (template === 'available') {
                html = $($template.available(data));

                html.data('field-name', name);
                html.data('field-type', type);
                html.data('field', data);

                html.addClass('form-config-fields-row');

                html.find('.form-config-fields-add').click(add);

                return html;
            }

            return html;
        }

        function update() {
            var container;

            $root.find('.form-config-fields-selected .form-config-fields-up')
                .attr('disabled', false).first()
                .attr('disabled', true);

            $root.find('.form-config-fields-selected .form-config-fields-down')
                .attr('disabled', false).last()
                .attr('disabled', true);

            container = $root.find('.form-config-fields-selected');

            if (container.find('.form-config-fields-row').size()) {
                container.show();
            } else {
                container.hide();
            }

            container = $root.find('.form-config-fields-available');

            if (container.find('.form-config-fields-row').size()) {
                container.show();
            } else {
                container.hide();
            }
        }
    }

})(jQuery);