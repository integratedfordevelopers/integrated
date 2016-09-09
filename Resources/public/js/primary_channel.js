$(function() {
    var $primaryChannel = $('.primary-channel'),
        $channelInputs = $('.channel-options input');

    $channelInputs.each(function() {
        var $input = $(this),
            $selector = $('<a href="#">').addClass('primary-channel-selector')
                .append($('<span>').addClass('glyphicon glyphicon-king'));

        if ($input.is(':checked') && $primaryChannel.val() == $input.val()) {
            $input.parent().addClass('primary-channel');
        }

        $input.after($selector);

        $selector.tooltip({
            placement: 'left',
            title: function () {
                if ($(this).parent().hasClass('primary-channel')) {
                   if ($('.channel-options input:checked').length === 1) {
                       return 'Cannot unset (only one channel is selected)'
                   } else {
                       return 'Unset active channel'
                   }
                } else {
                    return 'Make channel active';
                }
            },
            trigger: 'hover'
        });
    });

    $channelInputs.change(function () {
        var $input = $(this);

        //the checkbox has been unchecked
        if (!$input.is(':checked')) {
            removePrimary($input);
        } else {
            checkIfOtherInputFieldsAreChecked($input);
        }
    });

    $('.primary-channel-selector').click(function () {
        var $input = $(this).parent().find('input');

        if ($(this).parent().hasClass('primary-channel')) {
            removePrimary($input)
        } else {
            makePrimary($input);
        }

        //don't follow the link
        return false;
    });

    function makePrimary($input) {
        //unset active class
        $('.channel-options label').each(function() {
            $(this).removeClass('primary-channel');
        });

        $input.parent().addClass('primary-channel');

        $primaryChannel.val($input.val());

        if (!$input.is(':checked')) {
            //input is not yet checked, so let's check the checkbox
            $input.prop('checked', true);
        }
    }

    function removePrimary ($input) {
        var $parent = $input.parent();

        if ($parent.hasClass('primary-channel')) {
            $parent.removeClass('primary-channel');
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