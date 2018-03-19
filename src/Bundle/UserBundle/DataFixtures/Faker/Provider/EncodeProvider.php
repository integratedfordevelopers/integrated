<?php

namespace Integrated\Bundle\UserBundle\DataFixtures\Faker\Provider;

use Integrated\Bundle\UserBundle\Model\User;
use Symfony\Component\Security\Core\Encoder\EncoderFactoryInterface;

class EncodeProvider
{
    /**
     * @var EncoderFactoryInterface
     */
    private $factory;

    /**
     * @param EncoderFactoryInterface $factory
     */
    public function __construct(EncoderFactoryInterface $factory)
    {
        $this->factory = $factory;
    }

    /**
     * @param User $user
     * @param string $password
     * @return string
     */
    public function encodePassword(User $user, $password)
    {
        $user->setSalt(base64_encode(random_bytes(72)));
        return $this->factory->getEncoder($user)->encodePassword($password, $user->getSalt());
    }
}