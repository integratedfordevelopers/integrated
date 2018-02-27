# IntegratedCommentBundle #
This bundle provides block management

## Requirements ##
* See the require section in the composer.json

## Features ##
* Ability to add comments to input fields and tinyMCE editor

## Documentation ##
* [Integrated for Developers](http://integratedfordevelopers.com/ "Integrated for Developers")

## Installation ##
This bundle can be installed following these steps:

### Install using composer ###

    $ php composer.phar require integrated/comment-bundle:~0.6

### Enable the bundle ###

    // app/AppKernel.php
    public function registerBundles()
    {
        return array(
            // ...
            new Integrated\Bundle\CommentBundle\IntegratedCommentBundle()
            // ...
        );
    }

### Import the routing ###

    # app/config/routing.yml
    integrated_comment:
        resource: "@IntegratedCommentBundle/Resources/config/routing.xml"
        prefix: "/admin"
        
### Escaping comments ###
Comments made with tinyMCE will be added as html comments inside the source code, like this:

    <p>text <!--integrated-comment=0a80640f3d5e380baab6d8099aad9580-->commented text<!--end-integrated-comment--> not commented text</p>
    
If you don't like the html comments in your source code you can filter it with twig filter "remove_comments"
    
    {{ content.content|remove_comments }}

## License ##
This bundle is under the MIT license. See the complete license in the bundle:

    LICENSE

## Contributing ##
Pull requests are welcome. Please see our [CONTRIBUTING guide](http://integratedfordevelopers.com/contributing "CONTRIBUTING guide").

## About ##
This bundle is part of the Integrated project. You can read more about this project on the
[Integrated for Developers](http://integratedfordevelopers.com/ "Integrated for Developers") website.
