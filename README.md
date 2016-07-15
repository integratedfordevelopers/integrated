# IntegratedPageBundle #
This bundle provides page management

## Requirements ##
* See the require section in the composer.json

## Features ##
* Page management

## Documentation ##
* [Integrated for Developers](http://integratedfordevelopers.com/ "Integrated for Developers")

## Installation ##
This bundle can be installed following these steps:

### Install using composer ###

    $ php composer.phar require integrated/page-bundle:~0.3

### Enable the bundle ###

    // app/AppKernel.php
    public function registerBundles()
    {
        return array(
            // ...
            new Integrated\Bundle\PageBundle\IntegratedPageBundle()
            // ...
        );
    }

### Import the routing ###

    # app/config/routing.yml
    integrated_page:
        resource: @IntegratedPageBundle/Resources/config/routing.xml
        prefix: "/admin"

## Using contentType pages ##

In order for contentType pages to work you need to define your controllers as services (examples can be found in Resources/config/controllers.xml).

In the service you then have to tag your controller as a contentType controller by adding the tag "integrated_page". Furthermore the "class" attribute is required in this tag.
By default the showAction will be called for the contentType controller, but you can also define one or more actions in the "controller_actions" attribute.

some examples:

    <tag name="integrated_page.contenttype_controller" class="Integrated\Bundle\ContentBundle\Document\Content\Article"/>
    <tag name="integrated_page.contenttype_controller" class="Integrated\Bundle\ContentBundle\Document\Content\Relation\Company" controller_actions="fooAction, showAction"/>

The contentType pages will automatically be created when you create or change a contentType or channel.
Channels must be enabled for the contentType, otherwise no page will be created. With multiple channels a page per channel will be created.

## License ##
This bundle is under the MIT license. See the complete license in the bundle:

    LICENSE

## Contributing ##
Pull requests are welcome. Please see our [CONTRIBUTING guide](http://integratedfordevelopers.com/contributing "CONTRIBUTING guide").

## About ##
This bundle is part of the Integrated project. You can read more about this project on the
[Integrated for Developers](http://integratedfordevelopers.com/ "Integrated for Developers") website.
