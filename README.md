# IntegratedFormTypeBundle #
The IntegratedFormTypeBundle provides different Symfony Form Types for the Integrated project.

## Documentation ##
The documentation is stored in the `Resources/doc/index.md`.

[Read the Documentation](Resources/doc/index.md)

## Installation ##
The installation instructions can be found in the documentation.

## About ##
The IntegratedFormTypeBundle is part of the Intergrated project.

## Elements ##
 - integrated_sortable_collection
    - javascripts are loaded with integrated asset manager
    - the sortable items should have an order field with the following configuration

        $builder->add('order', 'hidden', [
            'attr' => [
                'data-itemorder' => 'collection',
            ],
        ]);

        or with annotations:

        * @Type\Field(type="hidden", options={"attr"={"data-itemorder"="collection"}})
