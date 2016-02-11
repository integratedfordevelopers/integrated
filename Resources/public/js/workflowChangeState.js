$(function() {

    var $nextStatus = $('.next-status-choice');
    var $assigned = $('.assigned-choice');

    var workflowId = $('.workflow-hidden').val();
    var currentStateId = $('.current-state').data('value');

    var changeState = function() {
        var status = $('input:checked', $nextStatus).val();

        if (status == undefined) {
            status = currentStateId;
        }

        $assigned.attr('disabled','disabled');
        $.ajax({
            url: Routing.generate('integrated_workflow_change_state', {
                'workflow':workflowId,
                'state':status,
                '_format':'json'
            }),
            dataType: 'json',
            success: function(response) {
                var selected = $('option:selected', $assigned).val();

                var $firstOption = $('option:first', $assigned);
                var $option = $firstOption.clone();
                $firstOption.siblings().remove();

                $.each(response.users, function(id, name) {
                    var $tmp = $option.clone().val(id).text(name);

                    if (id==selected) {
                        $tmp.attr('selected','selected');
                    }

                    $tmp.appendTo($assigned);
                });
                $assigned.removeAttr('disabled');

                $.each(response.fields, function(field, values) {
                    var $el = $('.' + field);

                    if ($el) {
                        $el.removeAttr('required');
                        $el.removeAttr('disabled');

                        if (values.disabled) {
                            $el.attr('disabled','disabled');
                        } else if (values.required) {
                            $el.attr('required','required');
                        }
                    }
                });
            }
        });
    };

    $('input', $nextStatus).change(changeState);
    changeState();

});