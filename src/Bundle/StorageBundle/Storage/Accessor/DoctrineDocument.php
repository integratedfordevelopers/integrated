<?php

/*
 * This file is part of the Integrated package.
 *
 * (c) e-Active B.V. <integrated@e-active.nl>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Integrated\Bundle\StorageBundle\Storage\Accessor;

use Doctrine\Common\Util\ClassUtils;

/**
 * @author Johnny Borg <johnny@e-active.nl>
 */
class DoctrineDocument
{
    /**
     * @const string
     */
    const GET_SIGNATURE = 'get%s';

    /**
     * @const string
     */
    const SET_SIGNATURE = 'set%s';

    /**
     * @var object
     */
    protected $document;

    /**
     * @var int
     */
    private $updates = 0;

    /**
     * @param object $document
     */
    public function __construct($document)
    {
        if (\is_object($document)) {
            $this->document = $document;
        } else {
            // We can call methods, so it seems like we've been given something rather unpleasant
            throw new \InvalidArgumentException(
                sprintf('Object of type %s is not a object', \gettype($document))
            );
        }
    }

    /**
     * @return object
     */
    public function getDocument()
    {
        return $this->document;
    }

    /**
     * @return string
     */
    public function getClassName()
    {
        return ClassUtils::getRealClass(\get_class($this->document));
    }

    /**
     * @return bool
     */
    public function hasUpdates()
    {
        return 0 !== $this->updates;
    }

    /**
     * @param string $propertyName
     *
     * @return mixed
     */
    public function get($propertyName)
    {
        $method = sprintf(self::GET_SIGNATURE, ucfirst($propertyName));
        if (method_exists($this->document, $method)) {
            return \call_user_func([$this->document, $method]);
        }

        // Well that did not go as planned
        throw new \LogicException(
            sprintf(
                'Required method %s does not exist on class %s.',
                $method,
                \get_class($this->document)
            )
        );
    }

    /**
     * @param string $propertyName
     * @param object $propertyValue
     *
     * @return mixed
     */
    public function set($propertyName, $propertyValue)
    {
        $method = sprintf(self::SET_SIGNATURE, ucfirst($propertyName));
        if (method_exists($this->document, $method)) {
            // This keeps track of the times something updated, not changed
            ++$this->updates;

            return \call_user_func([$this->document, $method], $propertyValue);
        }

        // We need something to set it, seems like we can't
        throw new \LogicException(
            sprintf(
                'Required method %s does not exist on class %s.',
                $method,
                \get_class($this->document)
            )
        );
    }
}
