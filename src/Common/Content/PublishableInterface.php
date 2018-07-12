<?php

/*
 * This file is part of the Integrated package.
 *
 * (c) e-Active B.V. <integrated@e-active.nl>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Integrated\Common\Content;

interface PublishableInterface
{
    /**
     * Get the publish time of the document.
     *
     * @return PublishTimeInterface
     */
    public function getPublishTime(): PublishTimeInterface;

    /**
     * Set the publish time of the document.
     *
     * @param PublishTimeInterface $publishTime
     *
     * @return $this
     */
    public function setPublishTime(PublishTimeInterface $publishTime);

    /**
     * Set the published of the document.
     *
     * @param bool $published
     *
     * @return $this
     */
    public function setPublished($published);

    /**
     * Get the published of the document.
     *
     * @param bool $checkPublishTime
     *
     * @return bool
     */
    public function isPublished($checkPublishTime = true): bool;
}
