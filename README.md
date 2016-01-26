# IntegratedContentBundle #
This bundle provides the document structure for Integrated.

## Requirements ##
* See the require section in the composer.json

## Features ##
* Document structure
* Base templates for Integrated

## Documentation ##
* [Integrated for Developers](http://integratedfordevelopers.com/ "Integrated for Developers")

## Installation ##
This bundle can be installed following these steps:

### Install using composer ###

    $ php composer.phar require integrated/content-bundle

### Enable the bundle ###

    // app/AppKernel.php
    public function registerBundles()
    {
        return array(
            // ...
            new Integrated\Bundle\ContentBundle\IntegratedContentBundle()
            // ...
        );
    }

### Import the routing ###

    # app/config/routing.yml
    integrated_content:
        resource: @IntegratedContentBundle/Resources/config/routing.xml

### Configuring the assets ###

The IntegratedContentBundle uses the [SpBowerBundle](https://github.com/Spea/SpBowerBundle) for handling the external
resources.

	# app/config/config.yml
	sp_bower:
        paths:
            IntegratedContentBundle: ~

The base template of the ContentBundle uses two [named assets](http://symfony.com/doc/current/cookbook/assetic/asset_management.html#using-named-assets):

1. `integrated_js`
2. `integrated_css`

These two named assets must be defined in the `app/config/config.yml`.

The IntegratedContentBundle uses [Sass](http://sass-lang.com/) for generating the stylesheet, in order to use these 
files a Sass filter can be used:

	# app/config/config.yml
	assetic:
		# ...
		filters:
			sass:
				bin: /usr/bin/sass
				apply_to: "\.scss$"
				style: compressed
		# ...
		assets:
			integrated_css:
				inputs:
					- @IntegratedContentBundle/Resources/public/sass/main.scss
				filters:
					- sass
				output: css/main.css
			integrated_js:
				inputs:
					# Add your custom javascript files here

## License ##
This bundle is under the MIT license. See the complete license in the bundle:

    LICENSE

## Contributing ##
Pull requests are welcome. Please see our [CONTRIBUTING guide](http://integratedfordevelopers.com/contributing "CONTRIBUTING guide").

## About ##
This bundle is part of the Integrated project. You can read more about this project on the
[Integrated for Developers](http://integratedfordevelopers.com/ "Integrated for Developers") website.