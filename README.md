# IntegratedThemeBundle

## Installation

composer.json

    "integrated/theme-bundle": "dev-master",

AppKernel.php

    $bundles = array(
        ...
        new Integrated\Bundle\PageBundle\IntegratedThemeBundle(),
        ...
    );


## Configuration

config.yml

    integrated_theme:
        themes:
            mytheme1:
                paths: 
                    - @AppBundle/Resources/views/themes/mytheme1
                fallback: 
                    - default
            mytheme2:
                paths:
                    - @AppBundle/Resources/views/themes/mytheme2
                    - @OtherBundle/Resources/views/themes/mytheme2
                fallback: 
                    - mytheme1
                    - mytheme3
