<?php

/*
 * This file is part of the Integrated package.
 *
 * (c) e-Active B.V. <integrated@e-active.nl>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Integrated\Common\Content\Form;

use Doctrine\ODM\MongoDB\DocumentRepository;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Integrated\Common\ContentType\ContentTypeRelationInterface;


/**
 * @author Jeroen van Leeuwen <jeroen@e-active.nl>
 */
class RelationType extends AbstractType
{
    /**
     * @var ContentTypeRelationInterface[]
     */
    protected $relations;

    /**
     * @param ContentTypeRelationInterface[] $relations
     */
    public function __construct($relations)
    {
        $this->relations = $relations;
    }

    /**
     * {@inheritdoc}
     */
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        foreach ($this->relations as $relation) {

            $builder->add(
                $relation->getId(),
                'document',
                array(
                    'class' => 'Integrated\Bundle\ContentBundle\Document\Content\Content',
                    'label' => $relation->getName(),
                    'multiple' => $relation->getMultiple(),
                    'expanded' => $relation->getMultiple(),
                    'empty_value' => 'Choose an option',
                    'query_builder' => function(DocumentRepository $dr) use($relation) {

                        $contentTypes = array();
                        foreach ($relation->getContentTypes() as $contentType) {
                            $contentTypes[] = $contentType->getClass();
                        }

                        return $dr->createQueryBuilder('c')->field('class')->in($contentTypes);
                    }
                )
            );

        }

        $transformer = new DataTransformer\Relation($this->relations);
        $builder->addModelTransformer($transformer);
    }

    public function getName()
    {
        return 'relations';
    }
}