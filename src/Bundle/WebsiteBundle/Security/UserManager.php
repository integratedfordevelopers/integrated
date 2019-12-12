<?php

namespace Integrated\Bundle\WebsiteBundle\Security;

use Integrated\Bundle\ContentBundle\Document\Channel\Channel;
use Integrated\Bundle\UserBundle\Model\User;
use Doctrine\ORM\EntityManagerInterface;
use Integrated\Common\Content\Channel\ChannelContextInterface;
use Symfony\Component\EventDispatcher\EventDispatcherInterface;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Security\Core\Authentication\Token\Storage\TokenStorageInterface;
use Symfony\Component\Security\Core\Authentication\Token\UsernamePasswordToken;
use Symfony\Component\Security\Core\Encoder\UserPasswordEncoderInterface;
use Symfony\Component\Security\Http\Event\InteractiveLoginEvent;
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
     * @var TokenStorageInterface
     */
    private $tokenStorage;

    /**
     * @var EventDispatcherInterface
     */
    private $eventDispatcher;

    /**
     * Authenticator constructor.
     *
     * @param EntityManagerInterface       $entityManager
     * @param UserPasswordEncoderInterface $passwordEncoder
     * @param ChannelContextInterface      $channelContext
     * @param ValidatorInterface           $validator
     * @param TokenStorageInterface        $tokenStorage
     * @param EventDispatcherInterface     $eventDispatcher
     */
    public function __construct(EntityManagerInterface $entityManager, UserPasswordEncoderInterface $passwordEncoder, ChannelContextInterface $channelContext, ValidatorInterface $validator, TokenStorageInterface $tokenStorage, EventDispatcherInterface $eventDispatcher)
    {
        $this->entityManager = $entityManager;
        $this->passwordEncoder = $passwordEncoder;
        $this->channelContext = $channelContext;
        $this->validator = $validator;
        $this->tokenStorage = $tokenStorage;
        $this->eventDispatcher = $eventDispatcher;
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
        if (\count($this->validator->validate($username, $emailConstraint)) > 0) {
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

    /**
     * @param string $username
     * @param string $password
     *
     * @throws \Exception
     */
    public function registerUser(string $username, string $password, $logUserIn = true)
    {
        if ($this->getUsernameStatus($username) !== $this::STATUS_USERNAME_NEW) {
            throw new \Exception('Invalid username for registration');
        }

        $user = new User();
        $user->setUsername($username);
        $user->setEmail($username);
        $user->setScope($this->channelContext->getChannel()->getScope());
        $user->setPassword($password);
        $user->setEnabled(true);

        $this->entityManager->persist($user);
        $this->entityManager->flush();

        if ($logUserIn) {
            $token = new UsernamePasswordToken($user->getUsername(), $user->getPassword(), "default", $user->getRoles());

            $this->tokenStorage->setToken($token);

            $event = new InteractiveLoginEvent(new Request(), $token);
            $this->eventDispatcher->dispatch('security.interactive_login', $event);
        }
    }
}
