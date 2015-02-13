<?php

/*
 * This file is part of the Integrated package.
 *
 * (c) e-Active B.V. <integrated@e-active.nl>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Integrated\Bundle\PageBundle\Locator;

use Symfony\Component\HttpKernel\Kernel;
use Symfony\Component\Finder\Finder;

/**
 * @author Ger Jan van den Bosch <gerjan@e-active.nl>
 */
class LayoutLocator
{
    /**
     * @var Kernel
     */
    private $kernel;

    /**
     * @var array
     */
    private $layouts;

    /**
     * @param Kernel $kernel
     */
    public function __construct(Kernel $kernel)
    {
        $this->kernel = $kernel;
    }

    /**
     * @return array
     */
    public function getLayouts()
    {
        if (null === $this->layouts) {

            $this->layouts = [];

            foreach ($this->kernel->getBundles() as $bundle) {

                $path = $bundle->getPath() . '/Resources/views/themes';

                if (is_dir($path)) {

                    $finder = new Finder();
                    $finder->files()->in($path)->depth(1)->name('*.html.twig');

                    /** @var \Symfony\Component\Finder\SplFileInfo $file */
                    foreach ($finder as $file) {

                        $this->layouts[] = $bundle->getName() . ':themes:' . $file->getRelativePathname();
                    }
                }
            }
        }

        return $this->layouts;
    }
}
