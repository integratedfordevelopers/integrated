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

use Integrated\Bundle\ContentHistoryBundle\Document\Embedded\Request;
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
    protected $contentType;

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
     * @var Request | null
     * @ODM\EmbedOne(targetDocument="Integrated\Bundle\ContentHistoryBundle\Document\Embedded\Request")
     */
    protected $request;

    /**
     * @var User | null
     * @ODM\EmbedOne(targetDocument="Integrated\Bundle\ContentHistoryBundle\Document\Embedded\User")
     */
    protected $user;

    /**
     * @param string $contentId
     * @param string $contentType
     * @param string $action
     */
    public function __construct($contentId, $contentType, $action)
    {
        $this->contentId = $contentId;
        $this->contentType = $contentType;
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
     * @return string
     */
    public function getContentType()
    {
        return $this->contentType;
    }

    /**
     * @return string
     */
    public function getAction()
    {
        return $this->action;
    }

    /**
     * @return DateTime
     */
    public function getDate()
    {
        return $this->date;
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
     * @return Request | null
     */
    public function getRequest()
    {
        return $this->request;
    }

    /**
     * @param Request | null $request
     * @return $this
     */
    public function setRequest($request = null)
    {
        $this->request = $request;
        return $this;
    }

    /**
     * @return User | null
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
}
