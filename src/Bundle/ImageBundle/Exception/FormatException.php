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
class FormatException extends \ErrorException
{
    /**
     * @param string $inputFormat
     * @param string $outputFormat
     * @return static
     */
    public static function noSupportingConverter($inputFormat, $outputFormat)
    {
        return new static(
            sprintf(
                'Format %s can not be converted in to %s, there is not converter supporting this format.',
                $inputFormat,
                $outputFormat
            )
        );
    }
}
