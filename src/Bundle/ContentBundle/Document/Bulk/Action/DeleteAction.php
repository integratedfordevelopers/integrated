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

class DeleteAction implements BulkActionInterface
{
    /**
     * @var string
     */
    private $handler;

    /**
     * ContentTypeAction constructor.
     *
     * @param string $handler
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
     * @return array
     */
    public function getOptions()
    {
        return [];
    }
}
