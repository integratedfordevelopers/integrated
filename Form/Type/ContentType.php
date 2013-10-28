<?php

namespace Integrated\Bundle\ContentBundle\Form\Type;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Integrated\Common\ContentType\Mapping\Metadata;

class ContentType extends AbstractType
{
    /**
     * @var Metadata\ContentType
     */
    protected $contentType;

    /**
     * @param Metadata\ContentType $contentType
     */
    public function __construct(Metadata\ContentType $contentType)
    {
        $this->contentType = $contentType;
    }

    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder->add(
            'class',
            'hidden'
        );

        $builder->add(
            'type',
            'text',
            array(
                'label' => 'Name',
            )
        );

        $builder->add(
            'fields',
            new ContentTypeFieldCollection($this->contentType->getFields())
        );
    }


    /**
     * @return string
     */
    public function getName()
    {
        return 'content_type';
    }
}