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

use Doctrine\ODM\MongoDB\Event\PreUpdateEventArgs as ODMPreUpdateEventArgs;
use Doctrine\ODM\MongoDB\UnitOfWork as ODMUnitOfWork;
use Doctrine\ODM\MongoDB\DocumentManager;
use Doctrine\ODM\MongoDB\DocumentRepository;

use Doctrine\ORM\Event\PreUpdateEventArgs as ORMPreUpdateEventArgs;
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
            'postUpdate',
        ];
    }

    /**
     * @param LifecycleEventArgs $args
     */
    public function prePersist(LifecycleEventArgs $args)
    {
        $this->handleEvent($args);
    }

    /**
     * @param LifecycleEventArgs $args
     */
    public function postPersist(LifecycleEventArgs $args)
    {
        $this->handleEvent($args);
    }

    /**
     * @param LifecycleEventArgs $args
     */
    public function postUpdate(LifecycleEventArgs $args)
    {
        $this->handleEvent($args);
    }

    /**
     * @param LifecycleEventArgs $args
     */
    protected function handleEvent(LifecycleEventArgs $args)
    {
        $object = $args->getObject();
        $om = $args->getObjectManager();

        $classMetadata = $this->metadataFactory->getMetadataForClass(get_class($object));

        foreach ($classMetadata->propertyMetadata as $propertyMetadata) {

            if ($propertyMetadata instanceof PropertyMetadata && count($propertyMetadata->slugFields)) {

                if ($args instanceof ODMPreUpdateEventArgs || $args instanceof ORMPreUpdateEventArgs) {

                    if ($args->hasChangedField($propertyMetadata->name)) {
                        // generate custom slug
                        $slug = $this->slugger->slugify($args->getNewValue($propertyMetadata->name));

                    } else {
                        return; // no changes
                    }

                } else {
                    $slug = $propertyMetadata->getValue($object);
                }

                if (!trim($slug)) {
                    // generate slug from the sluggable fields
                    $slug = $this->generateSlugFromMetadata($propertyMetadata->slugFields, $object);
                }

                // generate unique slug
                $slug = $this->generateUniqueSlug($om, get_class($object), $propertyMetadata->name, $slug);

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
     * @param string                                      $class
     * @param string                                      $field
     * @param string                                      $slug
     *
     * @return string
     */
    protected function generateUniqueSlug(ObjectManager $om, $class, $field, $slug)
    {
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

            $positions = [];

            foreach ($objects as $object) {

                $value = $this->propertyAccessor->getValue($object, $field);
                $positions[preg_match($pattern, $value, $match) ? (int) $match[2] : 1] = true;
            }

            for ($i = 1; $i <= (max(array_keys($positions)) + 1); $i++) {

                if (!isset($positions[$i])) {
                    // first available slug
                    return $slug . ($i > 1 ? '-' . $i : '');
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
        $uow = $om->getUnitOfWork();

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
        $uow = $om->getUnitOfWork();

        if ($uow instanceof ODMUnitOfWork) {

            return $this->getRepository($om, $class)->findBy([
                $field => new \MongoRegex('/^' . preg_quote($slug, '/') . '(-\d+)?$/i') // counter is optional
            ]);

        } elseif ($uow instanceof ORMUnitOfWork) {
            throw new \RuntimeException('Not implemented yet'); // @todo
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
