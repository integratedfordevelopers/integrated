<?php

/*
 * This file is part of the Integrated package.
 *
 * (c) e-Active B.V. <integrated@e-active.nl>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Integrated\Bundle\ContentBundle\Bulk;

use Doctrine\Persistence\ManagerRegistry;
use Integrated\Bundle\ContentBundle\Document\Relation\Relation;
use Integrated\Bundle\ContentBundle\Form\Type\BulkActionRelationType;
use Integrated\Common\Bulk\Form\Config;
use Integrated\Common\Bulk\Form\ConfigProviderInterface;

/**
 * @author Jan Sanne Mulder <jansanne@e-active.nl>
 */
class RelationFormProvider implements ConfigProviderInterface
{
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
    public function getConfig(array $content)
    {
        $types = [];

        foreach ($content as $item) {
            $types[$item->getContentType()] = $item->getContentType();
        }

        $builder = $this->manager->getRepository(Relation::class)->createQueryBuilder('r');
        $builder->field('r.sources.$id')->in($types);

        $config = [];

        foreach ($builder->getQuery()->getIterator() as $relation) {
            $config[] = new Config(
                RelationAddHandler::class,
                sprintf('add_%s', $relation->getId()),
                BulkActionRelationType::class,
                [
                    'relation' => $relation,
                    'relation_handler' => RelationAddHandler::class,
                    'label' => sprintf('Add %s', $relation->getName()),
                ],
                new RelationFormActionMatcher(RelationAddHandler::class, $relation->getId())
            );

            $config[] = new Config(
                RelationRemoveHandler::class,
                sprintf('remove_%s', $relation->getId()),
                BulkActionRelationType::class,
                [
                    'relation' => $relation,
                    'relation_handler' => RelationRemoveHandler::class,
                    'label' => sprintf('Remove %s', $relation->getName()),
                ],
                new RelationFormActionMatcher(RelationRemoveHandler::class, $relation->getId())
            );
        }

        return $config;
    }
}
