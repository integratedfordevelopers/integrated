<?php

/*
* This file is part of the Integrated package.
*
* (c) e-Active B.V. <integrated@e-active.nl>
*
* For the full copyright and license information, please view the LICENSE
* file that was distributed with this source code.
*/

namespace Integrated\Common\Form\Type;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;

use Integrated\Common\Form\Mapping\MetadataFactoryInterface;

/**
 * @author Ger Jan van den Bosch <gerjan@e-active.nl>
 */
class MetadataType extends AbstractType
{
    /**
     * @var MetadataFactoryInterface
     */
    private $factory;

    /**
     * @var string
     */
    private $name;

    /**
     * @param MetadataFactoryInterface $factory
     * @param string $name
     */
    public function __construct(MetadataFactoryInterface $factory, $name)
    {
        $this->factory = $factory;
        $this->name = $name;
    }

    /**
     * @inheritdoc
     */
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $metadata = $this->factory->getMetadata($options['data_class']); // @todo: auto-resolve class

        foreach ($metadata->getFields() as $field) {

            $builder->add($field->getName(), $field->getType(), $field->getOptions());
        }
    }

    /**
     * @inheritdoc
     */
    public function getName()
    {
        return $this->name;
    }
}