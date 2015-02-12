<?php

/*
 * This file is part of the Integrated package.
 *
 * (c) e-Active B.V. <integrated@e-active.nl>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Integrated\Bundle\BlockBundle\Locator;

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
     * @var array
     */
    private $templates;

    /**
     * @param Kernel $kernel
     */
    public function __construct(Kernel $kernel)
    {
        $this->kernel = $kernel;
    }

    /**
     * @param string $type
     * @return array
     */
    public function getLayouts($type)
    {
        if (!isset($this->layouts[$type])) {

            $this->layouts[$type] = [];

            foreach ($this->getTemplates() as $template) {

                $path = $template . '/blocks/' . $type;

                if (is_dir($path)) {

                    $finder = new Finder();
                    $finder->files()->in($path)->name('*.html.twig');

                    /** @var \Symfony\Component\Finder\SplFileInfo $file */
                    foreach ($finder as $file) {

                        $this->layouts[$type][] = $file->getRelativePathname();
                    }
                }
            }
        }

        return $this->layouts[$type];
    }


    /**
     * @return array
     */
    protected function getTemplates()
    {
        if (null === $this->templates) {

            $this->templates = [];

            foreach ($this->kernel->getBundles() as $bundle) {

                $path = $bundle->getPath() . '/Resources/views/templates';

                if (is_dir($path)) {

                    $finder = new Finder();
                    $finder->directories()->in($path)->depth(0);

                    /** @var \Symfony\Component\Finder\SplFileInfo $file */
                    foreach ($finder as $file) {

                        $this->templates[] = $file->getPathname();
                    }
                }
            }
        }

        return $this->templates;
    }
}
