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
use Integrated\Common\Security\PermissionInterface;
use Symfony\Component\Security\Core\Authorization\AuthorizationCheckerInterface;

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
     * @var AuthorizationCheckerInterface
     */
    private $authorizationChecker;

    /**
     * @param ContentTypeManager            $contentTypeManager
     * @param AuthorizationCheckerInterface $authorizationChecker
     */
    public function __construct(
        ContentTypeManager $contentTypeManager,
        AuthorizationCheckerInterface $authorizationChecker
    ) {
        $this->contentTypeManager = $contentTypeManager;
        $this->authorizationChecker = $authorizationChecker;
    }

    /**
     * @param string|null $filter
     *
     * @return \Integrated\Bundle\ContentBundle\Document\ContentType\ContentType[]
     */
    public function getContentTypes($filter = null)
    {
        $contentTypes = [];

        foreach ($this->contentTypeManager->filterInstanceOf(File::class) as $contentType) {
            if (ContentTypeFilter::match($contentType->getClass(), $filter)) {
                if ($this->authorizationChecker->isGranted(PermissionInterface::WRITE, $contentType)) {
                    $contentTypes[] = $contentType;
                }
            }
        }

        return $contentTypes;
    }
}
