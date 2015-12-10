$(function() {
    var $parent = $('#channel_domains');
    var $primaryDomain = $('#channel_primaryDomain');

    var SetPrimaryDomain = function($checkbox) {
        var value = $checkbox.parent('label').prev('input[type=text]').val();
        $('#channel_primaryDomain').val(value);
    };

    var RefreshCheckedStatus = function () {
        $('input[name=check_primary_domain]', $parent).each(function () {
            if ( $(this).parent('label').prev('input:text').val().trim() == '' ) {
                $(this).attr('disabled','disabled').removeAttr('checked');
            } else {
                $(this).removeAttr('disabled');
            }
        });

        if ($primaryDomain.val().length > 0) {
            var value = $primaryDomain.val();
            var $primaryInput = $('input[value="'+value+'"]:text', $parent);

            if ($primaryInput.length > 0) {
                $primaryInput.next('label').find('input:radio').attr('checked','checked').click();
            }
        }

        if ($('input[name=check_primary_domain]:checked', $parent).length == 0) {
            $('input[name=check_primary_domain]:first', $parent).attr('checked','checked').click();
        }

        SetPrimaryDomain($('input[name=check_primary_domain]:checked', $parent));

    };

    RefreshCheckedStatus();

    $($parent).on('keyup','input[type=text]', function () {
        RefreshCheckedStatus();
    });

    $(document).on('change', 'input[name=check_primary_domain]', function() {
        SetPrimaryDomain($(this));
    });
});