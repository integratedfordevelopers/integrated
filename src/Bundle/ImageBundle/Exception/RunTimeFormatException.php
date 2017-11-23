<?php

/*
 * This file is part of the Integrated package.
 *
 * (c) e-Active B.V. <integrated@e-active.nl>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Integrated\Bundle\ImageBundle\Exception;

/**
 * @author Johnny Borg <johnny@e-active.nl>
 */
class RunTimeFormatException extends \ErrorException
{
    /**
     * @param $outputFormat
     *
     * @return static
     */
    public static function conversionFileCreateFail($converter, $outputFormat, $file)
    {
        return new static(
            sprintf(
                'The converter %s did not produce a file while converting %s to %s',
                $converter,
                $outputFormat,
                $file
            )
        );
    }
}
