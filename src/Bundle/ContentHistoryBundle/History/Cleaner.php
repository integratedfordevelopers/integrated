<?php

/*
 * This file is part of the Integrated package.
 *
 * (c) e-Active B.V. <integrated@e-active.nl>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Integrated\Bundle\ContentHistoryBundle\History;

use Doctrine\ODM\MongoDB\DocumentManager;
use Integrated\Bundle\ContentHistoryBundle\Diff\ArrayComparer;
use Integrated\Bundle\ContentHistoryBundle\Document\ContentHistory;

class Cleaner
{
    /**
     * @var array
     */
    public $cleanTable = [];

    /**
     * @var DocumentManager
     */
    private $documentManager;

    /**
     * @param DocumentManager $documentManager
     */
    public function __construct(DocumentManager $documentManager)
    {
        $this->documentManager = $documentManager;
    }

    /**
     * @return array
     */
    public function clean(array $document)
    {
        $originalDocument = $document;

        $changed = false;
        if ($document['action'] == 'update') {
            foreach ($document['changeSet'] as $key => $change) {
                $diff = ArrayComparer::diff(($change[0] ?? []), ($change[1] ?? []));
                if (\count($diff) == 0) {
                    unset($document['changeSet'][$key]);
                    $changed = true;
                } elseif (\is_array($change[0])) {
                    $document['changeSet'][$key] = $diff;
                }

                if ($key == 'address') {
                    //exit;
                }
                if (\count($diff) > 0) {
                    //exit;
                }
            }
        }

        if (isset($this->cleanTable[$document['contentClass']])) {
            foreach ($this->cleanTable[$document['contentClass']] as $key) {
                if (!isset($document['changeSet'][$key])) {
                    continue;
                }

                unset($document['changeSet'][$key]);
                $changed = true;
            }
        }

        if (\count($document['changeSet']) === 0) {
            var_dump('remove');
            var_dump($document['_id']);
            echo "\n";

            exit;
            $this->documentManager->createQueryBuilder(ContentHistory::class)
                ->remove()
                ->field('_id')->equals($document['_id'])
                ->getQuery()
                ->execute();
            exit;
        } elseif ($changed) {
            var_dump('change');
            var_dump($document['_id']);
            var_dump($document['changeSet']);
            echo "\n";

            $this->documentManager->createQueryBuilder(ContentHistory::class)
                ->updateOne()
                ->field('changeSet')->set($document['changeSet'])
                ->field('_id')->equals($document['_id'])
                ->getQuery()
                ->execute();
            exit;
        }
    }
}
