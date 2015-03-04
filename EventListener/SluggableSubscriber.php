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

use Doctrine\Common\EventSubscriber;
use Doctrine\Common\Persistence\Event\LifecycleEventArgs;
use Doctrine\Common\Persistence\Mapping\MappingException;
use Doctrine\Common\Persistence\ObjectManager;

use Doctrine\ODM\MongoDB\Event\PreUpdateEventArgs as ODMPreUpdateEventArgs;
use Doctrine\ODM\MongoDB\UnitOfWork as ODMUnitOfWork;
use Doctrine\ODM\MongoDB\DocumentManager;

use Doctrine\ORM\Event\PreUpdateEventArgs as ORMPreUpdateEventArgs;
use Doctrine\ORM\UnitOfWork as ORMUnitOfWork;
use Doctrine\ORM\EntityManager;

use Metadata\MetadataFactoryInterface;
use Metadata\MergeableClassMetadata;

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
     * @param MetadataFactoryInterface $metadataFactory
     * @param SluggerInterface         $slugger
     */
    public function __construct(MetadataFactoryInterface $metadataFactory, SluggerInterface $slugger)
    {
        $this->metadataFactory = $metadataFactory;
        $this->slugger = $slugger;
    }

    /**
     * {@inheritdoc}
     */
    public function getSubscribedEvents()
    {
        return [
            'prePersist',
            'preUpdate',
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
    public function preUpdate(LifecycleEventArgs $args)
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

                // try to generate custom slug
                $slug = $this->generateSlugFromField($args, $propertyMetadata, $object);

                if (!trim($slug)) {
                    // generate slug from the sluggable fields
                    $slug = $this->generateSlugFromMetadata($classMetadata, $propertyMetadata->slugFields, $object);
                }

                // generate unique slug
                $slug = $this->generateSlugFromSimilar($om, get_class($object), $propertyMetadata->name, $slug);

                $propertyMetadata->setValue($object, $slug);
                $this->recomputeSingleObjectChangeSet($om, $object);
            }
        }
    }

    /**
     * @param LifecycleEventArgs|ODMPreUpdateEventArgs|ORMPreUpdateEventArgs $args
     * @param PropertyMetadata                                               $propertyMetadata
     * @param object                                                         $object
     *
     * @return string|null
     */
    protected function generateSlugFromField(LifecycleEventArgs $args, $propertyMetadata, $object)
    {
        if ($args instanceof ODMPreUpdateEventArgs || $args instanceof ORMPreUpdateEventArgs) {

            if ($args->hasChangedField($propertyMetadata->name)) {
                // generate slug value
                return $this->slugger->slugify($args->getNewValue($propertyMetadata->name));
            }

            return $propertyMetadata->getValue($object);
        }
    }

    /**
     * @param MergeableClassMetadata $classMetadata
     * @param array                  $fields
     * @param object                 $object
     *
     * @return string
     *
     * @throws MappingException
     */
    protected function generateSlugFromMetadata(MergeableClassMetadata $classMetadata, array $fields, $object)
    {
        $values = [];

        foreach ($fields as $field) {

            if (!isset($classMetadata->propertyMetadata[$field])) {
                throw new MappingException(sprintf('Field "%s" does not exist.', $field));
            }

            $metadata = $classMetadata->propertyMetadata[$field];

            if ($metadata instanceof \Metadata\PropertyMetadata) {
                // get sluggable value
                $values[] = $metadata->getValue($object);
            }
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
    protected function generateSlugFromSimilar(ObjectManager $om, $class, $field, $slug)
    {
        $uow = $om->getUnitOfWork();
        $accessor = PropertyAccess::createPropertyAccessor();

        $objects = [];

        if ($uow instanceof ODMUnitOfWork) {

            $objects = $om->getRepository($class)->findBy([
                $field => new \MongoRegex('/^' . preg_quote($slug, '/') . '(-\d+)?$/i')
            ]);

        } elseif ($uow instanceof ORMUnitOfWork) {

            throw new \RuntimeException('Not implemented yet'); // @todo
        }

        if (count($objects)) {

            $slugs = [];

            foreach ($objects as $object) {

                $value = $accessor->getValue($object, $field);
                $index = (int) str_replace($slug, 1, str_replace($slug . '-', '', $value));

                $slugs[$index] = $value;
            }

            for ($i = 1; $i <= (max(array_keys($slugs)) + 1); $i++) {

                if (!isset($slugs[$i])) {
                    // first available slug
                    return $slug . ($i > 1 ? '-' . $i : '');
                }
            }
        }

        return $slug;
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
