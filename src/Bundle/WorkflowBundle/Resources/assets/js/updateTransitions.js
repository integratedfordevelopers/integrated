/*
 * This file is part of the Integrated package.
 *
 * (c) e-Active B.V. <integrated@e-active.nl>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

/*
 * @author Roy Frans <roy@e-active.nl>
 */
(function()
{
    var statesId = 'workflow_definition_edit_states';
    var states = $('#' + statesId).find('.integrated-collection').children();

    $(document).ready(function (e) {
        //trigger sorting
        if ($('workflow_definition_new_states').length) {
            statesId = 'workflow_definition_new_states';
            states = $('#' + statesId).find('.integrated-collection').children();
        }

        $.each(states, function (i, el) {
            sort(getId(el));
        });

        setTriggers();
    });

    function setTriggers()
    {
        $('.state_add_button').click(function (e) {
            // use timeout to make sure the state has been added before processing it
            setTimeout(stateAdded, 100);
        });

        $('#' + statesId)
            .delegate('.state_delete_button', 'click', function (e) {
                var id = parseInt($(this).attr('data-field').replace("workflow_definition_edit_states_", ""));

                stateDeleted(id);
            }).delegate('.state_name_input_field', "change", function (e) {
                var id = parseInt(this.id.replace(statesId + "_", "").replace("_name", ""));

                stateChanged(id);
            });
    }

    function stateAdded()
    {
        // find id of new state
        var newStateId = getId($('#' + statesId).find('.integrated-collection').children().last());

        // get new state name
        var newStateName = $('#' + statesId +'_' + newStateId + '_name').val();

        // populate new state with current transitions
        for (var i = 0; i < newStateId; i++) {
            var stateName = $('#' + statesId + '_' + i + '_name').val();
            $('#' + statesId + '_' + newStateId + '_transitions')
                .append(
                    $('<option></option>')
                        .val(i)
                        .html(stateName)
                );
        }

        // add new state to transition lists of other states
        for (i = 0; i < newStateId; i++) {
            $('#' + statesId + '_' + i + '_transitions')
                .append(
                    $('<option></option>')
                        .val(newStateId)
                        .html(newStateName)
                );
            // transition list may need sorting
            sort(i);
        }

        // transition list of new state may need sorting
        sort(newStateId);
    }

    function stateChanged(id)
    {
        // get state name
        var stateName = $('#' + statesId + '_' + id + '_name').val();

        // replace name of this state in transition lists of other states
        var stateLength = $('#' + statesId).find('.integrated-collection').children().length;

        for (var i = 0; i < stateLength; i++) {
            $('#' + statesId + '_' + i + '_transitions option[value=\'' + id + '\']').html(stateName);

            // transition list may need sorting
            sort(i);
        }
    }

    function stateDeleted(id)
    {
        // delete this state from transition lists of other states
        var stateLength = $('#' + statesId).find('.integrated-collection').children().length;

        for (var i = 0; i < stateLength; i++) {
            $('#' + statesId + '_' + i + '_transitions option[value=\'' + id + '\']').remove();
        }
    }

    function sort(selectElementId)
    {
        // sort transition list
        var select = $('#' + statesId + '_' + selectElementId + '_transitions');
        select.html(select.find('option').sort(function (a, b) {
            return a.text == b.text ? 0 : a.text < b.text ? -1 : 1
        }));
    }

    function getId(stateLi)
    {
        // get the id number of the given li element
        return parseInt(
            $(stateLi).find('div[id^="' + statesId + '_"]')[0].id
                .replace(statesId + "_", '')
        );
    }
})
();