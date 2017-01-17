<?php

/*
 * This file is part of the Integrated package.
 *
 * (c) e-Active B.V. <integrated@e-active.nl>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Integrated\Bundle\PageBundle\Form\DataTransformer;

use Symfony\Component\Form\DataTransformerInterface;

use Doctrine\ODM\MongoDB\DocumentManager;

use Integrated\Common\Block\BlockInterface;

/**
 * @author Ger Jan van den Bosch <gerjan@e-active.nl>
 */
class BlockTransformer implements DataTransformerInterface
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
    public function transform($block)
    {
        if ($block instanceof BlockInterface) {
            return $block->getId();
        }

        return null;
    }

    /**
     * {@inheritdoc}
     */
    public function reverseTransform($id)
    {
        return $this->dm->getRepository('IntegratedBlockBundle:Block\Block')->find($id);
    }
}
