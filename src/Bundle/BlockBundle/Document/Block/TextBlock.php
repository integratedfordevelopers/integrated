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

use Doctrine\Common\Collections\ArrayCollection;
use Integrated\Bundle\ContentBundle\Document\Relation\Relation;
use Integrated\Common\Block\BlockRequiredItemsInterface;
use Integrated\Common\Form\Mapping\Annotations as Type;
use Symfony\Component\Validator\Constraints as Assert;

/**
 * TextBlock document.
 *
 * @author Ger Jan van den Bosch <gerjan@e-active.nl>
 *
 * @Type\Document("Text block")
 */
class TextBlock extends Block implements BlockRequiredItemsInterface
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
     * @var Relation
     * @Type\Field(
     *      type="Doctrine\Bundle\MongoDBBundle\Form\Type\DocumentType",
     *      options={
     *          "class"="IntegratedContentBundle:Relation\Relation",
     *          "choice_label"="name",
     *          "placeholder"="",
     *          "label"="Require relation",
     *          "required"=false
     *      }
     * )
     */
    protected $requiredRelation;

    /**
     * @var ArrayCollection
     * @Type\Field(
     *     type="Integrated\Bundle\FormTypeBundle\Form\Type\ContentChoiceType",
     *     options={
     *         "label"="Require relation with",
     *         "required"=false
     *     }
     * )
     */
    protected $requiredItems;

    /**
     * General object init.
     *
     * @param null $id
     */
    public function __construct($id = null)
    {
        parent::__construct($id);

        $this->requiredItems = new ArrayCollection();
    }

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
     * @return ?Relation
     */
    public function getRequiredRelation()
    {
        return $this->requiredRelation;
    }

    /**
     * @param ?Relation $requiredRelation
     */
    public function setRequiredRelation($requiredRelation)
    {
        $this->requiredRelation = $requiredRelation;
    }

    /**
     * @return array
     */
    public function getRequiredItems()
    {
        return $this->requiredItems->toArray();
    }

    /**
     * @param array $requiredItems
     *
     * @return $this
     */
    public function setRequiredItems(array $requiredItems)
    {
        $this->requiredItems = new ArrayCollection($requiredItems);

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
