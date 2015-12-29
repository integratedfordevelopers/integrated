# IntegratedWebsiteBundle #
This bundle provides a website front-end for content in Integrated and also the capabilities to edit pages and navigation.

## Requirements ##
* See the require section in the composer.json

## Features ##
* Provides a website front-end for content in Integrated
* Provides capabilities to edit pages
* Provides capabilities to edit navigation

## Documentation ##
* [Integrated for developers website](http://www.integratedfordevelopers.com "Integrated for developers website")

## Installation ##
This bundle can be installed following these steps:

### Install using composer ###

    $ php composer.phar require integrated/website-bundle:~0.3

### Enable the bundle ###

    // app/AppKernel.php
    public function registerBundles()
    {
        return array(
            // ...
            new Integrated\Bundle\WebsiteBundle\IntegratedWebsiteBundle()
            // ...
        );
    }

### Import the routing ###

    # app/config/routing.yml
    integrated_website:
        resource: @IntegratedWebsiteBundle/Resources/config/routing.xml

## License ##
This bundle is under the MIT license. See the complete license in the bundle:

    LICENSE

## Contributing ##
Pull requests are welcome. Please see our [CONTRIBUTING guide](http://www.integratedfordevelopers.com/contributing "CONTRIBUTING guide").

## About ##
This bundle is part of the Integrated project. You can read more about this project on the
[Integrated for developers](http://www.integratedfordevelopers.com "Integrated for developers") website.
