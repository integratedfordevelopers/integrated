<?php

/*
 * This file is part of the Integrated package.
 *
 * (c) e-Active B.V. <integrated@e-active.nl>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Integrated\Common\Storage\Identifier;

use Integrated\Common\Storage\Reader\ReaderInterface;

/**
 * @author Johnny Borg <johnny@e-active.nl>
 */
interface IdentifierInterface
{
    /**
     * @param ReaderInterface $reader
     *
     * @return string
     */
    public function getIdentifier(ReaderInterface $reader);
}
