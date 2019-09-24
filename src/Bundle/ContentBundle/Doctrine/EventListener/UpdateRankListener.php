<?php

/*
 * This file is part of the Integrated package.
 *
 * (c) e-Active B.V. <integrated@e-active.nl>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Integrated\Bundle\ContentBundle\Doctrine\EventListener;

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

class UpdateRankListener implements EventSubscriber
{
    /**
     */
    public function __construct()
    {
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

                if ($document->getRank() == '-first-') {
                    $min = 'a';
                    $max = $dm->createQueryBuilder(Content::class)
                        ->field('rank')->notEqual(null)
                        ->sort('rank', 1)
                        ->select('rank')
                        ->hydrate(false)
                        ->getQuery()
                        ->getSingleResult();
                    if ($max === null) {
                        $max = 'Z';
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
                        $min = 'a';
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
     *
     * @return string
     */
    private function calculateRank(string $min, string $max)
    {
        while (strlen($min) < strlen($max)) {
            $min = $min.'a';
        }

        while (strlen($max) < strlen($min)) {
            $max = $max.'Z';
        }

        $result = '';
        for ($i = 0; $i < strlen($min); $i++) {
            $char1 = $min{$i};
            $char2 = $max{$i};

            $num1 = $this->charToNum($char1);
            $num2 = $this->charToNum($char2);

            $char = $this->numToChar(floor(($num1 + $num2) / 2));

            $result .= $char;
        }

        if ($result == $min) {
            $result .= 'A';
        }

        return $result;
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
        if ($number < 26) {
            $number = $number + 97;
        } else {
            $number = $number + 39;
        }

        return chr($number);
    }
}
