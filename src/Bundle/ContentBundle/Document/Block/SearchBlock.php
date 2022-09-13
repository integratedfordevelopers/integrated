<?php

/*
 * This file is part of the Integrated package.
 *
 * (c) e-Active B.V. <integrated@e-active.nl>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Integrated\Bundle\ContentBundle\Document\Block;

use Integrated\Bundle\BlockBundle\Document\Block\Block;
use Integrated\Common\Form\Mapping\Annotations as Type;

/**
 * Search block document.
 *
 * @author Ger Jan van den Bosch <gerjan@e-active.nl>
 *
 * @Type\Document("Search block")
 */
class SearchBlock extends Block
{
    /**
     * @var ContentBlock
     * @Type\Field(
     *      type="Doctrine\Bundle\MongoDBBundle\Form\Type\DocumentType",
     *      options={
     *          "class"="Integrated\Bundle\ContentBundle\Document\Block\ContentBlock",
     *          "choice_label"="title",
     *          "placeholder"=""
     *      }
     * )
     */
    protected $block;

    /**
     * @var string
     * @Type\Field(
     *      type="Symfony\Component\Form\Extension\Core\Type\TextType",
     *      options={
     *          "label"="Results page URL"
     *      }
     * )
     */
    protected $url;

    /**
     * @return ContentBlock
     */
    public function getBlock()
    {
        return $this->block;
    }

    /**
     * @param ContentBlock $block
     *
     * @return $this
     */
    public function setBlock(ContentBlock $block)
    {
        $this->block = $block;

        return $this;
    }

    /**
     * @return string
     */
    public function getUrl()
    {
        return $this->url;
    }

    /**
     * @param string $url
     */
    public function setUrl($url)
    {
        $this->url = $url;
    }

    /**
     * {@inheritdoc}
     */
    public function getType()
    {
        return 'search';
    }
}
