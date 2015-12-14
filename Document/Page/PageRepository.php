<?php

/*
 * This file is part of the Integrated package.
 *
 * (c) e-Active B.V. <integrated@e-active.nl>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Integrated\Bundle\PageBundle\Document\Page;

use Doctrine\ODM\MongoDB\DocumentRepository;
use Integrated\Bundle\ContentBundle\Document\Channel\Channel;

/**
 * @author Johan Liefers <johan@e-active.nl>
 */
class PageRepository extends DocumentRepository
{
    /**
     * @param Channel $channel
     * @param null $pageType
     * @return \Doctrine\ODM\MongoDB\Query\Builder
     */
    public function getPages(Channel $channel, $pageType = null)
    {
        $builder = $this->createQueryBuilder();
        $builder->field('channel.$id')->equals($channel->getId());

        return $builder;
    }
}