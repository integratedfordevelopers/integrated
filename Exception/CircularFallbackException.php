<?php

/*
 * This file is part of the Integrated package.
 *
 * (c) e-Active B.V. <integrated@e-active.nl>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Integrated\Bundle\ThemeBundle\Exception;

/**
 * @author Ger Jan van den Bosch <gerjan@e-active.nl>
 */
class CircularFallbackException extends \ErrorException
{
    /**
     * @param string $template
     * @param array $fallbackStack
     * @return CircularFallbackException
     */
    public static function templateNotFound($template, array $fallbackStack)
    {
        return new self(sprintf(
            'Circular theme fallback detected for template "%s", %s',
            $template,
            implode(' -> ', $fallbackStack)
        ));
    }
}
