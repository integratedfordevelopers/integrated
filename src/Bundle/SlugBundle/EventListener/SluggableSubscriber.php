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

use Doctrine\Common\EventSubscriber;
use Doctrine\Common\Persistence\Event\LifecycleEventArgs;
use Doctrine\Common\Persistence\ObjectManager;
use Doctrine\Common\Persistence\ObjectRepository;
use Doctrine\ODM\MongoDB\DocumentManager;
use Doctrine\ODM\MongoDB\DocumentRepository;
use Doctrine\ODM\MongoDB\UnitOfWork as ODMUnitOfWork;
use Doctrine\ORM\EntityManager;
use Doctrine\ORM\EntityRepository;
use Doctrine\ORM\UnitOfWork as ORMUnitOfWork;
use Integrated\Bundle\SlugBundle\Mapping\Metadata\PropertyMetadata;
use Integrated\Bundle\SlugBundle\Slugger\SluggerInterface;
use Metadata\MetadataFactoryInterface;
use Symfony\Component\PropertyAccess\PropertyAccess;
use Symfony\Component\PropertyAccess\PropertyAccessor;

/**
 * Doctrine ORM and ODM subscriber for slug generation.
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
            //'onFlush', // @todo implement to support update after a persist (INTEGRATED-294)
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
        $class = \get_class($object);

        $classMetadata = $this->metadataFactory->getMetadataForClass($class);
        $classMetadataInfo = $om->getClassMetadata($class);

        $identifierFields = $classMetadataInfo->getIdentifierFieldNames();

        foreach ($classMetadata->propertyMetadata as $propertyMetadata) {
            if ($propertyMetadata instanceof PropertyMetadata && \count($propertyMetadata->slugFields)) {
                $hasIdentifierFields = \count(array_intersect($identifierFields, $propertyMetadata->slugFields)) > 0;

                if ($event == 'prePersist' &&
                    $hasIdentifierFields ||
                    $event == 'postPersist' &&
                    !$hasIdentifierFields
                ) {
                    continue; // generate slug in another event
                }

                $slug = null;

                if ($event == 'preUpdate') {
                    if ($args->hasChangedField($propertyMetadata->name)) {
                        // generate custom slug
                        $slug = $this->slugger->slugify(
                            $args->getNewValue($propertyMetadata->name),
                            $propertyMetadata->slugSeparator
                        );
                    } elseif (null !== $propertyMetadata->getValue($object)) {
                        continue; // no changes
                    }
                } else {
                    // generate custom slug
                    $slug = $this->slugger->slugify(
                        $propertyMetadata->getValue($object),
                        $propertyMetadata->slugSeparator
                    );
                }

                if (!trim($slug)) {
                    // generate slug from the sluggable fields
                    $slug = $this->generateSlugFromMetadata(
                        $object,
                        $propertyMetadata->slugFields,
                        $propertyMetadata->slugSeparator
                    );
                }

                if ($propertyMetadata->slugLengthLimit) {
                    $slug = substr($slug, 0, $propertyMetadata->slugLengthLimit);
                }

                $id = $event == 'preUpdate' && method_exists($object, 'getId') ? $object->getId() : null;

                // generate unique slug
                $slug = $this->generateUniqueSlug(
                    $om,
                    $object,
                    $propertyMetadata->name,
                    $slug,
                    $propertyMetadata->slugSeparator,
                    $id,
                    $propertyMetadata->slugFields
                );

                $propertyMetadata->setValue($object, $slug);
                $this->recomputeSingleObjectChangeSet($om, $object);
            }
        }
    }

    /**
     * @param object $object
     * @param array  $fields
     * @param string $separator
     *
     * @return string
     */
    protected function generateSlugFromMetadata($object, array $fields, $separator = '-')
    {
        $values = [];

        foreach ($fields as $field) {
            $values[] = $this->propertyAccessor->getValue($object, $field);
        }

        // generate slug value
        return $this->slugger->slugify(implode(' ', $values), $separator);
    }

    /**
     * @param object $object
     * @param mixed  $value
     * @param array  $fields
     *
     * @return bool
     */
    protected function checkIfFieldValue($object, $value, $fields)
    {
        foreach ($fields as $field) {
            if ($value == $this->propertyAccessor->getValue($object, $field)) {
                return true;
            }
        }

        return false;
    }

    /**
     * @param ObjectManager|DocumentManager|EntityManager $om
     * @param object                                      $object
     * @param string                                      $field
     * @param string                                      $slug
     * @param string                                      $separator
     * @param string                                      $id
     * @param array                                       $slugFields
     *
     * @return string
     */
    protected function generateUniqueSlug(ObjectManager $om, $object, $field, $slug, $separator = '-', $id = null, $slugFields = [])
    {
        if (!trim($slug)) {
            return null;
        }

        $class = \get_class($object);

        if ($this->isUniqueSlug($om, $class, $field, $slug, $id)) {
            return $slug;
        }

        // slug with counter pattern
        $pattern = '/(.+)'.preg_quote($separator, '/').'(\d+)$/i';

        if (preg_match($pattern, $slug, $match)) {
            // Check if integer at the end of the slug matches any slug fields, if not, remove the int
            if (!$this->checkIfFieldValue($object, $match[2], $slugFields)) {
                // remove counter from slug
                $slug = $match[1];
            }
        }

        $objects = $this->findSimilarSlugs($om, $class, $field, $slug, $separator);

        if (\count($objects)) {
            $oid = spl_object_hash($object);
            $slugs = [];

            foreach ($objects as $object2) {
                if (property_exists($object2, $field) && $oid !== spl_object_hash($object2)) {
                    $value = $this->propertyAccessor->getValue($object2, $field);
                    $slugs[] = $value;
                }
            }

            if (!empty($slugs)) {
                for ($i = 1; $i <= (max(array_keys($slugs)) + 2); ++$i) {
                    $slug2 = $slug.($i > 1 ? $separator.$i : '');

                    if (!\in_array($slug2, $slugs)) {
                        return $slug2;
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
     * @param string                                      $id
     *
     * @return bool
     */
    protected function isUniqueSlug(ObjectManager $om, $class, $field, $slug, $id = null)
    {
        // check in document manager
        foreach ($this->getScheduledObjects($om) as $object) {
            if (property_exists($object, $field) && $slug === $this->propertyAccessor->getValue($object, $field)) {
                if (!(null !== $id && method_exists($object, 'getId') && $id == $object->getId())) {
                    return false;
                }
            }
        }

        $uow = $om->getUnitOfWork();

        // check in database
        if ($uow instanceof ODMUnitOfWork) {
            $builder = $this->getRepository($om, $class)->createQueryBuilder();
            $builder->field($field)->equals($slug);

            if (null !== $id) {
                // exclude current document
                $builder->field('id')->notEqual($id);
            }

            $query = $builder->count()->getQuery();

            return $query->execute() === 0;
        } elseif ($uow instanceof ORMUnitOfWork) {
            throw new \RuntimeException('Not implemented yet'); // @todo (INTEGRATED-294)
        }
    }

    /**
     * @param ObjectManager|DocumentManager|EntityManager $om
     * @param string                                      $class
     * @param string                                      $field
     * @param string                                      $slug
     * @param string                                      $separator
     *
     * @return array
     */
    protected function findSimilarSlugs(ObjectManager $om, $class, $field, $slug, $separator = '-')
    {
        $objects = $this->getScheduledObjects($om);
        $uow = $om->getUnitOfWork();

        if ($uow instanceof ODMUnitOfWork) {
            return array_merge($objects, $this->getRepository($om, $class)->findBy([
                $field => new \MongoRegex(
                    '/^'.preg_quote($slug, '/').'('.preg_quote($separator, '/').'\d+)?$/'
                ), // counter is optional
            ]));
        } elseif ($uow instanceof ORMUnitOfWork) {
            throw new \RuntimeException('Not implemented yet'); // @todo (INTEGRATED-294)
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
            $reflection = $classMetadata->getReflectionClass();

            $parents = [];

            // get parent class
            while ($parent = $reflection->getParentClass()) {
                $parents[] = $parent->getName();
                $reflection = $parent;
            }

            if (\count($parents)) {
                $class = end($parents);
            }

            return $om->getRepository($class);
        } elseif ($uow instanceof ORMUnitOfWork) {
            throw new \RuntimeException('Not implemented yet'); // @todo (INTEGRATED-294)
        }
    }

    /**
     * @param ObjectManager|DocumentManager|EntityManager $om
     * @param object                                      $object
     */
    protected function recomputeSingleObjectChangeSet(ObjectManager $om, $object)
    {
        if ($om->contains($object)) {
            $classMetadata = $om->getClassMetadata(\get_class($object));
            $uow = $om->getUnitOfWork();

            if ($uow instanceof ODMUnitOfWork) {
                $uow->recomputeSingleDocumentChangeSet($classMetadata, $object);
            } elseif ($uow instanceof ORMUnitOfWork) {
                $uow->recomputeSingleEntityChangeSet($classMetadata, $object);
            }
        }
    }
}
