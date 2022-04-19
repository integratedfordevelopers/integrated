<?php

/*
 * This file is part of the Integrated package.
 *
 * (c) e-Active B.V. <integrated@e-active.nl>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Integrated\Bundle\ContentBundle\Doctrine\ODM\Migration;

use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\MongoDB\Collection;
use Integrated\Bundle\ContentBundle\Document\Content\Content;
use Integrated\Bundle\ContentBundle\Document\Relation\Relation;

/**
 * @author Johan Liefers <johan@-eactive.nl>
 */
trait RelationHelper
{
    /**
     * @param string           $name
     * @param string           $id
     * @param array|Collection $sources
     * @param array|Collection $targets
     * @param bool             $multiple
     * @param bool             $required
     * @param null             $type     if null $id is used as type
     *
     * @return Relation
     *
     * @throws \Exception
     */
    public function addRelation($name, $id, $sources, $targets, $multiple = true, $required = false, $type = null)
    {
        $relation = $this->getDocumentManager()->getRepository(Relation::class)->find($id);

        if (!$relation) {
            $relation = new Relation();

            if (\is_array($sources)) {
                $sources = new ArrayCollection($sources);
            } elseif (!$sources instanceof Collection) {
                throw new \Exception('sources of relation should be either array or instance of Collection');
            }

            if (\is_array($targets)) {
                $targets = new ArrayCollection($targets);
            } elseif (!$targets instanceof Collection) {
                throw new \Exception('sources of relation should be either array or instance of Collection');
            }

            $relation->setSources($sources)
                ->setName($name)
                ->setTargets($targets)
                ->setType($type ?: $id)
                ->setId($id)
                ->setMultiple($multiple)
                ->setRequired($required);

            $this->getDocumentManager()->persist($relation);
            $this->getDocumentManager()->flush();

            $this->write(sprintf('Added relation with id "%s".', $id));
        }

        return $relation;
    }

    /**
     * @param string $id
     */
    protected function removeRelation($id)
    {
        $dm = $this->getDocumentManager();
        $relation = $dm->getRepository(Relation::class)->find($id);

        if ($relation) {
            $dm->remove($relation);
            $dm->flush();

            $this->write(sprintf('Removed relation with id "%s".', $id));
        }
    }

    /**
     * @param string $type
     * @param bool   $removeReferences
     */
    protected function removeRelationByType($type, $removeReferences = false)
    {
        $dm = $this->getDocumentManager();
        $relation = $dm->getRepository(Relation::class)->findOneBy(['type' => $type]);

        if ($relation) {
            $dm->remove($relation);
            $dm->flush();

            $this->write(sprintf('Removed relation with type "%s".', $type));
        }

        if ($removeReferences) {
            $builder = $dm->createQueryBuilder(Content::class)->hydrate(false);
            $builder->field('relations.relationType')->equals($type);

            foreach ($builder->getQuery()->execute() as $content) {
                foreach ($content['relations'] as $i => $value) {
                    if ($value['relationType'] == $type) {
                        unset($content['relations'][$i]);
                    }
                }

                // reset array keys
                $content['relations'] = array_values($content['relations']);

                $dm->getDocumentCollection(Content::class)->update(['_id' => $content['_id']], $content);
            }
        }
    }

    /**
     * @param string $message
     */
    abstract protected function write($message);

    /**
     * @return \Doctrine\ODM\MongoDB\DocumentManager
     */
    abstract protected function getDocumentManager();

    /**
     * @return \Symfony\Component\DependencyInjection\ContainerInterface
     */
    abstract public function getContainer();
}
