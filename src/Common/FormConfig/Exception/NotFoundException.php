<?php

/*
 * This file is part of the Integrated package.
 *
 * (c) e-Active B.V. <integrated@e-active.nl>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Integrated\Common\FormConfig\Exception;

class NotFoundException extends RuntimeException
{
    /**
     * @param string $type
     * @param string $key
     */
    public function __construct(string $type, string $key)
    {
        parent::__construct(sprintf(
            'The content type %s does not have a form configuration with the key %s',
            $type,
            $key
        ));
    }
}
