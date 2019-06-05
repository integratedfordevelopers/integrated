<?php

/*
 * This file is part of the Integrated package.
 *
 * (c) e-Active B.V. <integrated@e-active.nl>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Integrated\Bundle\PageBundle\Services;

use Doctrine\ODM\MongoDB\DocumentManager;
use Integrated\Bundle\PageBundle\Document\Page\ContentTypePage;
use Integrated\Bundle\PageBundle\Document\Page\Page;
use Integrated\Bundle\PageBundle\Form\Type\PageCopyPageType;

class PageCopyService
{
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
     * @param array $data
     */
    public function copyPages(string $channel, array $data)
    {
        $queryBuilder = $this->documentManager->createQueryBuilder(Page::class)
            ->field('channel.$id')->equals($channel);

        $result = $queryBuilder->getQuery()->execute();
        foreach ($result as $page) {

        }
    }
}
