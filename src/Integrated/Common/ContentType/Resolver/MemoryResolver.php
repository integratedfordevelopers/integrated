<?php

/*
 * This file is part of the Integrated package.
 *
 * (c) e-Active B.V. <integrated@e-active.nl>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Integrated\Common\ContentType\Resolver;

use Integrated\Common\ContentType\ContentTypeIterator;
use Integrated\Common\ContentType\ContentTypeInterface;
use Integrated\Common\ContentType\ContentTypeResolverInterface;

use Integrated\Common\ContentType\Exception\ExceptionInterface;
use Integrated\Common\ContentType\Exception\UnexpectedTypeException;
use Integrated\Common\ContentType\Exception\InvalidArgumentException;

/**
 * @author Jan Sanne Mulder <jansanne@e-active.nl>
 */
class MemoryResolver implements ContentTypeResolverInterface
{
    /**
     * @var ContentTypeInterface[]
     */
    private $types = [];

    /**
     * Constructor.
     *
     * @param ContentTypeInterface[] $types
     */
    public function __construct(array $types)
    {
        $this->types = $types;
    }

    /**
     * {@inheritdoc}
     */
    public function getType($class, $type)
    {
        if (!is_string($class)) {
            throw new UnexpectedTypeException($class, 'string');
        }

        if (!is_string($type)) {
            throw new UnexpectedTypeException($type, 'string');
        }

        $key = json_encode(['class' => $class, 'type' => $type]);

        if (isset($this->types[$key])) {
            return $this->types[$key];
        }

        throw new InvalidArgumentException(sprintf('Could not resolve the content type based on the given class "%s" and type "%s"', $class, $type));
    }

    /**
     * {@inheritdoc}
     */
    public function hasType($class, $type)
    {
        try {
            $this->getType($class, $type);
        } catch (UnexpectedTypeException $e) {
            throw $e;
        } catch (ExceptionInterface $e) {
            return false;
        }

        return true;
    }

    /**
     * {@inheritdoc}
     */
    public function getTypes()
    {
        return new ContentTypeIterator(array_values($this->types));
    }
}
