# IntegratedSlugBundle #
Provides a slugger which can generate a slug from a string and event listeners to auto-generate slugs on chosen fields

## Requirements ##
* See the require section in the composer.json

## Documentation ##
* [Integrated for developers website](http://www.integratedfordevelopers.com "Integrated for developers website")

## Installation ##
This bundle can be installed following these steps:

### Install using composer ###

    $ php composer.phar require integrated/slug-bundle:~0.3

### Enable the bundle ###

    // app/AppKernel.php
    public function registerBundles()
    {
        return array(
            // ...
            new Integrated\Bundle\SlugBundle\IntegratedSlugBundle()
            // ...
        );
    }

## Example
    
    use Doctrine\ODM\MongoDB\Mapping\Annotations as ODM;
    use Integrated\Bundle\SlugBundle\Mapping\Annotations\Slug;
    
    class Article
    {
        /**
         * @var string
         * @ODM\String
         */
        protected $title;
    
        /**
         * @var string
         * @ODM\String
         * @Slug(fields={"title"})
         */
        protected $slug;
        
        ...
    }

### Multiple fields

    @Slug(fields={"title", "anotherField"})
    
### Custom seperator

     @Slug(fields={"title"}, seperator="_")
    
### Custom method to generate slug
    
    @Slug(fields={"getSlug"})

## License ##
This bundle is under the MIT license. See the complete license in the bundle:

    LICENSE

## Contributing ##
Pull requests are welcome. Please see our [CONTRIBUTING guide](http://www.integratedfordevelopers.com/contributing "CONTRIBUTING guide").

## About ##
This bundle is part of the Integrated project. You can read more about this project on the
[Integrated for developers](http://www.integratedfordevelopers.com "Integrated for developers") website.
   
