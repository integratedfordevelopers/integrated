# IntegratedSitemapBundle #
This bundle provides the frontend of sitemaps

## Requirements ##
* See the require section in the composer.json

## Features ##
* Sitemap management

## Documentation ##
* [Integrated for Developers](http://integratedfordevelopers.com/ "Integrated for Developers")

## Installation ##
This bundle can be installed following these steps:

### Install using composer ###

    $ php composer.phar require integrated/sitemap-bundle:^1.0

### Enable the bundle ###

    // app/AppKernel.php
    public function registerBundles()
    {
        return array(
            // ...
            new Integrated\Bundle\SitemapBundle\IntegratedSitemapBundle()
            // ...
        );
    }

### Import the routing ###

    # app/config/routing.yml
    integrated_sitemap:
        resource: @IntegratedSitemapBundle/Resources/config/routing.xml

## License ##
This bundle is under the MIT license. See the complete license in the bundle:

    LICENSE

## Contributing ##
Pull requests are welcome. Please see our [CONTRIBUTING guide](http://integratedfordevelopers.com/contributing "CONTRIBUTING guide").

## About ##
This bundle is part of the Integrated project. You can read more about this project on the
[Integrated for Developers](http://integratedfordevelopers.com/ "Integrated for Developers") website.
