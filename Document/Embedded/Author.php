<?php

namespace Integrated\Bundle\CommentBundle\Document\Embedded;

use Doctrine\ODM\MongoDB\Mapping\Annotations as ODM;

/**
 * Class Author
 * @ODM\Document
 */
class Author
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
    protected $userId;

    /**
     * @var string
     * @ODM\String
     */
    protected $userName;

    /**
     * Author constructor.
     * @param null $userId
     * @param null $userName
     */
    public function __construct($userId = null, $userName = null)
    {
        $this->setUserId($userId);
        $this->setUserName($userName);
    }

    /**
     * @return string
     */
    public function getUserId()
    {
        return $this->userId;
    }

    /**
     * @param string $userId
     */
    public function setUserId($userId)
    {
        $this->userId = $userId;
    }

    /**
     * @return string
     */
    public function getUserName()
    {
        return $this->userName;
    }

    /**
     * @param string $userName
     */
    public function setUserName($userName)
    {
        $this->userName = $userName;
    }
}
