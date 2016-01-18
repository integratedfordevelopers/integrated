$(function() {

    var $nextStatus = $('.next-status-choice');
    var $assigned = $('.assigned-choice');

    var workflowId = $('.workflow-hidden').val();
    var currentStateId = $('.current-state').data('value');

    var UpdateAssignedList = function() {
        var status = $('input:checked', $nextStatus).val();

        if (status == undefined) {
            status = currentStateId;
        }

        $.ajax({
            url: Routing.generate('integrated_workflow_assigned', {
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
            }
        });
    };

    $('input', $nextStatus).change(UpdateAssignedList);
    UpdateAssignedList();

});