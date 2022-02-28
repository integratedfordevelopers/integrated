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

use Doctrine\Common\Collections\ArrayCollection;
use Integrated\Bundle\SolrBundle\Process\Exception\LogicException;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\HttpKernel\Kernel;
use Symfony\Component\Process\Process;

/**
 * @author Johnny Borg <johnny@e-active.nl>
 */
class ProcessPoolGenerator
{
    /**
     * @const
     */
    public const COMMAND = 'php bin/console %s %s %d:%d -e %s';

    /**
     * @var InputInterface
     */
    private $input;

    /**
     * @var Kernel
     */
    private $kernel;

    /**
     * @param InputInterface $input
     * @param Kernel         $kernel
     */
    public function __construct(InputInterface $input, Kernel $kernel)
    {
        $this->input = $input;
        $this->kernel = $kernel;
    }

    /**
     * @param ArgumentProcess $argumentProcess
     * @param string          $workingDirectory
     *
     * @return ArrayCollection|Process[]
     *
     * @throws LogicException
     */
    public function getProcessesPool(ArgumentProcess $argumentProcess, $workingDirectory)
    {
        $result = new ArrayCollection();

        for ($i = 0; $i < $argumentProcess->getProcessMax(); ++$i) {
            $result[] = new Process(
                sprintf(
                    self::COMMAND,
                    $this->input->getFirstArgument(),
                    $this->input->getParameterOption('command'),
                    $i,
                    $argumentProcess->getProcessMax(),
                    $this->kernel->getEnvironment()
                ),
                $workingDirectory
            );
        }

        if ($result->count()) {
            return $result;
        }

        throw LogicException::noProcessesGenerated();
    }
}
