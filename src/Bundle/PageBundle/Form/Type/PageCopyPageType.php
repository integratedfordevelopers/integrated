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

use Integrated\Bundle\BlockBundle\Document\Block\Block;
use Integrated\Bundle\BlockBundle\Document\Block\InlineTextBlock;
use Integrated\Bundle\ChannelBundle\Form\Type\ChannelChoiceType;
use Integrated\Bundle\PageBundle\Document\Page\Grid\Grid;
use Integrated\Bundle\PageBundle\Document\Page\Grid\Item;
use Integrated\Bundle\PageBundle\Document\Page\Grid\ItemsInterface;
use Integrated\Bundle\PageBundle\Document\Page\Page;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\CheckboxType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\Form\FormInterface;
use Symfony\Component\Form\FormView;
use Symfony\Component\OptionsResolver\OptionsResolver;

class PageCopyPageType extends AbstractType
{
    /**
     * {@inheritdoc}
     */
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder->add('selected', CheckboxType::class, [
            'required' => false,
        ]);

        /** @var Page $page */
        $page = $options['page'];
        $blocks = [];
        foreach ($page->getGrids() as $grid) {
            if ($grid instanceof Grid) {
                $blocks = array_merge($blocks, $this->getGridBlocks($grid, $page));
            }
        }

        $builder->add('blocks', PageCopyBlocksType::class, [
            'blocks' => $blocks,
            'label' => false,
            'channel' => $options['channel'],
            'targetChannel' => $options['targetChannel'],
        ]);
    }

    /**
     * {@inheritdoc}
     */
    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setRequired(['page', 'copyAction', 'channel', 'targetChannel']);
        $resolver->setAllowedTypes('page', Page::class);
        $resolver->setAllowedTypes('copyAction', 'string');
        $resolver->setAllowedTypes('channel', 'string');
        $resolver->setAllowedTypes('targetChannel', 'string');
    }

    /**
     * {@inheritdoc}
     */
    public function buildView(FormView $view, FormInterface $form, array $options)
    {
        parent::buildView($view, $form, $options);

        $view->vars = array_merge($view->vars, [
            'page' => $options['page'],
            'copyAction' => $options['copyAction'],
        ]);
    }

    /**
     * @param ItemsInterface $grid
     * @param Page           $page
     *
     * @return array
     */
    private function getGridBlocks(ItemsInterface $grid, Page $page)
    {
        $blocks = [];

        foreach ($grid->getItems() as $item) {
            if (!$item instanceof Item) {
                continue;
            }

            $block = $item->getBlock();

            if ($block instanceof Block) {
                $blocks[$block->getId()] = $block;
            }

            if ($item->getRow()) {
                foreach ($item->getRow()->getColumns() as $column) {
                    $blocks = array_merge($blocks, $this->getGridBlocks($column, $page));
                }
            }
        }

        return $blocks;
    }
}
