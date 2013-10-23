<?php

namespace Integrated\Bundle\ContentBundle\Form\Type;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Integrated\Bundle\ContentBundle\Form\DataTransformer\ContentTypeFieldCollection as Transformer;
use Integrated\Bundle\ContentBundle\Mapping\Metadata;

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
     * @return string
     */
    public function getName()
    {
        return 'content_type_field_collection';
    }
}