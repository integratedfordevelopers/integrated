<?php

namespace Integrated\Bundle\ContentBundle\Form\Type;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Doctrine\Common\Persistence\ObjectRepository;
use Integrated\Common\ContentType\Mapping\Metadata;

class ContentType extends AbstractType
{
    /**
     * @var Metadata\ContentType
     */
    protected $contentType;

    /**
     * @var ObjectRepository
     */
    protected $repository;

    /**
     * @param Metadata\ContentType $contentType
     * @param ObjectRepository $repository
     */
    public function __construct(Metadata\ContentType $contentType, ObjectRepository $repository)
    {
        $this->contentType = $contentType;
        $this->repository = $repository;
    }

    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder->add(
            'class',
            'hidden'
        );

        $builder->add(
            'name',
            'text',
            array(
                'label' => 'Name',
            )
        );

        $builder->add(
            'fields',
            new ContentTypeFieldCollection($this->contentType->getFields())
        );

        $builder->add(
            'relations',
            new ContentTypeRelationCollection($this->repository)
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