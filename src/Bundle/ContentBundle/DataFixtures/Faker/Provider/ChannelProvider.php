<?php

/*
 * This file is part of the Integrated package.
 *
 * (c) e-Active B.V. <integrated@e-active.nl>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Integrated\Bundle\ContentBundle\DataFixtures\Faker\Provider;

use Doctrine\ODM\MongoDB\DocumentManager;
use Doctrine\ODM\MongoDB\DocumentNotFoundException;
use Integrated\Bundle\ContentBundle\Document\Channel\Channel;

class ChannelProvider
{
    /**
     * @var DocumentManager
     */
    private $dm;

    /**
     * @param DocumentManager $dm
     */
    public function __construct(DocumentManager $dm)
    {
        $this->dm = $dm;
    }

    /**
     * @param string $id
     * @return Channel
     * @throws DocumentNotFoundException
     */
    public function channel($id)
    {
        $channel = $this->dm->getRepository(Channel::class)->find($id);

        if (!$channel) {
            throw DocumentNotFoundException::documentNotFound(Channel::class, $id);
        }

        return $channel;
    }
}
