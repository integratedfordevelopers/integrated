$(function() {
    var $primaryChannel = $('.primary-channel'),
        $channelInputs = $('.channel-options input'),
        $primarySelector = $('<a href="#">').addClass('primary-channel-selector')
            .text(' (' + $primaryChannel.data('make-primary-text') + ')');

    updateChannelSelectors();

    function updateChannelSelectors()
    {
        $('.primary-channel-selector').remove();
        $('.is-primary-channel').removeClass('is-primary-channel');

        $channelInputs.each(function() {
            var $input = $(this);

            if ($input.is(':checked')) {
                if ($primaryChannel.val() == $input.val()) {
                    $input.parent().addClass('is-primary-channel');
                } else {
                    $input.parent().append($primarySelector.clone());
                }
            }
        });
    }

    $channelInputs.change(function () {
        var $input = $(this);

        //the checkbox has been unchecked
        if (!$input.is(':checked')) {
            removePrimary($input);
        } else {
            checkIfOtherInputFieldsAreChecked($input);
        }

        updateChannelSelectors();
    });

    $(document).on('click', '.primary-channel-selector', function () {
        makePrimary($(this).parent().find('input'));

        updateChannelSelectors();

        //don't follow the link
        return false;
    });

    function makePrimary($input) {
        $primaryChannel.val($input.val());
    }

    function removePrimary ($input) {
        if ($input.parent().hasClass('is-primary-channel')) {
            $primaryChannel.val('');
        }

        checkIfOtherInputFieldsAreChecked($input);
    }

    function checkIfOtherInputFieldsAreChecked($input) {
        $selectedChannelInputs = $('.channel-options input:checked').not($input);
        //if only one other item is checked then that should be the new primary
        if (1 === $selectedChannelInputs.length) {
            makePrimary($selectedChannelInputs.first());
        } else if (0 ===  $selectedChannelInputs.length && $input.is(':checked')) {
            makePrimary($input);
        }
    }
});