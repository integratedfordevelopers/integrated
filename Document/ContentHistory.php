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

use DateTime;

/**
 * @author Ger Jan van den Bosch <gerjan@e-active.nl>
 *
 * @ODM\Document(collection="content_history")
 * @ODM\Index(keys={"contentId"="asc", "date"="desc"})
 */
class ContentHistory
{
    const ACTION_INSERT = 'INSERT';
    const ACTION_UPDATE = 'UPDATE';
    const ACTION_DELETE = 'DELETE';

    /**
     * @var string
     * @ODM\Id(strategy="UUID")
     */
    protected $id;

    /**
     * @var string
     * @ODM\String
     */
    protected $contentId;

    /**
     * @var string
     * @ODM\String
     */
    protected $action;

    /**
     * @var DateTime
     * @ODM\Date
     */
    protected $date;

    /**
     * @var array
     * @ODM\Hash(nullable=true)
     */
    protected $changeSet = [];

    /**
     * @var User | null
     * @ODM\EmbedOne(targetDocument="Integrated\Bundle\ContentHistoryBundle\Document\Embedded\User")
     */
    protected $user;

    /**
     * @var ContentHistory | null
     * @ODM\ReferenceOne(targetDocument="Integrated\Bundle\ContentHistoryBundle\Document\ContentHistory")
     */
    protected $previous;

    /**
     * @param string $contentId
     * @param string $action
     */
    public function __construct($contentId, $action)
    {
        $this->contentId = $contentId;
        $this->action = $action;
        $this->date = new DateTime();
    }

    /**
     * @return string
     */
    public function getId()
    {
        return $this->id;
    }

    /**
     * @return string
     */
    public function getContentId()
    {
        return $this->contentId;
    }

    /**
     * @param string $contentId
     * @return $this
     */
    public function setContentId($contentId)
    {
        $this->contentId = $contentId;
        return $this;
    }

    /**
     * @return string
     */
    public function getAction()
    {
        return $this->action;
    }

    /**
     * @param string $action
     * @return $this
     */
    public function setAction($action)
    {
        $this->action = $action;
        return $this;
    }

    /**
     * @return DateTime
     */
    public function getDate()
    {
        return $this->date;
    }

    /**
     * @param DateTime $date
     * @return $this
     */
    public function setDate($date)
    {
        $this->date = $date;
        return $this;
    }

    /**
     * @return array
     */
    public function getChangeSet()
    {
        return $this->changeSet;
    }

    /**
     * @param array $changeSet
     * @return $this
     */
    public function setChangeSet(array $changeSet = [])
    {
        $this->changeSet = $changeSet;
        return $this;
    }

    /**
     * @return User
     */
    public function getUser()
    {
        return $this->user;
    }

    /**
     * @param User | null $user
     * @return $this
     */
    public function setUser(User $user = null)
    {
        $this->user = $user;
        return $this;
    }

    /**
     * @return ContentHistory
     */
    public function getPrevious()
    {
        return $this->previous;
    }

    /**
     * @param ContentHistory | null $previous
     * @return $this
     */
    public function setPrevious(ContentHistory $previous = null)
    {
        $this->previous = $previous;
        return $this;
    }
}
