<?php

/*
* This file is part of the Integrated package.
*
* (c) e-Active B.V. <integrated@e-active.nl>
*
* For the full copyright and license information, please view the LICENSE
* file that was distributed with this source code.
*/

namespace Integrated\Bundle\ContentHistoryBundle\Document;

use Doctrine\ODM\MongoDB\Mapping\Annotations as ODM;

use Integrated\Bundle\ContentHistoryBundle\Document\Embedded\User;
use Integrated\Common\Content\ContentInterface;

/**
 * @author Ger Jan van den Bosch <gerjan@e-active.nl>
 *
 * @ODM\Document(collection="content_history")
 */
class ContentHistory
{
    /**
     * @var string
     * @ODM\Id(strategy="UUID")
     */
    protected $id;

    /**
     * @var User
     * @ODM\EmbedOne(targetDocument="Integrated\Bundle\ContentHistoryBundle\Document\Embedded\User")
     */
    protected $user;

    /**
     * @var ContentInterface
     * @ODM\EmbedOne(targetDocument="Integrated\Bundle\ContentBundle\Document\Content\Content")
     */
    protected $snapshot;

    /**
     * @return string
     */
    public function getId()
    {
        return $this->id;
    }

    /**
     * @return User
     */
    public function getUser()
    {
        return $this->user;
    }

    /**
     * @param User $user
     * @return $this
     */
    public function setUser(User $user = null)
    {
        $this->user = $user;
        return $this;
    }

    /**
     * @return ContentInterface
     */
    public function getSnapshot()
    {
        return $this->snapshot;
    }

    /**
     * @param ContentInterface $snapshot
     * @return $this
     */
    public function setSnapshot(ContentInterface $snapshot)
    {
        $this->snapshot = $snapshot;
        return $this;
    }
}
