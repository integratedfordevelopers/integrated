# DateTimeType #

The IntegratedFormTypeBundle ships with a custom DateTime Type.

## Front-end resources ##
The DateTime Type has the following resources:

* [DateTime Picker](https://github.com/smalot/bootstrap-datetimepicker)

## Assets ##
Add the following assets to the Integrated assets:

    //app/config.yml

    // ...
    assetic:
        // ...
        assets:
            integrated_css:
                inputs:
                    - @IntegratedFormTypeBundle/Resources/public/components/smalot-bootstrap-datetimepicker/css/bootstrap-datetimepicker.min.css
            integrated_js:
                inputs:
                    - @IntegratedFormTypeBundle/Resources/public/components/smalot-bootstrap-datetimepicker/js/bootstrap-datetimepicker.min.js
                    - @IntegratedFormTypeBundle/Resources/public/js/*