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
    var statesContainer = $('#' + statesId).find('.integrated-collection');
    var states = $(statesContainer).children();

    var stateAdded = [];
    var stateTransitionsAdded = [];

    $(document).ready(function () {
        if ($("workflow_definition_new_states").length) {
            statesId = 'workflow_definition_new_states';
            statesContainer = $('#' + statesId).find('.integrated-collection');
            states = $(statesContainer).children();
        }

        setTriggers();
        orderStates();
    });

    function setTriggers()
    {
        $.each(states, function (i, el) {
            var id = getId(el);

            $('#' + statesId + '_' + id + '_transitions').on('change', function(e){
                orderStates();
            });

            $('#' + statesId + '_' + id + '_default').click(function (e) {
                orderStates();
            });
        });

        $('.state_delete_button').click(function (e) {
            orderStates();
        });

        $('.state_add_button').click(function (e) {
            orderStates();
        });

        $('#' + statesId).find('.state_name_input_field').on("change", function (e) {
            orderStates();
        });
    }

    function orderStates()
    {
        // reset
        stateAdded = [];
        stateTransitionsAdded = [];

        // initial sort
        sort();

        // add default state and it's transitions
        addDefault();

        // add all other states
        $.each(states, function (i, el) {
            var id = getId(el);
            if (undefined == stateAdded[id]) {
                //this state is not in the tree of the default state, append at the end
                statesContainer.append($('#' + statesId + '_' + id).parents('li'));
                addTransitionsTree(id);
            }
        });
    }

    function sort()
    {
        states.sort(function (a, b) {
            // get name of a
            var aText = $('#' + statesId + '_' + getId(a) + '_name').val();
            // get name of b
            var bText = $('#' + statesId + '_' + getId(b) + '_name').val();

            return aText == bText ? 0 : aText < bText ? -1 : 1;
        });
    }

    function addDefault()
    {
        $.each(states ,function (i, el) {
            var id = getId(el);
            var defaultCheckbox = $('#' + statesId + '_' + id + '_default');
            var isDefault = defaultCheckbox[0].checked;

            if (undefined == stateAdded[id] && isDefault) {
                // append default state
                statesContainer.append(el);
                stateAdded[id] = true;

                // append transitions
                addTransitionsTree(id);
            }
        });
    }

    function addTransitionsTree(id)
    {
        // find transitions
        var transitions = $('#' + statesId + '_' + id + '_transitions').find("option:selected");

        // add them (if not already)
        $.each(transitions, function (i, el){
            var tid = $(el).val();
            if (undefined == stateAdded[tid]) {
                statesContainer.append($('#' + statesId + '_' + tid).parents('li'));
                stateAdded[tid] = true;
            }
        });

        // add transitions of transitions (if not already)
        $.each(transitions, function (i, el){
            var tid = $(el).val();
            if (undefined == stateTransitionsAdded[tid]) {
                stateTransitionsAdded[tid] = true;
                addTransitionsTree(tid);
            }
        });
    }

    function getId(stateLi)
    {
        // get the id number of the given li element
        return parseInt(
            $(stateLi).find('div[id^="' + statesId + '_"]')[0].id
                .replace(statesId + '_', '')
        );
    }
})
();