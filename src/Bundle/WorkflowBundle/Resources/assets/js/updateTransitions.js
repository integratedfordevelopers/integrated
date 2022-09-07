/*
 * This file is part of the Integrated package.
 *
 * (c) e-Active B.V. <integrated@e-active.nl>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

import '../../../../ContentBundle/Resources/assets/js/collection'

const workflow = 'integrated_workflow_definition_states';

$(document).ready(function (e) {
    init();
});

function init() {
    $('#'+workflow+' [data-addfield="collection"]').click(function (event) {
        setTimeout(update, 0);
        setTimeout(register, 0);
    });

    register();
}

function update() {
    const states = getStates();

    $('#'+workflow+' select.state_transitions_input_field').each(function() {
        const elm = $(this);
        const id = parseInt(this.id.replace(workflow, '').replace('transitions', '').replace('_', ''));
        const val = elm.val();

        elm.empty();

        for (const index in states) {
            if (states[index].id == id) {
                continue;
            }

            elm.append($('<option></option>').val(states[index].id).html(states[index].label))
        }

        elm.val(val);
    });

    function getStates() {
        let map = {};

        $('#'+workflow+' .state_name_input_field').each(function() {
            map[parseInt(this.id.replace(workflow, '').replace('name', '').replace('_', ''))] = $(this).val();
        });

        let array = [];

        for (const id in map) {
            array.push({
                'id': id,
                'label': map[id],
            })
        }

        array.sort(function (a, b) {
            return a.label == b.label ? 0 : a.label < b.label ? -1 : 1
        });

        return array;
    }
}

function register() {
    $('#'+workflow+' [data-removefield="collection"]').each(function () {
        let elm = $(this);

        if (!elm.data('workflow-state-event-registered')) {
            elm.on('click', function(event) {
                setTimeout(update, 0);
            })
        }

        elm.data('workflow-state-event-registered', 1);
    })

    $('#'+workflow+' .state_name_input_field').each(function () {
        let elm = $(this);

        if (!elm.data('workflow-state-event-registered')) {
            elm.on('change', function(event) {
                update();
            })
        }

        elm.data('workflow-state-event-registered', 1);
    })
}
