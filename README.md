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

### Configure the bundle ###

To enable the twitter connector set the consumer in the config

    # app/config/config.yml
    integrated_social:
        twitter:
            consumer_key: '%twitter_consumer_key%'
            consumer_secret: '%twitter_consumer_secret%'

To enable the facebook connector set the consumer in the config

    # app/config/config.yml
    integrated_social:
        facebook:
            app_id: '%facebook_app_id%'
            app_secret: '%facebook_app_secret%'

## License ##
This bundle is under the MIT license. See the complete license in the bundle:

    LICENSE

## Contributing ##
Pull requests are welcome. Please see our [CONTRIBUTING guide](http://www.integratedfordevelopers.com/contributing "CONTRIBUTING guide").

## About ##
This bundle is part of the Integrated project. You can read more about this project on the
[Integrated for developers](http://www.integratedfordevelopers.com "Integrated for developers") website.