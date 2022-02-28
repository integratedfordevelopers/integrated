<?php

/*
 * This file is part of the Integrated package.
 *
 * (c) e-Active B.V. <integrated@e-active.nl>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Integrated\Bundle\SolrBundle\Process;

use Integrated\Bundle\SolrBundle\Process\Exception\FormatException;
use Integrated\Bundle\SolrBundle\Process\Exception\LogicException;

/**
 * @author Johnny Borg <johnny@e-active.nl>
 */
class ArgumentProcess
{
    /**
     * @const string
     */
    public const FORMAT = '/^(\d):(\d)$/';

    /**
     * @var string
     */
    protected $argument;

    /**
     * @param string $argument
     */
    public function __construct($argument)
    {
        $this->argument = $argument;
    }

    /**
     * @return bool
     */
    public function isParentProcess()
    {
        return false === strpos($this->argument, ':');
    }

    /**
     * @return int
     */
    public function getProcessNumber()
    {
        if (!$this->isParentProcess()) {
            if (preg_match(self::FORMAT, $this->argument, $matches)) {
                return $matches[1];
            }

            throw FormatException::noRegexMatch();
        }

        throw LogicException::invalidMethodCall();
    }

    /**
     * @return int
     *
     * @throws FormatException
     */
    public function getProcessMax()
    {
        if (!$this->isParentProcess()) {
            if (preg_match(self::FORMAT, $this->argument, $matches)) {
                return $matches[2];
            }

            throw FormatException::noRegexMatch();
        }

        return (int) $this->argument;
    }
}
