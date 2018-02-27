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

trait PublishTitleTrait
{
    /**
     * @var string
     * @Type\Field(
     *       options={
     *          "required"=false,
     *          "attr"={"class"="published-title"}
     *       }
     * )
     */
    protected $publishedTitle = '';

    /**
     * @Type\Field(
     *      type="Symfony\Component\Form\Extension\Core\Type\CheckboxType",
     *      options={
     *          "required"=false,
     *          "attr"={"class"="use-title"}
     *      }
     * )
     */
    protected $useTitle;

    /**
     * @return string
     */
    public function getPublishedTitle()
    {
        return $this->publishedTitle !== null ? $this->publishedTitle : $this->title;
    }

    /**
     * @param string $publishedTitle
     */
    public function setPublishedTitle($publishedTitle)
    {
        $this->publishedTitle = $publishedTitle === null ? '' : $publishedTitle;
    }

    /**
     * @return bool
     */
    public function getUseTitle()
    {
        return $this->useTitle;
    }

    /**
     * @param bool $useTitle
     */
    public function setUseTitle($useTitle)
    {
        $this->useTitle = $useTitle;
    }
}
