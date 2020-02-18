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
    const RANK_FIRST_TAG = '-first-';
    const RANK_MIN_CHAR = 'A';
    const RANK_MEDIUM_CHAR = 'a';
    const RANK_MAX_CHAR = 'z';
    const ASCII_TABLE_POS_LOWER_A = 97;
    const ASCII_TABLE_POS_UPPER_A = 65;
    const ALPHABET_LENGTH = 26;

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

                $queryBuilder = $dm->createQueryBuilder(Content::class)
                    ->field('rank')->notEqual(null)
                    ->sort('rank', 1)
                    ->select('rank')
                    ->limit(1)
                    ->hydrate(false);

                if ($document->getRank() == self::RANK_FIRST_TAG) {
                    $min = self::RANK_MIN_CHAR;
                } else {
                    if (preg_match('/[^a-zA-Z]/', $document->getRank())) {
                        throw new \Exception('Rank can only contain [a-zA-Z]: '.$document->getRank());
                    }

                    $rankDocument = $dm->getRepository(Content::class)->findOneBy(['rank' => $document->getRank()]);
                    if ($rankDocument === null || $rankDocument->getId() == $document->getId()) {
                        continue;
                    }

                    $min = $document->getRank();
                    $queryBuilder->field('rank')->gt($document->getRank());
                }

                $max = $queryBuilder->getQuery()->getSingleResult();
                $max = $max['rank'] ?? self::RANK_MAX_CHAR;

                $document->setRank($this->calculateRank($min, $max));

                $class = $dm->getClassMetadata(\get_class($document));
                $uow->recomputeSingleDocumentChangeSet($class, $document);
            }
        }
    }

    /**
     * Calculate a new rank by calculating the middle between the min and max string.
     *
     * @param string $min
     * @param string $max
     *
     * @return string
     */
    private function calculateRank(string $min, string $max)
    {
        while (\strlen($min) < \strlen($max)) {
            $min = $min.self::RANK_MIN_CHAR;
        }

        while (\strlen($max) < \strlen($min)) {
            $max = $max.self::RANK_MAX_CHAR;
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
            $result .= self::RANK_MEDIUM_CHAR;
        }

        return $result;
    }

    /**
     * Get a numeric representation of an a-zA-Z character, starting with A.
     *
     * @param string $char
     *
     * @return int
     */
    private function charToNum(string $char)
    {
        $number = \ord($char);
        if ($number >= self::ASCII_TABLE_POS_LOWER_A) {
            $number = $number - (self::ASCII_TABLE_POS_LOWER_A - self::ALPHABET_LENGTH);
        } else {
            $number = $number - self::ASCII_TABLE_POS_UPPER_A;
        }

        return $number;
    }

    /**
     * Convert a numeric representation of an a-zA-Z character (starting with A) back to the character.
     *
     * @param int $number
     *
     * @return string
     */
    private function numToChar(int $number)
    {
        if ($number < self::ALPHABET_LENGTH) {
            $number = $number + self::ASCII_TABLE_POS_UPPER_A;
        } else {
            $number = $number + (self::ASCII_TABLE_POS_LOWER_A - self::ALPHABET_LENGTH);
        }

        return \chr($number);
    }
}
