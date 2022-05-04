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

class Parser
{
    public function getReadableChangeset(ContentHistory $history): array
    {
        $table = [];
        dump($history->getChangeSet());
        foreach ($history->getChangeSet() as $key => $data) {
            $data = $this->normalizeValue($data);

            if (!is_array($data)) {
                $table[] = [
                    'name' => $key,
                    'old' => '',
                    'new' => $data,
                ];

                continue;
            }

            $this->walkArray($table, $key, $data);
        }

        dump($table);

        return $table;
    }

    private function walkArray(array &$table, string $path, array $data)
    {
        if (count($data) === 2 && array_key_exists(0, $data) && array_key_exists(1, $data) && !is_array($data[0]) && !is_array($data[1])) {
            $table[] = [
                'name' => $path,
                'old' => $this->normalizeValue($data[0]),
                'new' => $this->normalizeValue($data[1]),
            ];

            return;
        }

        foreach ($data as $key => $subdata) {
            $subdata = $this->normalizeValue($subdata);
            if (!is_array($subdata)) {
                $table[] = [
                    'name' => $path.' > '.$key,
                    'old' => '',
                    'new' => $subdata,
                ];

                continue;
            }

            $this->walkArray($table, $path.' > '.$key, $subdata);
        }
    }

    private function normalizeValue($value)
    {
        if ($value instanceof \DateTimeInterface) {
            return $value->format('r');
        } elseif ($value instanceof \MongoDate) {
            dump($value->toDateTime());
            return $value->toDateTime()->format('r');
        }

        if (\is_object($value)) {
            return serialize((array) $value);
        }

        return $value;
    }
}
