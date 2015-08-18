# Getting started #

## Installation ##
This bundle can be installed following these steps:

### Install using composer ###

    $ php composer.phar require integrated/content-bundle:~0.3

### Enable the bundle ###

    // app/AppKernel.php
    public function registerBundles()
    {
        return array(
            // ...
            new Integrated\Bundle\ContentBundle\ContentBundle(),
            // ...
        );
    }

## Next steps ##
* [Configuration](configuration.md)
* [Relations](relations.md)