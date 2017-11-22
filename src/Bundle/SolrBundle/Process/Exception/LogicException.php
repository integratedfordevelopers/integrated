<?php

/*
 * This file is part of the Integrated package.
 *
 * (c) e-Active B.V. <integrated@e-active.nl>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Integrated\Bundle\SolrBundle\Process\Exception;

/**
 * @author Johnny Borg <johnny@e-active.nl>
 */
class LogicException extends \Exception
{
    /**
     * @return static
     */
    public static function invalidMethodCall()
    {
        return new static('This method should not be called in this context');
    }

    /**
     * @return static
     */
    public static function noProcessesGenerated()
    {
        return new static('No processes could be generated with given input');
    }
}
