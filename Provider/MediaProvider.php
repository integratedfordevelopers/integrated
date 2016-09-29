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

use Doctrine\Common\Persistence\ObjectRepository;

use Integrated\Bundle\ContentBundle\Document\Content\File;
use Integrated\Bundle\ContentBundle\Document\ContentType\ContentType;

use Integrated\Bundle\ContentBundle\Filter\ContentTypeFilter;
use Integrated\Bundle\WorkflowBundle\Services\WorkflowPermission;

/**
 * @author Johnny Borg <johnny@e-active.nl>
 */
class MediaProvider
{
    /**
     * @var ObjectRepository
     */
    private $contentType;

    /**
     * @var WorkflowPermission
     */
    private $permission;

    /**
     * @param ObjectRepository $contentType
     * @param WorkflowPermission $permission
     */
    public function __construct(ObjectRepository $contentType, WorkflowPermission $permission = null)
    {
        $this->contentType = $contentType;
        $this->permission = $permission;
    }

    /**
     * @param null|string $filter
     * @return \Integrated\Bundle\ContentBundle\Document\ContentType\ContentType[]
     */
    public function getContentTypes($filter = null)
    {
        $contentTypes = [];

        /** @var ContentType $contentType */
        foreach ($this->contentType->findAll() as $contentType) {
            if (ContentTypeFilter::match($contentType->getClass(), $filter)) {
                $reflection = new \ReflectionClass($contentType->getClass());
                if ($reflection->isSubclassOf(File::class) || File::class == $reflection->getName()) {
                    if (null == $this->permission) {
                        $contentTypes[] = $contentType;
                    } elseif ($this->permission->hasAccess($contentType)) {
                        $contentTypes[] = $contentType;
                    }
                }
            }
        }

        return $contentTypes;
    }
}
