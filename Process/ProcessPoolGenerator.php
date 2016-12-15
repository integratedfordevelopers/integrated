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
use Symfony\Component\Process\Process;

/**
 * @author Johnny Borg <johnny@e-active.nl>
 */
class ProcessPoolGenerator
{
    /**
     * @const
     */
    const COMMAND = 'php app/console %s %s %d:%d';

    /**
     * @var InputInterface
     */
    private $input;

    /**
     * @param ArgumentProcess $argumentProcess
     */
    public function __construct(InputInterface $input)
    {
        $this->input = $input;
    }

    /**
     * @param ArgumentProcess $argumentProcess
     * @return ArrayCollection|Process[]
     */
    public function getProcessesPool(ArgumentProcess $argumentProcess, $workingDirectory)
    {
        $result = new ArrayCollection();

        for ($i = 0; $i < $argumentProcess->getProcessMax(); $i++) {
            $result[] = new Process(
                sprintf(
                    self::COMMAND,
                    $this->input->getFirstArgument(),
                    $this->input->getParameterOption('command'),
                    $i,
                    $argumentProcess->getProcessMax()
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
