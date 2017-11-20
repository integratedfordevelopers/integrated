<?php

/*
 * This file is part of the Integrated package.
 *
 * (c) e-Active B.V. <integrated@e-active.nl>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Integrated\Common\ContentType;

use Integrated\Common\ContentType\Exception\InvalidArgumentException;
use Integrated\Common\ContentType\Exception\UnexpectedTypeException;

/**
 * @author Jan Sanne Mulder <jansanne@e-active.nl>
 */
interface ResolverInterface
{
    /**
     * Returns the content type.
     *
     * @param string $type The content type name
     *
     * @return ContentTypeInterface
     *
     * @throws UnexpectedTypeException  if the passed argument is not a string
     * @throws InvalidArgumentException if the content type can not be found
     */
    public function getType($type);

    /**
     * check if the content type exists
     *
     * @param string $type The content type name
     *
     * @return bool
     *
     * @throws UnexpectedTypeException  if the passed arguments are not strings
     */
    public function hasType($type);

    /**
     * Get a list of all the content types.
     *
     * @return IteratorInterface
     */
    public function getTypes();
}
