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
use Doctrine\ODM\MongoDB\Event\OnFlushEventArgs;
use Integrated\Bundle\ContentBundle\Document\Content\Content;
use Integrated\Common\Content\RankableInterface;

class UpdateRankListener implements EventSubscriber
{
    public function __construct()
    {
    }

    /**
     * {@inheritdoc}
     */
    public function getSubscribedEvents()
    {
        return [
            'onFlush',
        ];
    }

    /**
     * @param OnFlushEventArgs $args
     *
     * @throws \Exception
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
                    $min = 'A';
                    $max = $dm->createQueryBuilder(Content::class)
                        ->field('rank')->notEqual(null)
                        ->sort('rank', 1)
                        ->select('rank')
                        ->limit(1)
                        ->hydrate(false)
                        ->getQuery()
                        ->getSingleResult();
                    if ($max === null) {
                        $max = 'z';
                    } else {
                        $max = $max['rank'];
                    }

                    $document->setRank($this->calculateRank($min, $max));
                } else {
                    if (preg_match('/[^a-zA-Z]/', $document->getRank())) {
                        throw new \Exception('Rank can only contain [a-zA-Z]: '.$document->getRank());
                    }

                    $rankDocument = $dm->getRepository(Content::class)->findOneBy(['rank' => $document->getRank()]);
                    if ($rankDocument === null || $rankDocument->getId() == $document->getId()) {
                        continue;
                    }

                    $min = $document->getRank();
                    $max = $dm->createQueryBuilder(Content::class)
                        ->field('rank')->notEqual(null)
                        ->field('rank')->gt($document->getRank())
                        ->sort('rank', 1)
                        ->select('rank')
                        ->limit(1)
                        ->hydrate(false)
                        ->getQuery()
                        ->getSingleResult();
                    if ($max == null) {
                        $max = 'z';
                    } else {
                        $max = $max['rank'];
                    }

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
        while (\strlen($min) < \strlen($max)) {
            $min = $min.'A';
        }

        while (\strlen($max) < \strlen($min)) {
            $max = $max.'z';
        }

        $result = '';
        for ($i = 0; $i < \strlen($min); ++$i) {
            $char1 = $min[$i];
            $char2 = $max[$i];

            $num1 = $this->charToNum($char1);
            $num2 = $this->charToNum($char2);

            $char = $this->numToChar(floor(($num1 + $num2) / 2));

            $result .= $char;
        }

        if ($result == $min) {
            $result .= 'a';
        }

        return $result;
    }

    /**
     * @param string $char
     *
     * @return int
     */
    private function charToNum(string $char)
    {
        $number = \ord($char);
        if ($number >= 97) {
            $number = $number - 71;
        } else {
            $number = $number - 65;
        }

        return $number;
    }

    /**
     * @param int $number
     *
     * @return string
     */
    private function numToChar(int $number)
    {
        if ($number < 26) {
            $number = $number + 65;
        } else {
            $number = $number + 71;
        }

        return \chr($number);
    }
}
