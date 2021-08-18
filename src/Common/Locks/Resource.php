<?php

/*
 * This file is part of the Integrated package.
 *
 * (c) e-Active B.V. <integrated@e-active.nl>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Integrated\Common\Locks;

use Integrated\Common\Locks\Exception\InvalidArgumentException;
use Integrated\Common\Locks\Exception\InvalidObjectException;
use Symfony\Component\Security\Acl\Util\ClassUtils;
use Symfony\Component\Security\Core\Authentication\Token\TokenInterface;
use Symfony\Component\Security\Core\User\UserInterface;

/**
 * @author Jan Sanne Mulder <jansanne@e-active.nl>
 */
class Resource implements ResourceInterface
{
    /**
     * @var string
     */
    protected $type;

    /**
     * @var string|null
     */
    protected $identifier = null;

    /**
     * @param string      $type
     * @param string|null $identifier
     */
    public function __construct($type, $identifier = null)
    {
        if (empty($type)) {
            throw new InvalidArgumentException('$type cannot be empty.');
        }

        $this->type = (string) $type;
        $this->identifier = $identifier === null ? null : (string) $identifier;
    }

    /**
     * Construct a Resource for the given domain object.
     *
     * @param object $object
     *
     * @return ResourceInterface
     *
     * @throws InvalidObjectException
     */
    public static function fromObject($object)
    {
        if (!\is_object($object)) {
            throw new InvalidObjectException('$object must be a object');
        }

        try {
            if ($object instanceof ResourceIdentifierInterface) {
                return new self(ClassUtils::getRealClass($object), $object->getIdentifier());
            } elseif (method_exists($object, 'getId')) {
                return new self(ClassUtils::getRealClass($object), $object->getId());
            }
        } catch (\InvalidArgumentException $e) {
            throw new InvalidObjectException($e->getMessage(), 0, $e);
        }

        throw new InvalidObjectException('$object must either implement the ResourceIdentifierInterface, or have a method named "getId".');
    }

    public static function fromAccount(UserInterface $user)
    {
        return new self(ClassUtils::getRealClass($user), $user->getUsername());
    }

    public static function fromToken(TokenInterface $token)
    {
        $user = $token->getUser();

        if ($user instanceof UserInterface) {
            return self::fromAccount($user);
        }

        return new self(\is_object($user) ? ClassUtils::getRealClass($user) : ClassUtils::getRealClass($token), (string) $user);
    }

    /**
     * {@inheritdoc}
     */
    public function getType()
    {
        return $this->type;
    }

    /**
     * {@inheritdoc}
     */
    public function getIdentifier()
    {
        return $this->identifier;
    }

    /**
     * {@inheritdoc}
     */
    public function equals(ResourceInterface $resource)
    {
        return $this->type === $resource->getType() && $this->identifier === $resource->getIdentifier();
    }

    /**
     * Get the string representation of the resource.
     *
     * @return string
     */
    public function __toString()
    {
        return sprintf(
            'Resource(%s, %s)',
            $this->type,
            $this->identifier === null ? 'NULL' : $this->identifier
        );
    }
}
