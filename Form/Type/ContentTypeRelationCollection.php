<?php

namespace Integrated\Bundle\ContentBundle\Form\Type;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Doctrine\Common\Persistence\ObjectRepository;
use Integrated\Common\ContentType\ContentTypeInterface;
use Integrated\Bundle\ContentBundle\Form\DataTransformer\ContentTypeRelationCollection as Transformer;
use Integrated\Common\ContentType\Mapping\Metadata;


class ContentTypeRelationCollection extends AbstractType
{
    /**
     * @var ObjectRepository
     */
    protected $repository;

    /**
     * @param ObjectRepository $repository
     */
    public function __construct(ObjectRepository $repository)
    {
        $this->repository = $repository;
    }

    /**
     * @param FormBuilderInterface $builder
     * @param array $options
     */
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        foreach ($this->repository->findAll() as $contentType) {
            if ($contentType instanceof ContentTypeInterface) {
                $builder->add(
                    // TODO: add getId to ContentTypeInterface
                    $contentType->getId(),
                    new ContentTypeRelation($contentType),
                    array(
                        'label' => $contentType->getType()
                    )
                );
            }
        }

        $transformer = new Transformer();
        $builder->addModelTransformer($transformer);
    }

    /**
     * @return string
     */
    public function getName()
    {
        return 'content_type_relation_collection';
    }
}