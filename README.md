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
