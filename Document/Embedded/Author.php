<?php

namespace Integrated\Bundle\CommentBundle\Document\Embedded;

use Doctrine\ODM\MongoDB\Mapping\Annotations as ODM;
use Integrated\Bundle\ContentBundle\Document\Content\Relation\Person;
use Integrated\Bundle\UserBundle\Model\User;

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
     * @var Person
     * @ODM\ReferenceOne(targetDocument="Integrated\Bundle\ContentBundle\Document\Content\Relation\Person")
     */
    protected $person;

    /**
     * Author constructor.
     * @param User $user
     */
    public function __construct(User $user)
    {
        $this->setUserId($user->getId());
        $this->setUserName($user->getUsername());
        $this->setPerson($user->getRelation());
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

    /**
     * @return string
     */
    public function getId()
    {
        return $this->id;
    }

    /**
     * @param string $id
     */
    public function setId($id)
    {
        $this->id = $id;
    }

    /**
     * @return Person
     */
    public function getPerson()
    {
        return $this->person;
    }

    /**
     * @param Person $person
     */
    public function setPerson($person)
    {
        $this->person = $person;
    }
}
