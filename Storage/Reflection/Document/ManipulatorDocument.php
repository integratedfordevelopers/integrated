<?php

namespace Integrated\Bundle\StorageBundle\Storage\Reflection\Document;

/**
 * @author Johnny Borg <johnny@e-active.nl>
 */
class ManipulatorDocument
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
     * @param object $document
     */
    public function __construct($document)
    {
        if (is_object($document)) {
            $this->document = $document;
        } else {
            // We can call methods, so it seems like we've been given something rather unpleasant
            throw new \InvalidArgumentException(
                sprintf('Object of type %s is not a object', gettype($document))
            );
        }
    }

    /**
     * @param string $propertyName
     * @return mixed
     */
    public function get($propertyName)
    {
        $method = sprintf(self::GET_SIGNATURE, ucfirst($propertyName));
        if (method_exists($this->document, $method)) {
            return call_user_func([$this->document, $method]);
        }

        // Well that did not go as planned
        throw new \LogicException(
            sprintf(
                'Required method %s does not exist on class %s.',
                $method,
                get_class($this->document)
            )
        );
    }

    /**
     * @param string $propertyName
     * @param object $propertyValue
     * @return mixed
     */
    public function set($propertyName, $propertyValue)
    {
        $method = sprintf(self::SET_SIGNATURE, ucfirst($propertyName));
        if (method_exists($this->document, $method)) {
            return call_user_func([$this->document, $method], $propertyValue);
        }

        // We need something to set it, seems like we can't
        throw new \LogicException(
            sprintf(
                'Required method %s does not exist on class %s.',
                $method,
                get_class($this->document)
            )
        );
    }
}
