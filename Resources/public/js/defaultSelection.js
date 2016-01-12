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
    $(document).ready(function () {
        // make sure that there is never more than 1 state checked as default
        $('#workflow_definition_new_states, #workflow_definition_edit_states').delegate('.state_default_input_field', 'click', function (e) {
            if (this.checked) {
                $('.state_default_input_field').prop('checked', false);
                $(this).prop('checked', true);
            }
        });
    });
})
();