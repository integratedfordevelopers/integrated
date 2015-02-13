<?php

/*
 * This file is part of the Integrated package.
 *
 * (c) e-Active B.V. <integrated@e-active.nl>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Integrated\Bundle\PageBundle\Form\Type;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\Form\Extension\Core\ChoiceList\ChoiceList;

use Doctrine\ODM\MongoDB\DocumentManager;

use Integrated\Bundle\PageBundle\Form\Type\Grid\GridType;
use Integrated\Bundle\PageBundle\Locator\LayoutLocator;

/**
 * @author Ger Jan van den Bosch <gerjan@e-active.nl>
 */
class PageType extends AbstractType
{
    /**
     * @var DocumentManager
     */
    protected $dm;

    /**
     * @var LayoutLocator
     */
    protected $locator;

    /**
     * @param DocumentManager $dm
     * @param LayoutLocator $locator
     */
    public function __construct(DocumentManager $dm, LayoutLocator $locator)
    {
        $this->dm = $dm;
        $this->locator = $locator;
    }

    /**
     * {@inheritdoc}
     */
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder->add('title', 'text');

        $builder->add('slug', 'text');

        $builder->add('layout', 'choice', [
            'choice_list' => $this->getChoiceList(),
        ]);

        $builder->add('publishedAt', 'integrated_datetime');

        $builder->add('disabled', 'checkbox', [
            'required' => false,
        ]);

        $builder->add('grids', 'collection', [
            'type'         => new GridType($this->dm),
            'allow_add'    => true,
            'allow_delete' => true,
            'prototype'    => false,
            'required'     => false,
        ]);
    }

    /**
     * @return ChoiceList
     */
    protected function getChoiceList()
    {
        $layouts = $this->locator->getLayouts();

        return new ChoiceList($layouts, $layouts);
    }

    /**
     * {@inheritdoc}
     */
    public function getName()
    {
        return 'integrated_page_page';
    }
}