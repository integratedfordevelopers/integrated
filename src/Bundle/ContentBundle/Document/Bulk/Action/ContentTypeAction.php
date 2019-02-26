<?php

/*
 * This file is part of the Integrated package.
 *
 * (c) e-Active B.V. <integrated@e-active.nl>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Integrated\Bundle\ContentBundle\Document\Bulk\Action;

use Integrated\Common\Bulk\BulkActionInterface;

class ContentTypeAction implements BulkActionInterface
{
    /**
     * @var string
     */
    private $handler;

    /**
     * @var string
     */
    private $contentType;

    /**
     * ContentTypeAction constructor.
     * @param string $handler
     * @param string $contentType
     */
    public function __construct(string $handler)
    {
        $this->handler = $handler;
    }

    /**
     * @return string
     */
    public function getHandler()
    {
        return $this->handler;
    }

    /**
     * @param string $handler
     *
     * @return $this
     */
    public function setHandler($handler)
    {
        $this->handler = $handler;

        return $this;
    }

    /**
     * @return string
     */
    public function getContentType()
    {
        return $this->contentType;
    }

    /**
     * @param string $contentType
     *
     * @return $this
     */
    public function setContentType(string $contentType)
    {
        $this->contentType = $contentType;

        return $this;
    }

    /**
     * @return array
     */
    public function getOptions()
    {
        return [
            'contentType' => $this->getContentType(),
        ];
    }
}
