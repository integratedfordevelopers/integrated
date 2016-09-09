<?php

/*
 * This file is part of the Integrated package.
 *
 * (c) e-Active B.V. <integrated@e-active.nl>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Integrated\Bundle\ContentHistoryBundle\Doctrine\ODM\MongoDB\Logger;

use Doctrine\ODM\MongoDB\DocumentManager;

use Integrated\Common\Content\ContentInterface;

/**
 * @author Ger Jan van den Bosch <gerjan@e-active.nl>
 */
class ContentHistoryLogger
{
    /**
     * @var DocumentManager
     */
    protected $dm;

    /**
     * @param DocumentManager $dm
     */
    public function __construct(DocumentManager $dm)
    {
        $this->dm = $dm;
    }

    /**
     * @param ContentInterface $document
     * @param string $action
     */
    public static function createLog(ContentInterface $document, $action)
    {

    }
}
