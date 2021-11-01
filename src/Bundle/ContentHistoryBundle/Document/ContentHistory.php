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

use DateTime;
use Integrated\Bundle\ContentHistoryBundle\Document\Embedded\Request;
use Integrated\Bundle\ContentHistoryBundle\Document\Embedded\User;
use Integrated\Common\Content\ContentInterface;

/**
 * @author Ger Jan van den Bosch <gerjan@e-active.nl>
 */
class ContentHistory
{
    /**
     * @var string
     */
    protected $id;

    /**
     * @var string
     */
    protected $contentId;

    /**
     * @var string
     */
    protected $contentType;

    /**
     * @var string
     */
    protected $contentClass;

    /**
     * @var string
     */
    protected $action;

    /**
     * @var DateTime
     */
    protected $date;

    /**
     * @var array
     */
    protected $changeSet = [];

    /**
     * @var Request|null
     */
    protected $request;

    /**
     * @var User|null
     */
    protected $user;

    /**
     * @param ContentInterface $content
     * @param string           $action
     */
    public function __construct(ContentInterface $content, $action)
    {
        $this->contentId = $content->getId();
        $this->contentType = $content->getContentType();
        $this->contentClass = \get_class($content);
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
    public function getContentClass()
    {
        return $this->contentClass;
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
     *
     * @return $this
     */
    public function setChangeSet(array $changeSet = [])
    {
        $this->changeSet = $changeSet;

        return $this;
    }

    /**
     * @return Request|null
     */
    public function getRequest()
    {
        return $this->request;
    }

    /**
     * @param Request|null $request
     *
     * @return $this
     */
    public function setRequest(Request $request = null)
    {
        $this->request = $request;

        return $this;
    }

    /**
     * @return User|null
     */
    public function getUser()
    {
        return $this->user;
    }

    /**
     * @param User|null $user
     *
     * @return $this
     */
    public function setUser(User $user = null)
    {
        $this->user = $user;

        return $this;
    }
}
