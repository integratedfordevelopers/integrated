<?php

/*
 * This file is part of the Integrated package.
 *
 * (c) e-Active B.V. <integrated@e-active.nl>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Integrated\Bundle\StorageBundle\Form\Type;

use Gregwar\ImageBundle\Services\ImageHandling;
use Integrated\Bundle\AssetBundle\Manager\AssetManager;
use Symfony\Component\Translation\TranslatorInterface;

/**
 * @author Jan Sanne Mulder <jansanne@e-active.nl>
 */
class FileDropzoneType extends AbstractDropzoneType
{
    /**
     * @param AssetManager        $stylesheets
     * @param AssetManager        $javascripts
     * @param TranslatorInterface $translator
     * @param ImageHandling       $imageHandling
     */
    public function __construct(
        AssetManager $stylesheets,
        AssetManager $javascripts,
        TranslatorInterface $translator,
        ImageHandling $imageHandling
    ) {
        parent::__construct($stylesheets, $javascripts, $translator, $imageHandling, 'file');
    }

    /**
     * {@inheritdoc}
     */
    public function getName()
    {
        return 'integrated_file_dropzone';
    }

    /**
     * {@inheritdoc}
     */
    public function getParent()
    {
        return 'integrated_file';
    }
}
