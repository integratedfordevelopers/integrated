# WysiHtml5xType #

The IntegratedFormTypeBundle ships with a custom WysiHtml5x Type.

## Front-end resources ##
The WysiHtml5x Type has the following resources:

* [wysihtml5](http://edicy.github.io/wysihtml5/)
* [bootstrap3-wysihtml5-bower](https://github.com/Waxolunist/bootstrap3-wysihtml5-bower)

## Assets ##
Add the following assets to the Integrated assets:

    //app/config.yml

    // ...
    assetic:
        // ...
        assets:
            integrated_css:
                inputs:
                	- @IntegratedFormTypeBundle/Resources/public/components/bootstrap3-wysihtml5-bower/dist/bootstrap3-wysihtml5.min.css
            integrated_js:
                inputs:
                    - @IntegratedFormTypeBundle/Resources/public/components/handlebars/handlebars.runtime.min.js
                    - @IntegratedFormTypeBundle/Resources/public/components/wysihtml5x/parser_rules/advanced.js
                    - @IntegratedFormTypeBundle/Resources/public/components/wysihtml5x/dist/wysihtml5x-0.4.3.js
                    - @IntegratedFormTypeBundle/Resources/public/components/bootstrap3-wysihtml5-bower/dist/bootstrap3-wysihtml5.all.min.js
                    - @IntegratedFormTypeBundle/Resources/public/js/wysihtml5x.js
                    - @IntegratedFormTypeBundle/Resources/public/js/*