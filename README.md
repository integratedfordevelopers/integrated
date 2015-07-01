# IntegratedWebsiteBundle

## Installation

composer.json

    "integrated/website-bundle": "dev-master",

AppKernel.php

    $bundles = array(
        ...
        new Integrated\Bundle\WebsiteBundle\IntegratedWebsiteBundle(),
        ...
    );

routing.yml

    integrated_website:
        resource: "@IntegratedWebsiteBundle/Resources/config/routing.xml"
        
config.yml
    
    twig:
        form_themes:
            - 'IntegratedWebsiteBundle:Form:form_div_layout.html.twig'
