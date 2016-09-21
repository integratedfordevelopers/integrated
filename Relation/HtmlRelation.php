<?php

/*
 * This file is part of the Integrated package.
 *
 * (c) e-Active B.V. <integrated@e-active.nl>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Integrated\Bundle\ContentBundle\Relation;

use Integrated\Bundle\ContentBundle\Document\Content\Content;

/**
 * @author Johnny Borg <johnny@e-active.nl>
 */
class HtmlRelation
{
    /**
     * @param $html
     * @return Content[]
     */
    public function read($html)
    {
        $document = new \DOMDocument();
        $document->loadHTML($html);

        $xpath = new \DOMXPath($document);
        foreach ($xpath->query('//img[@data-integrated-id]') as $elm) {
            yield $elm->getAttribute('data-integrated-id');
        }
    }
}
