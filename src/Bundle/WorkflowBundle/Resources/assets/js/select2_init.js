
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
    $(document).ready(function() {
        triggerSelect2();

        $('.state_add_button').click(function () {
            // use timeout to trigger select2 init after the new state and select2 fields are loaded
            setTimeout(triggerSelect2, 100);
        });
    });

    function triggerSelect2() {
        $('select.select2').select2();
    }
})
();