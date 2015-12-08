# IntegratedSlugBundle

## Installation

composer.json

    "integrated/slug-bundle": "dev-master",

AppKernel.php

    $bundles = array(
        ...
        new Integrated\Bundle\SlugBundle\IntegratedSlugBundle(),
        ...
    );


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
   
