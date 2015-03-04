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
    
    use Integrated\Bundle\SlugBundle\Mapping\Annotations\Slug;
    
    class Article
    {
        /**
         * @var string
         */
        protected $title;
    
        /**
         * @var string
         * @Slug(fields={"title"})
         */
        protected $slug;
        
        ...
    }