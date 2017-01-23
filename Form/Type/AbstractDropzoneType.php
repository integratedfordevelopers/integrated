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
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\Form\FormInterface;
use Symfony\Component\Form\FormView;
use Symfony\Component\Translation\TranslatorInterface;

use Gregwar\ImageBundle\Services\ImageHandling;

use Integrated\Common\Content\Document\Storage\Embedded\StorageInterface;
use Integrated\Bundle\AssetBundle\Manager\AssetManager;

/**
 * @author Johan Liefers <johan@e-active.nl>
 */
abstract class AbstractDropzoneType extends AbstractType
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
     * @var TranslatorInterface
     */
    private $translator;

    /**
     * @var ImageHandling
     */
    private $imageHandling;

    /**
     * @var string
     */
    private $type;

    /**
     * @param AssetManager $stylesheets
     * @param AssetManager $javascripts
     * @param TranslatorInterface $translator
     * @param ImageHandling $imageHandling
     * @param string $type
     */
    protected function __construct(
        AssetManager $stylesheets,
        AssetManager $javascripts,
        TranslatorInterface $translator,
        ImageHandling $imageHandling,
        $type
    ) {
        $this->stylesheets = $stylesheets;
        $this->javascripts = $javascripts;
        $this->translator = $translator;
        $this->imageHandling = $imageHandling;
        $this->type = $type;
    }

    /**
     * {@inheritdoc}
     */
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        //make hidden instead of checkbox
        $builder->add('remove', 'hidden', [
            'mapped' => false,
            'required' => false,
            'attr' => [
                'class' => 'remove-file'
            ]
        ]);
    }

    /**
     * {@inheritdoc}
     */
    public function buildView(FormView $view, FormInterface $form, array $options)
    {
        $this->stylesheets->add('bundles/integratedstorage/components/jquery.filer/css/jquery.filer.css');
        $this->stylesheets->add('bundles/integratedstorage/components/jquery.filer/css/themes/jquery.filer-dragdropbox-theme.css');
        $this->stylesheets->add('bundles/integratedstorage/css/drag-drop.css');
        $this->javascripts->add('bundles/integratedstorage/components/jquery.filer/js/jquery.filer.js');
        $this->javascripts->add('bundles/integratedstorage/js/drag-drop.js');

        $view->vars['type'] = $this->type;

        // builds the variable options passed to the javascript

        $options = ['captions' => [
                'removeConfirmation' => $this->translator->trans(sprintf('Are you sure you want to remove this %s?', $this->type)),
                'errors' => [
                    'filesLimit' => $this->translator->trans('You can only upload one ' . $this->type),
                    'filesType' => $this->translator->trans('Only Images are allowed to be uploaded.'),
                ]
        ]];

        if (isset($view->vars['preview']) && $view->vars['preview'] instanceof StorageInterface) {
            /** @var StorageInterface $preview */
            $preview = $view->vars['preview'];

            $options['files'] = [[
                'name' => $preview->getPathname(),
                'type' => $preview->getMetadata()->getMimeType(),
                'file' => $resizedPath = (false === strpos($preview->getMetadata()->getMimeType(), 'image') ?
                    $preview->getPathname() :
                    $this->imageHandling->open($preview)->cropResize(300, 150)->jpeg()
                ),
            ]];
        }

        $view->vars['options'] = $options;
    }
}
