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
use Doctrine\Common\Persistence\Mapping\MappingException;
use Doctrine\Common\Persistence\ObjectManager;

use Doctrine\ODM\MongoDB\DocumentManager;
use Doctrine\ORM\EntityManager;

use Doctrine\ODM\MongoDB\UnitOfWork as ODMUnitOfWork;
use Doctrine\ORM\UnitOfWork as ORMUnitOfWork;

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
     *
     * @throws MappingException
     */
    protected function handleEvent(LifecycleEventArgs $args)
    {
        $object = $args->getObject();
        $om = $args->getObjectManager();

        $classMetadata = $this->metadataFactory->getMetadataForClass(get_class($object));

        foreach ($classMetadata->propertyMetadata as $propertyMetadata) {

            if ($propertyMetadata instanceof PropertyMetadata && count($propertyMetadata->slugFields)) {

                $values = [];

                foreach ($propertyMetadata->slugFields as $field) {

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
                $slug = $this->slugger->slugify(implode(' ', $values));

                $propertyMetadata->setValue($object, $slug);
                $this->recomputeSingleObjectChangeSet($om, $object);
            }
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

            } else if ($uow instanceof ORMUnitOfWork) {
                $uow->recomputeSingleEntityChangeSet($classMetadata, $object);
            }
        }
    }
}
