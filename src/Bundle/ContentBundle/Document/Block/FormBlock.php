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

use Integrated\Bundle\ContentBundle\Document\Relation\Relation;
use Integrated\Bundle\ContentBundle\Document\ContentType\ContentType;
use Integrated\Common\Form\Mapping\Annotations as Type;
use Integrated\Bundle\BlockBundle\Document\Block\Block;

use Symfony\Component\Validator\Constraints as Assert;

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
     *      type="Doctrine\Bundle\MongoDBBundle\Form\Type\DocumentType",
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
     * @Type\Field(
     *     type="text",
     *     options={
     *          "required"=false,
     *     }
     * )
     */
    protected $returnUrl;

    /**
     * @var string
     * @Type\Field(
     *     type="textarea",
     *     options={
     *          "required"=false,
     *     }
     * )
     */
    protected $textAfterSubmit;

    /**
     * @var array
     * @Assert\All({
     *     @Assert\Email
     * })
     * @Type\Field(
     *      type="Braincrafted\Bundle\BootstrapBundle\Form\Type\BootstrapCollectionType",
     *      options={
     *          "label"="Sent form to e-mail address(es)",
     *          "type"="email",
     *          "allow_add"=true,
     *          "allow_delete"=true,
     *          "required"=false,
     *      }
     * )
     */
    protected $emailAddresses = [];

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
     * @var Relation
     * @Type\Field(
     *      type="Doctrine\Bundle\MongoDBBundle\Form\Type\DocumentType",
     *      options={
     *          "label"="Link to content item",
     *          "class"="IntegratedContentBundle:Relation\Relation",
     *          "property"="name",
     *          "placeholder"="Do not link",
     *          "required"=false,
     *      }
     * )
     */
    protected $linkRelation;

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
     * @return string
     */
    public function getTextAfterSubmit()
    {
        return $this->textAfterSubmit;
    }

    /**
     * @param string $textAfterSubmit
     * @return $this
     */
    public function setTextAfterSubmit($textAfterSubmit)
    {
        $this->textAfterSubmit = $textAfterSubmit;
        return $this;
    }

    /**
     * @return array
     */
    public function getEmailAddresses()
    {
        return $this->emailAddresses;
    }

    /**
     * @param array $emailAddresses
     * @return $this
     */
    public function setEmailAddresses(array $emailAddresses = [])
    {
        $this->emailAddresses = $emailAddresses;
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
     * @return Relation
     */
    public function getLinkRelation()
    {
        return $this->linkRelation;
    }

    /**
     * @param Relation $linkRelation
     * @return $this
     */
    public function setLinkRelation($linkRelation)
    {
        $this->linkRelation = $linkRelation;
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
