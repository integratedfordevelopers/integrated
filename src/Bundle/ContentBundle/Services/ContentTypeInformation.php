<?php

/*
 * This file is part of the Integrated package.
 *
 * (c) e-Active B.V. <integrated@e-active.nl>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Integrated\Bundle\ContentBundle\Services;

use Doctrine\ODM\MongoDB\DocumentManager;
use Integrated\Bundle\ContentBundle\Document\ContentType\ContentType;

class ContentTypeInformation
{
    /**
     * @var DocumentManager
     */
    private $dm;

    /**
     * @param DocumentManager $dm
     */
    public function __construct(DocumentManager $dm)
    {
        $this->dm = $dm;
    }

    /**
     * @param string $channelId
     * @return array
     */
    public function getPublishingAllowedContentTypes(string $channelId)
    {
        $result = [];

        $contentTypes = $this->dm->getRepository(ContentType::class)->findAll();
        foreach ($contentTypes as $contentType) {
            $channelOption = $contentType->getOption('channels');
            if (isset($channelOption['disabled'])
                && $channelOption['disabled'] == 1
                || $contentType->getOption('publication') === 'disabled') {
                continue;
            }

            if (isset($channelOption['restricted']) && (count($channelOption['restricted']) > 0) && !in_array($channelId, $channelOption['restricted'])) {
                continue;
            }

            $result[] = $contentType->getId();
        }

        return $result;
    }
}
