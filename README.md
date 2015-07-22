# IntegratedWebsiteBundle #
This bundle provides frontend block management

## Requirements ##
* See the require section in the composer.json

## Features ##
* Frontend block management

## Documentation ##
* [Integrated for Developers](http://integratedfordevelopers.com/ "Integrated for Developers")

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
Pull requests are welcome. Please see our [CONTRIBUTING guide](http://integratedfordevelopers.com/contributing "CONTRIBUTING guide").

## About ##
This bundle is part of the Integrated project. You can read more about this project on the
[Integrated for Developers](http://integratedfordevelopers.com/ "Integrated for Developers") website.
