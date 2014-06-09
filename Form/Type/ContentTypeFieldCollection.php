<?php

/*
 * This file is part of the Integrated package.
 *
 * (c) e-Active B.V. <integrated@e-active.nl>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Integrated\Bundle\ContentBundle\Form\Type;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Integrated\Common\ContentType\Mapping\Metadata;
use Integrated\Bundle\ContentBundle\Form\DataTransformer\ContentTypeFieldCollection as Transformer;

/**
 * @author Jeroen van Leeuwen <jeroen@e-active.nl>
 */
class ContentTypeFieldCollection extends AbstractType
{
    /**
     * @var Metadata\ContentTypeField[]
     */
    protected $fields;

    /**
     * @param array $fields
     */
    public function __construct(array $fields)
    {
        $this->fields = $fields;
    }

    /**
     * {@inheritdoc}
     */
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        foreach ($this->fields as $field) {
            $builder->add(
                $field->getName(),
                new ContentTypeField($field),
                array(
                    'label' => $field->getLabel()
                )
            );
        }

        $transformer = new Transformer();
        $builder->addModelTransformer($transformer);
    }

    /**
     * {@inheritdoc}
     */
    public function getName()
    {
        return 'content_type_field_collection';
    }
}