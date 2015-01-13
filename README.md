# IntegratedPageBundle

## Installation

composer.json

    "integrated/page-bundle": "dev-master",

AppKernel.php

    $bundles = array(
        ...
        new Integrated\Bundle\PageBundle\IntegratedPageBundle(),
        ...
    );

routing.yml

    integrated_page:
        resource: "@IntegratedPageBundle/Resources/config/routing.xml"
        prefix: "/admin"
