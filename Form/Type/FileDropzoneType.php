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

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormInterface;
use Symfony\Component\Form\FormView;

use Integrated\Bundle\AssetBundle\Manager\AssetManager;

/**
 * @author Ger Jan van den Bosch <gerjan@e-active.nl>
 */
class FileDropzoneType extends AbstractType
{
    /**
     * @var AssetManager
     */
    private $stylesheets;

    /**
     * @var AssetManager
     */
    private $javascripts;

    /**
     * @param AssetManager $stylesheets
     * @param AssetManager $javascripts
     */
    public function __construct(AssetManager $stylesheets, AssetManager $javascripts)
    {
        $this->stylesheets = $stylesheets;
        $this->javascripts = $javascripts;
    }

    /**
     * {@inheritdoc}
     */
    public function buildView(FormView $view, FormInterface $form, array $options)
    {
        $this->stylesheets->add('bundles/integratedstorage/components/dropzone/dist/dropzone.css');
        $this->javascripts->add('bundles/integratedstorage/components/dropzone/dist/dropzone.js');
    }

    /**
     * {@inheritdoc}
     */
    public function getParent()
    {
        return 'integrated_file';
    }

    /**
     * {@inheritdoc}
     */
    public function getName()
    {
        return 'integrated_file_dropzone';
    }
}
