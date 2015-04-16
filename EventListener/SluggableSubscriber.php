<?php

/*
 * This file is part of the Integrated package.
 *
 * (c) e-Active B.V. <integrated@e-active.nl>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Integrated\Bundle\SlugBundle\EventListener;

use Symfony\Component\PropertyAccess\PropertyAccess;
use Symfony\Component\PropertyAccess\PropertyAccessor;

use Doctrine\Common\EventSubscriber;
use Doctrine\Common\Persistence\Event\LifecycleEventArgs;
use Doctrine\Common\Persistence\ObjectManager;
use Doctrine\Common\Persistence\ObjectRepository;

use Doctrine\ODM\MongoDB\UnitOfWork as ODMUnitOfWork;
use Doctrine\ODM\MongoDB\DocumentManager;
use Doctrine\ODM\MongoDB\DocumentRepository;

use Doctrine\ORM\UnitOfWork as ORMUnitOfWork;
use Doctrine\ORM\EntityManager;
use Doctrine\ORM\EntityRepository;

use Metadata\MetadataFactoryInterface;

use Integrated\Bundle\SlugBundle\Mapping\Metadata\PropertyMetadata;
use Integrated\Bundle\SlugBundle\Slugger\SluggerInterface;

/**
 * Doctrine ORM and ODM subscriber for slug generation
 *
 * @author Ger Jan van den Bosch <gerjan@e-active.nl>
 */
class SluggableSubscriber implements EventSubscriber
{
    /**
     * @var MetadataFactoryInterface
     */
    private $metadataFactory;

    /**
     * @var SluggerInterface
     */
    private $slugger;

    /**
     * @var PropertyAccessor
     */
    private $propertyAccessor;

    /**
     * @param MetadataFactoryInterface $metadataFactory
     * @param SluggerInterface         $slugger
     */
    public function __construct(MetadataFactoryInterface $metadataFactory, SluggerInterface $slugger)
    {
        $this->metadataFactory = $metadataFactory;
        $this->slugger = $slugger;
        $this->propertyAccessor = PropertyAccess::createPropertyAccessor();
    }

    /**
     * {@inheritdoc}
     */
    public function getSubscribedEvents()
    {
        return [
            'prePersist',
            'postPersist',
            'preUpdate',
            //'onFlush', // @todo implement to support update after a persist
        ];
    }

    /**
     * @param LifecycleEventArgs $args
     */
    public function prePersist(LifecycleEventArgs $args)
    {
        // used for slug as id
        $this->handleEvent($args, 'prePersist');
    }

    /**
     * @param LifecycleEventArgs $args
     */
    public function postPersist(LifecycleEventArgs $args)
    {
        // used for id in slug
        $this->handleEvent($args, 'postPersist');
    }

    /**
     * @param LifecycleEventArgs $args
     */
    public function preUpdate(LifecycleEventArgs $args)
    {
        $this->handleEvent($args, 'preUpdate');
    }

    /**
     * @param LifecycleEventArgs $args
     * @param string             $event
     */
    protected function handleEvent(LifecycleEventArgs $args, $event)
    {
        $object = $args->getObject();
        $om = $args->getObjectManager();
        $class = get_class($object);

        $classMetadata = $this->metadataFactory->getMetadataForClass($class);
        $classMetadataInfo = $om->getClassMetadata($class);

        $identifierFields = $classMetadataInfo->getIdentifierFieldNames();

        foreach ($classMetadata->propertyMetadata as $propertyMetadata) {

            if ($propertyMetadata instanceof PropertyMetadata && count($propertyMetadata->slugFields)) {

                $hasIdentifierFields = count(array_intersect($identifierFields, $propertyMetadata->slugFields)) > 0;

                if ($event == 'prePersist' && $hasIdentifierFields || $event == 'postPersist' && !$hasIdentifierFields) {
                    continue; // generate slug in another event
                }

                if ($event == 'preUpdate') {

                    if ($args->hasChangedField($propertyMetadata->name)) {
                        // generate custom slug
                        $slug = $this->slugger->slugify($args->getNewValue($propertyMetadata->name));

                    } else {
                        continue; // no changes
                    }

                } else {
                    // generate custom slug
                    $slug = $this->slugger->slugify($propertyMetadata->getValue($object));
                }

                if (!trim($slug)) {
                    // generate slug from the sluggable fields
                    $slug = $this->generateSlugFromMetadata($propertyMetadata->slugFields, $object);
                }

                // generate unique slug
                $slug = $this->generateUniqueSlug($om, $object, $propertyMetadata->name, $slug);

                $propertyMetadata->setValue($object, $slug);
                $this->recomputeSingleObjectChangeSet($om, $object);
            }
        }
    }

    /**
     * @param array  $fields
     * @param object $object
     *
     * @return string
     */
    protected function generateSlugFromMetadata(array $fields, $object)
    {
        $values = [];

        foreach ($fields as $field) {
            $values[] = $this->propertyAccessor->getValue($object, $field);
        }

        // generate slug value
        return $this->slugger->slugify(implode(' ', $values));
    }

    /**
     * @param ObjectManager|DocumentManager|EntityManager $om
     * @param object                                      $object
     * @param string                                      $field
     * @param string                                      $slug
     *
     * @return string
     */
    protected function generateUniqueSlug(ObjectManager $om, $object, $field, $slug)
    {
        $class = get_class($object);

        if ($this->isUniqueSlug($om, $class, $field, $slug)) {
            return $slug;
        }

        // slug with counter pattern
        $pattern = '/(.+)-(\d+)$/i';

        if (preg_match($pattern, $slug, $match)) {
            // remove counter from slug
            $slug = $match[1];
        }

        $objects = $this->findSimilarSlugs($om, $class, $field, $slug);

        if (count($objects)) {

            $oid = spl_object_hash($object);
            $positions = [];

            foreach ($objects as $object2) {

                if (property_exists($object2, $field) && $oid !== spl_object_hash($object2)) {

                    $value = $this->propertyAccessor->getValue($object2, $field);
                    $positions[preg_match($pattern, $value, $match) ? (int) $match[2] : 1] = true;
                }
            }

            if (!empty($positions)) {

                for ($i = 1; $i <= (max(array_keys($positions)) + 1); $i++) {

                    if (!isset($positions[$i])) {
                        // first available slug
                        return $slug . ($i > 1 ? '-' . $i : '');
                    }
                }
            }
        }

        return $slug;
    }

    /**
     * @param ObjectManager|DocumentManager|EntityManager $om
     * @param string                                      $class
     * @param string                                      $field
     * @param string                                      $slug
     *
     * @return bool
     */
    protected function isUniqueSlug(ObjectManager $om, $class, $field, $slug)
    {
        // check in document manager
        foreach ($this->getScheduledObjects($om) as $object) {

            if (property_exists($object, $field) && $slug === $this->propertyAccessor->getValue($object, $field)) {
                return false;
            }
        }

        $uow = $om->getUnitOfWork();

        // check in database
        if ($uow instanceof ODMUnitOfWork) {

            $builder = $this->getRepository($om, $class)->createQueryBuilder();
            $builder->field($field)->equals(new \MongoRegex('/^' . preg_quote($slug, '/') . '$/i'));

            $query = $builder->count()->getQuery();

            return ($query->execute() === 0);

        } elseif ($uow instanceof ORMUnitOfWork) {
            throw new \RuntimeException('Not implemented yet'); // @todo
        }
    }

    /**
     * @param ObjectManager|DocumentManager|EntityManager $om
     * @param string                                      $class
     * @param string                                      $field
     * @param string                                      $slug
     *
     * @return array
     */
    protected function findSimilarSlugs(ObjectManager $om, $class, $field, $slug)
    {
        $objects = $this->getScheduledObjects($om);
        $uow = $om->getUnitOfWork();

        if ($uow instanceof ODMUnitOfWork) {

            return array_merge($objects, $this->getRepository($om, $class)->findBy([
                $field => new \MongoRegex('/^' . preg_quote($slug, '/') . '(-\d+)?$/i') // counter is optional
            ]));

        } elseif ($uow instanceof ORMUnitOfWork) {
            throw new \RuntimeException('Not implemented yet'); // @todo
        }
    }

    /**
     * @param ObjectManager $om
     *
     * @return array
     */
    protected function getScheduledObjects(ObjectManager $om)
    {
        $uow = $om->getUnitOfWork();

        if ($uow instanceof ODMUnitOfWork) {
            return array_merge($uow->getScheduledDocumentInsertions(), $uow->getScheduledDocumentUpdates());

        } elseif ($uow instanceof ORMUnitOfWork) {
            return array_merge($uow->getScheduledEntityInsertions(), $uow->getScheduledEntityUpdates());
        }
    }

    /**
     * @param ObjectManager|DocumentManager|EntityManager $om
     * @param string                                      $class
     *
     * @return ObjectRepository|DocumentRepository|EntityRepository
     */
    protected function getRepository(ObjectManager $om, $class)
    {
        $uow = $om->getUnitOfWork();

        if ($uow instanceof ODMUnitOfWork) {

            $classMetadata = $om->getClassMetadata($class);

            if (count($classMetadata->parentClasses)) {
                // get parent class
                $class = end($classMetadata->parentClasses);
            }

            return $om->getRepository($class);

        } elseif ($uow instanceof ORMUnitOfWork) {
            throw new \RuntimeException('Not implemented yet'); // @todo
        }
    }

    /**
     * @param ObjectManager|DocumentManager|EntityManager $om
     * @param object                                      $object
     */
    protected function recomputeSingleObjectChangeSet(ObjectManager $om, $object)
    {
        if ($om->contains($object)) {

            $classMetadata = $om->getClassMetadata(get_class($object));
            $uow = $om->getUnitOfWork();

            if ($uow instanceof ODMUnitOfWork) {
                $uow->recomputeSingleDocumentChangeSet($classMetadata, $object);

            } elseif ($uow instanceof ORMUnitOfWork) {
                $uow->recomputeSingleEntityChangeSet($classMetadata, $object);
            }
        }
    }
}
