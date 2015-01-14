<?php

/**
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
class TemplateLocator
{
    /**
     * @var string
     */
    private $directory = '/Resources/templates';

    /**
     * @var Kernel
     */
    private $kernel;

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
     * @return array
     */
    public function getTemplates()
    {
        if (null === $this->templates) {
            $this->templates = [];

            // @todo: support app/Resources/templates

            foreach ($this->kernel->getBundles() as $bundle) {

                $path = $bundle->getPath().$this->directory;

                if (is_dir($path)) {

                    $finder = new Finder();
                    $finder->files()->in($path)->name('*.html.twig')->notName('*.grid.html.twig');

                    /** @var \Symfony\Component\Finder\SplFileInfo $file */
                    foreach ($finder as $file) {

                        $this->templates[] = '@'.$bundle->getName().$this->directory.'/'.$file->getRelativePathname();
                    }
                }
            }
        }

        return $this->templates;
    }
}
