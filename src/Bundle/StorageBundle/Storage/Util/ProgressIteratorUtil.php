<?php

namespace Integrated\Bundle\StorageBundle\Storage\Util;

use Doctrine\MongoDB\ArrayIterator;
use Doctrine\MongoDB\Iterator;
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
    const FORMAT = '%current%/%max% [%bar%] %percent:3s%% %elapsed:6s%/%estimated:-6s% %memory:6s%';

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
        if ($this->iterator->count()) {
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
        if ($this->iterator->count()) {
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
        $progress = new ProgressBar($this->output, $this->iterator->count());
        $progress->start();
        $progress->setFormat(self::FORMAT);
        $progress->setRedrawFrequency(ceil($this->iterator->count() / 50));

        return $progress;
    }
}
