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

use Symfony\Component\Validator\Constraints as Assert;
use Integrated\Bundle\ContentBundle\Document\ContentType\ContentType;
use Integrated\Common\Form\Mapping\Annotations as Type;
use Integrated\Bundle\BlockBundle\Document\Block\Block;

/**
 * Form block document
 *
 * @author Ger Jan van den Bosch <gerjan@e-active.nl>
 *
 * @Type\Document("Form block")
 */
class FormBlock extends Block
{
    /**
     * @var ContentType
     * @Type\Field(
     *      type="document",
     *      options={
     *          "class"="IntegratedContentBundle:ContentType\ContentType",
     *          "property"="name",
     *          "placeholder"=""
     *      }
     * )
     */
    protected $contentType;

    /**
     * @var string
     * @Assert\NotBlank
     * @Type\Field
     */
    protected $returnUrl;

    /**
     * @var bool
     * @Type\Field(
     *      type="checkbox",
     *      options={
     *          "required"=false
     *      }
     * )
     */
    protected $recaptcha = false;

    /**
     * @return ContentType
     */
    public function getContentType()
    {
        return $this->contentType;
    }

    /**
     * @param ContentType $contentType
     * @return $this
     */
    public function setContentType(ContentType $contentType)
    {
        $this->contentType = $contentType;
        return $this;
    }

    /**
     * @return string
     */
    public function getReturnUrl()
    {
        return $this->returnUrl;
    }

    /**
     * @param string $returnUrl
     * @return $this
     */
    public function setReturnUrl($returnUrl)
    {
        $this->returnUrl = $returnUrl;
        return $this;
    }

    /**
     * @return boolean
     */
    public function isRecaptcha()
    {
        return $this->recaptcha;
    }

    /**
     * @param boolean $recaptcha
     * @return $this
     */
    public function setRecaptcha($recaptcha)
    {
        $this->recaptcha = $recaptcha;
        return $this;
    }

    /**
     * {@inheritdoc}
     */
    public function getType()
    {
        return 'form';
    }
}
