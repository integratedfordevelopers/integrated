<?php

/*
 * This file is part of the Integrated package.
 *
 * (c) e-Active B.V. <integrated@e-active.nl>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Integrated\Bundle\BlockBundle\Document\Block;

use Symfony\Component\Validator\Constraints as Assert;
use Integrated\Common\Form\Mapping\Annotations as Type;

/**
 * TextBlock document.
 *
 * @author Ger Jan van den Bosch <gerjan@e-active.nl>
 *
 * @Type\Document("Text block")
 */
class TextBlock extends Block
{
    use PublishTitleTrait;

    /**
     * @var string
     * @Assert\NotBlank
     * @Type\Field(
     *       options={
     *          "attr"={"class"="main-title"}
     *       }
     * )
     */
    protected $title;

    /**
     * @var string
     * @Type\Field(type="Integrated\Bundle\FormTypeBundle\Form\Type\EditorType",options={"mode"="web"})
     */
    protected $content;

    /**
     * @return string
     */
    public function getContent()
    {
        return $this->content;
    }

    /**
     * @param string $content
     *
     * @return $this
     */
    public function setContent($content)
    {
        $this->content = $content;

        return $this;
    }

    /**
     * {@inheritdoc}
     */
    public function getType()
    {
        return 'text';
    }
}
