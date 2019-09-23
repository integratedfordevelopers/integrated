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
use Doctrine\ODM\MongoDB\Event\OnFlushEventArgs;
use Doctrine\ODM\MongoDB\UnitOfWork as ODMUnitOfWork;
use Doctrine\ORM\EntityManager;
use Doctrine\ORM\EntityRepository;
use Doctrine\ORM\UnitOfWork as ORMUnitOfWork;
use Integrated\Bundle\ContentBundle\Document\Content\Article;
use Integrated\Bundle\ContentBundle\Document\Content\Content;
use Integrated\Bundle\ContentBundle\Document\Content\Embedded\Relation;
use Integrated\Bundle\SlugBundle\Mapping\Metadata\PropertyMetadata;
use Integrated\Bundle\SlugBundle\Slugger\SluggerInterface;
use Integrated\Common\Content\RankableInterface;
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
            'onFlush'
        ];
    }

    /**
     * @param OnFlushEventArgs $args
     */
    public function onFlush(OnFlushEventArgs $args)
    {
        $dm = $args->getDocumentManager();
        $uow = $dm->getUnitOfWork();

        foreach (array_merge($uow->getScheduledDocumentInsertions(), $uow->getScheduledDocumentUpdates()) as $document) {
            if ($document instanceof RankableInterface) {
                if ($document->getRank() === null) {
                    continue;
                }

                $changeset = $uow->getDocumentChangeSet($document);
                if (!isset($changeset['rank'])) {
                    continue;
                }

                if (!is_array($changeset)) {
                    return null;
                }

                if ($document->getRank() == '-first-') {
                    $min = 'aaaaaaaaaa';
                    $max = $dm->createQueryBuilder(Content::class)
                        ->field('rank')->notEqual(null)
                        ->sort('rank', 1)
                        ->select('rank')
                        ->hydrate(false)
                        ->getQuery()
                        ->getSingleResult();
                    if ($max === null) {
                        $max = 'ZZZZZZZZZZ';
                    } else {
                        $max = $max['rank'];
                    }

                    $document->setRank($this->calculateRank($min, $max));
                } else {
                    $rankDocument = $dm->getRepository(Content::class)->findOneBy(['rank' => $document->getRank()]);
                    if ($rankDocument === null || $rankDocument->getId() == $document->getId()) {
                        continue;
                    }

                    $min = $dm->createQueryBuilder(Content::class)
                        ->field('rank')->notEqual(null)
                        ->field('rank')->lt($document->getRank())
                        ->sort('rank', -1)
                        ->select('rank')
                        ->hydrate(false)
                        ->getQuery()
                        ->getSingleResult();
                    if ($min == null) {
                        $min = 'aaaaaaaaaa';
                    } else {
                        $min = $min['rank'];
                    }
                    $max = $document->getRank();

                    $document->setRank($this->calculateRank($min, $max));
                }

                $class = $dm->getClassMetadata(\get_class($document));
                $uow->recomputeSingleDocumentChangeSet($class, $document);
            }
        }
    }

    /**
     * @param string $min
     * @param string $max
     * @param int $level
     */
    private function calculateRank(string $min, string $max, $level = 0) {
        $char1 = $min{$level};
        $char2 = $max{$level};

        if ($char1 == $char2) {
            return $this->calculateRank($min, $max, ($level+1));
        }

        if ($char1 === null) {
            //$char1 =
        }
        $num1 = $this->charToNum($char1);
        $num2 = $this->charToNum($char2);
        if (($num2 - $num1) < 2) {

        }
    }

    /**
     * @param string $char
     * @return int
     */
    private function charToNum(string $char)
    {
        $number = ord($char);
        if ($number >= 97) {
            $number = $number - 97;
        } else {
            $number = $number - 39;
        }

        return $number;
    }

    /**
     * @param int $number
     * @return string
     */
    private function numToChar(int $number)
    {
        if ($number <= 26) {
            $number = $number + 97;
        } else {
            $number = $number + 39;
        }

        return chr($number);
    }
}
