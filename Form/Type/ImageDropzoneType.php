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
use Integrated\Bundle\ImageBundle\Converter\Container;
use Integrated\Bundle\ImageBundle\Converter\Format\WebFormat;

use Symfony\Component\Form\FormInterface;
use Symfony\Component\Form\FormView;
use Symfony\Component\Translation\TranslatorInterface;

/**
 * @author Jan Sanne Mulder <jansanne@e-active.nl>
 */
class ImageDropzoneType extends AbstractDropzoneType
{
    /**
     * @var WebFormat
     */
    protected $webFormat;

    /**
     * @var Container
     */
    protected $converterContainer;

    /**
     * ImageDropzoneType constructor.
     * @param AssetManager $stylesheets
     * @param AssetManager $javascripts
     * @param TranslatorInterface $translator
     * @param ImageHandling $imageHandling
     * @param WebFormat $webFormat
     * @param Container $converterContainer
     */
    public function __construct(
        AssetManager $stylesheets,
        AssetManager $javascripts,
        TranslatorInterface $translator,
        ImageHandling $imageHandling,
        WebFormat $webFormat,
        Container $converterContainer
    ) {
        $this->webFormat = $webFormat;
        $this->converterContainer = $converterContainer;

        parent::__construct($stylesheets, $javascripts, $translator, $imageHandling, 'image');
    }

    /**
     * {@inheritdoc}
     */
    public function buildView(FormView $view, FormInterface $form, array $options)
    {
        parent::buildView($view, $form, $options);
        $view->vars['options']['extensions'] = array_merge(
            $this->webFormat->getWebFormats()->toArray(),
            $this->converterContainer->formats()->toArray()
        );
    }

    /**
     * {@inheritdoc}
     */
    public function getBlockPrefix()
    {
        return 'integrated_image_dropzone';
    }
}
