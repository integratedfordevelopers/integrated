# IntegratedBlockBundle

## Installation

composer.json

    "integrated/block-bundle": "dev-master",

AppKernel.php

    $bundles = array(
        ...
        new Integrated\Bundle\BlockBundle\IntegratedBlockBundle(),
        ...
    );

routing.yml

    integrated_block:
        resource: "@IntegratedBlockBundle/Resources/config/routing.xml"
        prefix: "/admin"
