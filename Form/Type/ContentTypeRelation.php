<?php

namespace Integrated\Bundle\ContentBundle\Form\Type;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Integrated\Common\ContentType\ContentTypeInterface;
use Integrated\Bundle\ContentBundle\Form\DataTransformer\ContentTypeRelation as Transformer;

class ContentTypeRelation extends AbstractType
{
    /**
     * @var ContentTypeInterface
     */
    protected $contentType;

    /**
     * @param ContentTypeInterface $contentType
     */
    public function __construct(ContentTypeInterface $contentType)
    {
        $this->contentType = $contentType;
    }

    public function buildForm(FormBuilderInterface $builder, array $options)
    {


        $builder->add(
            'enabled',
            'checkbox',
            array(
                'required' => false,
                'label' => $this->contentType->getName()
            )
        );

        $builder->add(
            'multiple',
            'choice',
            array(
                'choices' => array(
                    0 => 'One',
                    1 => 'Multiple'
                ),
                'expanded' => true,
                'required' => false
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

        $transformer = new Transformer($this->contentType);
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