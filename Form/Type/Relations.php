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

use Doctrine\ODM\MongoDB\DocumentRepository;
use Doctrine\Bundle\MongoDBBundle\ManagerRegistry;
use Integrated\Common\Content\Form\RelationsTypeInterface;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Integrated\Common\ContentType\ContentTypeRelationInterface;
use Symfony\Component\OptionsResolver\OptionsResolverInterface;
use Integrated\Bundle\ContentBundle\Form\DataTransformer\Relations as DataTransformer;

/**
 * @author Jeroen van Leeuwen <jeroen@e-active.nl>
 */
class Relations extends AbstractType implements RelationsTypeInterface
{

    protected $relations;

    /**
     * @param ManagerRegistry $mr
     */
    public function __construct(ManagerRegistry $mr)
    {
        $this->mr = $mr;
    }

    /**
     * {@inheritdoc}
     */
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        foreach ($this->relations as $relation) {

            $contentTypeStr = '';
            foreach ($relation->getContentTypes() as $contentType) {
                $contentTypeStr .= $contentType->getType() . '&';
            }
            $contentTypeStr = substr($contentTypeStr, 0, -1);

            $builder->add(
                $relation->getId(),
                'hidden',
                array(
                    'attr' => array(
                        'data-title' => $relation->getName(),
                        'data-relation' => $relation->getId(),
                        'data-url' => $contentTypeStr,
                        'data-multiple' => $relation->getMultiple()
                    )
                )
            );
        }

//        $builder->addEventListener(
//            FormEvents::PRE_SET_DATA,
//            function (FormEvent $event) {
//                $form = $event->getForm();
//                $data = $event->getData();
//
//                var_dump($data);
//            }
//        );

        $transformer = new DataTransformer($this->relations, $this->mr->getManager());
        $builder->addModelTransformer($transformer);
    }

    public function setDefaultOptions(OptionsResolverInterface $resolver)
    {
        $resolver->setDefaults(array(
            'data_class' => null
        ));
    }

    public function getName()
    {
        return 'integrated_relations';
    }

    public function setRelations($relations)
    {
        $this->relations = $relations;
    }
}