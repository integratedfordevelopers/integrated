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

use Symfony\Contracts\Translation\TranslatorInterface;
use Integrated\Bundle\AssetBundle\Manager\AssetManager;
use Integrated\Bundle\ImageBundle\Twig\Extension\ImageExtension;

/**
 * @author Jan Sanne Mulder <jansanne@e-active.nl>
 */
class FileDropzoneType extends AbstractDropzoneType
{
    /**
     * @param AssetManager        $stylesheets
     * @param AssetManager        $javascripts
     * @param TranslatorInterface $translator
     * @param ImageExtension      $imageExtension
     */
    public function __construct(
        AssetManager $stylesheets,
        AssetManager $javascripts,
        TranslatorInterface $translator,
        ImageExtension $imageExtension
    ) {
        parent::__construct($stylesheets, $javascripts, $translator, $imageExtension, 'file');
    }

    /**
     * {@inheritdoc}
     */
    public function getBlockPrefix()
    {
        return 'integrated_file_dropzone';
    }
}
