$(function() {

    var $nextStatus = $('.next-status-choice');
    var $assigned = $('.assigned-choice');

    var workflowId = $('.workflow-hidden').val();
    var contentType = $('.content-type-hidden').val();
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
                'contentType':contentType,
                '_format':'json'
            }),
            dataType: 'json',
            success: function(response) {
                var selected = $('option:selected', $assigned).val();

                var $firstOption = $('option:first', $assigned);
                var $option = $firstOption.clone();
                $firstOption.siblings().remove();

                $.each(response.users, function(index, user) {
                    var $tmp = $option.clone().val(user.id).text(user.name);

                    if (user.id==selected) {
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
                        $el.parents('.form-group').show();

                        if (values.disabled) {
                            $el.attr('disabled','disabled').parents('.form-group').hide();
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

$(document).ready(function () {
    $('#integrated_content_extension_workflow_assigned').select2();
});