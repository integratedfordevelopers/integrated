$(function() {
    var $domains_collection = $('.channel-domains');
    var $primary_domain_input = $('.primary-domain-input');

    $domains_collection.on('keyup', 'input[type=text]', refresh_checked_status);
    $domains_collection.on('click', '.primary-domain-radio', function() {
        var value = $(this).closest('.row').find('input:text').val();
        $primary_domain_input.val(value);
    });
    $domains_collection.on('change', 'input[type=text]', function () {
        if ($(this).closest('.row').find('.primary-domain-radio').is(":checked")) {
            $primary_domain_input.val($(this).val());
        }
    });

    function refresh_checked_status() {
        /* if a domain input has entered domain name allow to set it as primary */
        $('.primary-domain-radio', $domains_collection).each(function () {
            var domain_name = $(this).closest('.row').find('input:text').val().trim();

            if (domain_name) {
                $(this).removeAttr('disabled');

                /* if one of domains */
                if ($primary_domain_input.val() == domain_name) {
                    $(this).prop('checked', true).trigger('click');
                }
            } else {
                $(this).attr('disabled','disabled').prop('checked', false);
            }
        });

        /* if no selected primary domain, select first */
        if (!$('.primary-domain-radio:checked', $domains_collection).length) {
            $('.primary-domain-radio:first', $domains_collection).prop('checked', true).trigger('click');
        }
    }

    refresh_checked_status();
  });
