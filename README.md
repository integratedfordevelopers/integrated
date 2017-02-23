# IntegratedSocialBundle #
Provides connectors for social media.

## Requirements ##
* See the require section in the composer.json

## Documentation ##
* [Integrated for developers website](http://integratedfordevelopers.com/content/documentation "Integrated for developers website")

## Installation ##
This bundle can be installed following these steps:

### Install using composer ###

    $ php composer.phar require integrated/social-bundle:~0.6

### Enable the bundle ###

    // app/AppKernel.php
    public function registerBundles()
    {
        return array(
            // ...
            new Integrated\Bundle\SocialBundle\IntegratedSocialBundle()
            // ...
        );
    }

## License ##
This bundle is under the MIT license. See the complete license in the bundle:

    LICENSE

## Contributing ##
Pull requests are welcome. Please see our [CONTRIBUTING guide](http://www.integratedfordevelopers.com/contributing "CONTRIBUTING guide").

## About ##
This bundle is part of the Integrated project. You can read more about this project on the
[Integrated for developers](http://www.integratedfordevelopers.com "Integrated for developers") website.