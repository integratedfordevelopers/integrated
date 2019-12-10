<?php

namespace Integrated\Bundle\WebsiteBundle\Security;

use Integrated\Bundle\ContentBundle\Document\Channel\Channel;
use Integrated\Bundle\UserBundle\Model\User;
use Doctrine\ORM\EntityManagerInterface;
use Integrated\Common\Content\Channel\ChannelContextInterface;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Security\Core\Encoder\UserPasswordEncoderInterface;
use Symfony\Component\Validator\Constraints\Email;
use Symfony\Component\Validator\Validator\ValidatorInterface;

class UserManager
{
    const STATUS_USERNAME_NEW = 1;
    const STATUS_USERNAME_EXISTS = 2;
    const STATUS_USERNAME_INVALID = 3;

    /**
     * @var EntityManagerInterface
     */
    private $entityManager;

    /**
     * @var UserPasswordEncoderInterface
     */
    private $passwordEncoder;

    /**
     * @var ChannelContextInterface
     */
    private $channelContext;

    /**
     * @var ValidatorInterface
     */
    private $validator;

    /**
     * Authenticator constructor.
     *
     * @param EntityManagerInterface       $entityManager
     * @param UserPasswordEncoderInterface $passwordEncoder
     * @param ChannelContextInterface      $channelContext
     * @param ValidatorInterface           $validator
     */
    public function __construct(EntityManagerInterface $entityManager, UserPasswordEncoderInterface $passwordEncoder, ChannelContextInterface $channelContext, ValidatorInterface $validator)
    {
        $this->entityManager = $entityManager;
        $this->passwordEncoder = $passwordEncoder;
        $this->channelContext = $channelContext;
        $this->validator = $validator;
    }

    /**
     * @return bool
     */
    public function isEnabled()
    {
        if (!$channel = $this->channelContext->getChannel()) {
            return false;
        }

        if (!$channel instanceof Channel) {
            return false;
        }

        if ($channel->getScope() === null) {
            return false;
        }

        return true;
    }

    /**
     * @param Request $request
     *
     * @return bool
     */
    public function getUsernameStatus(?string $username)
    {
        $emailConstraint = new Email();

        if (!$this->isEnabled()) {
            return $this::STATUS_USERNAME_INVALID;
        }

        // use the validator to validate the value
        if (count($this->validator->validate($username, $emailConstraint)) > 0) {
            return $this::STATUS_USERNAME_INVALID;
        }

        $scope = $this->channelContext->getChannel()->getScope();

        $user = $this->entityManager->getRepository(User::class)->findOneBy(
            ['username' => $username, 'scope' => $scope->getId()]
        );

        if ($user === null) {
            return $this::STATUS_USERNAME_NEW;
        }

        return $this::STATUS_USERNAME_EXISTS;
    }
}
