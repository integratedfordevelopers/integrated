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

use Integrated\Common\ContentType\ContentTypeInterface;
use Integrated\Common\ContentType\Exception\ExceptionInterface;
use Integrated\Common\ContentType\Exception\InvalidArgumentException;
use Integrated\Common\ContentType\Exception\UnexpectedTypeException;
use Integrated\Common\ContentType\Iterator;
use Integrated\Common\ContentType\ResolverInterface;

/**
 * @author Jan Sanne Mulder <jansanne@e-active.nl>
 */
class MemoryResolver implements ResolverInterface
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
    public function getType($type)
    {
        if (!\is_string($type)) {
            throw new UnexpectedTypeException($type, 'string');
        }

        if (isset($this->types[$type])) {
            return $this->types[$type];
        }

        throw new InvalidArgumentException(sprintf('Could not resolve the content type based on the given type "%s"', $type));
    }

    /**
     * {@inheritdoc}
     */
    public function hasType($type)
    {
        try {
            $this->getType($type);
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
        return new Iterator($this->types);
    }
}
