<?php

/*
 * This file is part of the Integrated package.
 *
 * (c) e-Active B.V. <integrated@e-active.nl>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Integrated\Bundle\ContentBundle\Provider;

use Integrated\Bundle\ContentBundle\Doctrine\ContentTypeManager;
use Integrated\Bundle\ContentBundle\Document\Content\File;
use Integrated\Bundle\ContentBundle\Filter\ContentTypeFilter;
use Integrated\Bundle\WorkflowBundle\Services\WorkflowPermission;

/**
 * @author Johnny Borg <johnny@e-active.nl>
 */
class MediaProvider
{
    /**
     * @var ContentTypeManager
     */
    private $contentTypeManager;

    /**
     * @var WorkflowPermission
     */
    private $permission;

    /**
     * @param ContentTypeManager $contentTypeManager
     * @param WorkflowPermission $permission
     */
    public function __construct(ContentTypeManager $contentTypeManager, WorkflowPermission $permission = null)
    {
        $this->contentTypeManager = $contentTypeManager;
        $this->permission = $permission;
    }

    /**
     * @param null|string $filter
     *
     * @return \Integrated\Bundle\ContentBundle\Document\ContentType\ContentType[]
     */
    public function getContentTypes($filter = null)
    {
        $contentTypes = [];

        foreach ($this->contentTypeManager->filterInstanceOf(File::class) as $contentType) {
            if (ContentTypeFilter::match($contentType->getClass(), $filter)) {
                if (null == $this->permission) {
                    $contentTypes[] = $contentType;
                } elseif ($this->permission->hasAccess($contentType)) {
                    $contentTypes[] = $contentType;
                }
            }
        }

        return $contentTypes;
    }
}
