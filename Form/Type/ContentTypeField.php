<?php

namespace Integrated\Bundle\ContentBundle\Form\Type;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Integrated\Bundle\ContentBundle\Form\DataTransformer\ContentTypeField as Transformer;
use Integrated\Bundle\ContentBundle\Mapping\Metadata;

class ContentTypeField extends AbstractType
{
    /**
     * @var Metadata\ContentTypeField
     */
    protected $contentTypeField;

    /**
     * @param Metadata\ContentTypeField $contentTypeField
     */
    public function __construct(Metadata\ContentTypeField $contentTypeField)
    {
        $this->contentTypeField = $contentTypeField;
    }

    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder->add(
            'enabled',
            'checkbox',
            array(
                'required' => false,
                'label' => $this->contentTypeField->getLabel()
            )
        );

        $builder->add(
            'required',
            'checkbox',
            array(
                'required' => false
                //'label' => 'Required'
            )
        );

        $transformer = new Transformer($this->contentTypeField);
        $builder->addModelTransformer($transformer);
    }


    /**
     * @return string
     */
    public function getName()
    {
        return 'content_type_field';
    }
}