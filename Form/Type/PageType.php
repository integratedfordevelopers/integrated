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

use Integrated\Bundle\PageBundle\Form\Type\Grid\GridType;

use Doctrine\Bundle\MongoDBBundle\ManagerRegistry;
use Doctrine\ODM\MongoDB\DocumentManager;

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
     * @param ManagerRegistry $mr
     */
    public function __construct(ManagerRegistry $mr)
    {
        $this->dm = $mr->getManager();
    }

    /**
     * {@inheritdoc}
     */
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder->add('title', 'text');

        $builder->add('slug', 'text');

        $builder->add('layout', 'choice', [
            'choices' => [
                'example1' => 'Example 1',
                'example2' => 'Example 2',
            ],
        ]);

        $builder->add('publishedAt', 'integrated_datetime');

        $builder->add('disabled', 'checkbox', [
            'required' => false,
        ]);

        $builder->add('grids', 'collection', [
            'type'     => new GridType($this->dm),
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