<?php

/*
 * This file is part of the Integrated package.
 *
 * (c) e-Active B.V. <integrated@e-active.nl>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Integrated\Bundle\StorageBundle\Storage\Util;

use ArrayIterator;
use Iterator;
use Symfony\Component\Console\Helper\ProgressBar;
use Symfony\Component\Console\Output\OutputInterface;

/**
 * Create a new iterator with map and run over the data with walk.
 *
 * @author Johnny Borg <johnny@e-active.nl>
 */
class ProgressIteratorUtil
{
    /**
     * @const string
     */
    public const FORMAT = '%current%/%max% [%bar%] %percent:3s%% %elapsed:6s%/%estimated:-6s% %memory:6s%';

    /**
     * @var Iterator
     */
    private $iterator;

    /**
     * @var OutputInterface
     */
    private $output;

    /**
     * @param Iterator        $iterator
     * @param OutputInterface $output
     */
    public function __construct(Iterator $iterator, OutputInterface $output)
    {
        $this->iterator = $iterator;
        $this->output = $output;
    }

    /**
     * @param \Closure $closure
     *
     * @return $this
     */
    public function map(\Closure $closure)
    {
        if (\count($this->iterator->toArray())) {
            $progress = $this->createProgress();
            $iterator = new ArrayIterator();

            foreach ($this->iterator as $item) {
                $result = $closure($item);
                if ($result) {
                    $iterator[] = $result;
                }

                $progress->advance();
            }

            // Map creates a new collection
            $this->iterator = $iterator;
            $progress->finish();
        }

        return $this;
    }

    /**
     * @param \Closure $closure
     *
     * @return $this
     */
    public function walk(\Closure $closure)
    {
        if (\count($this->iterator->toArray())) {
            $progress = $this->createProgress();

            foreach ($this->iterator as $item) {
                $closure($item);

                $progress->advance();
            }

            $progress->finish();
        }

        return $this;
    }

    /**
     * @return ProgressBar
     */
    protected function createProgress()
    {
        $items = $this->iterator->toArray();
        $progress = new ProgressBar($this->output, \count($items));
        $progress->start();
        $progress->setFormat(self::FORMAT);
        $progress->setRedrawFrequency(ceil(\count($items) / 50));

        return $progress;
    }
}
