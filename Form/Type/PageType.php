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

use Doctrine\ODM\MongoDB\DocumentManager;

use Integrated\Bundle\PageBundle\Form\Type\Grid\GridType;

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
     * @param DocumentManager $dm
     */
    public function __construct(DocumentManager $dm)
    {
        $this->dm = $dm;
    }

    /**
     * {@inheritdoc}
     */
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder->add('title', 'text');

        $builder->add('path', 'text', [
            'label' => 'URL',
        ]);

        $builder->add('layout', 'integrated_page_layout_choice');

        $builder->add('publishedAt', 'integrated_datetime');

        $builder->add('disabled', 'checkbox', [
            'required' => false,
        ]);
    }

    /**
     * {@inheritdoc}
     */
    public function getName()
    {
        return 'integrated_page_page';
    }
}