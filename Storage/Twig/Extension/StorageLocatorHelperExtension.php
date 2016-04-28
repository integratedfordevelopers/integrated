<?php

/*
 * This file is part of the Integrated package.
 *
 * (c) e-Active B.V. <integrated@e-active.nl>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Integrated\Bundle\StorageBundle\Twig\Extension;
use Integrated\Bundle\StorageBundle\Locator\StorageLocator;
use Integrated\Bundle\StorageBundle\Storage\Util\StorageLocatorHelper;

/**
 * @author Marijn Otte <marijn@e-active.nl>
 */
class StorageLocatorHelperExtension extends \Twig_Extension
{
    /**
     * @param TwigRendererInterface $renderer
     * @param FormFactory $form
     */
    public function __construct(TwigRendererInterface $renderer, FormFactory $form)
    {
        $this->renderer = $renderer;
        $this->form = $form;
    }

    /**
     * {@inheritdoc}
     */
    public function getFunctions()
    {
        return [
            new \Twig_SimpleFunction(
                'integrated_storage_locatorhelper',
                [$this, 'createStorageLocatorhelper']
            ),
        ];
    }

    /**
     * @param string $identifier
     * @param array $filesystems
     * @return StorageLocator
     */
    public function createStorageLocatorhelper(
        $identifier,
        $filesystems
    ) {
        return new StorageLocatorHelper($identifier, $filesystems);
    }

    /**
     * {@inheritdoc}
     */
    public function getName()
    {
        return 'integrated_storage_locatorhelper_extension';
    }
}
