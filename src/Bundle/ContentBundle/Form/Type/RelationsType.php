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

use Doctrine\Bundle\MongoDBBundle\ManagerRegistry;
use Integrated\Bundle\ContentBundle\Document\Relation\Relation;
use Integrated\Bundle\ContentBundle\Form\DataTransformer\Relations as RelationsTransformer;
use Integrated\Bundle\ContentBundle\Validator\Constraints\RelationNotNull;
use Integrated\Common\ContentType\ContentTypeInterface;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\HiddenType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

/**
 * @author Jeroen van Leeuwen <jeroen@e-active.nl>
 */
class RelationsType extends AbstractType
{
    /**
     * @var string
     */
    const REPOSITORY = 'Integrated\\Bundle\\ContentBundle\\Document\\Relation\\Relation';

    /**
     * @var ManagerRegistry
     */
    private $manager;

    /**
     * @param ManagerRegistry $manager
     */
    public function __construct(ManagerRegistry $manager)
    {
        $this->manager = $manager;
    }

    /**
     * {@inheritdoc}
     */
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        /** @var ContentTypeInterface $type */
        $type = $options['content_type'];

        /** @var Relation[] $relations */
        $relations = $this->manager->getRepository(self::REPOSITORY)->findBy(['sources.$id' => $type->getId()], ['name' => 'ASC']);

        foreach ($relations as $relation) {
            $contentTypes = [];

            foreach ($relation->getTargets() as $contentType) {
                $contentTypes[] = [
                    'type' => $contentType->getType(),
                    'name' => $contentType->getName(),
                ];
            }

            $constraints = [];
            if ($relation->isRequired()) {
                $constraints[] = new RelationNotNull([
                    'relation' => $relation->getName(),
                ]);
            }

            $builder->add($relation->getId(), HiddenType::class, ['attr' => [
                'data-title' => $relation->getName(),
                'data-relation' => $relation->getId(),
                'data-multiple' => $relation->isMultiple(),
                'data-types' => json_encode($contentTypes),
                ],
                'constraints' => $constraints,
            ]);
        }

        $builder->addModelTransformer(new RelationsTransformer($relations, $this->manager->getManager()));
    }

    /**
     * {@inheritdoc}
     */
    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults(['data_class' => null]);
        $resolver->setRequired(['content_type']);
        $resolver->setAllowedTypes('content_type', 'Integrated\\Common\\ContentType\\ContentTypeInterface');
    }

    /**
     * {@inheritdoc}
     */
    public function getBlockPrefix()
    {
        return 'integrated_content_relations';
    }
}
