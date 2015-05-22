<?php

/*
 * This file is part of the Integrated package.
 *
 * (c) e-Active B.V. <integrated@e-active.nl>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Integrated\Bundle\ContentBundle\Block;

use Symfony\Component\Form\FormFactory;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\RequestStack;

use Doctrine\ODM\MongoDB\DocumentManager;

use Integrated\Bundle\BlockBundle\Block\BlockHandler;
use Integrated\Bundle\ContentBundle\Document\Block\FormBlock;
use Integrated\Common\Block\BlockInterface;
use Integrated\Common\Content\Form\FormFactory as ContentFormFactory;

/**
 * Search block handler
 *
 * @author Ger Jan van den Bosch <gerjan@e-active.nl>
 */
class SearchBlockHandler extends BlockHandler
{
    /**
     * {@inheritdoc}
     */
    public function execute(BlockInterface $block)
    {
        return $this->render([
            'block' => $block,
        ]);
    }
}
