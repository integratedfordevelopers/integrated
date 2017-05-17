<?php

/*
 * This file is part of the Integrated package.
 *
 * (c) e-Active B.V. <integrated@e-active.nl>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Integrated\Bundle\ContentBundle\JsonLD;

use Integrated\Bundle\ContentBundle\Document\Content\Content;
use Integrated\Bundle\ContentBundle\Document\Content\Image;
use Integrated\Common\Content\Document\Storage\Embedded\StorageInterface;

/**
 * @author Jan Sanne Mulder <jansanne@e-active.nl>
 */
class UrlGenerator
{
    /**
     * @param object $content
     * @return string
     */
    public function generateUrl($content)
    {
        if ($content instanceof StorageInterface) {
            return $content->getPathname();
        }

        if ($content instanceof Image) {
            return $this->generateImageUrl($content);
        }

        if ($content instanceof Content) {
            if (method_exists($content, 'getSlug')) {
                return $this->generateContentUrl($content);
            }
        }

        return null;
    }

    /**
     * @param Image $content
     * @return string
     */
    protected function generateImageUrl(Image $content)
    {
        $domain = null;

        if ($channel = $content->getPrimaryChannel()) {
            $domain = $channel->getPrimaryDomain();

            if (!$domain) {
                $domain = reset($channel->getDomains());
            }
        }

        if ($domain) {
            return sprintf(
                'https://%s%s',
                $domain,
                $content->getFile()->getPathname()
            );
        }

        return null;
    }

    /**
     * @param Content $content
     * @return string
     */
    protected function generateContentUrl(Content $content)
    {
        $domain = null;

        if ($channel = $content->getPrimaryChannel()) {
            $domain = $channel->getPrimaryDomain();

            if (!$domain) {
                $domain = reset($channel->getDomains());
            }
        }

        if ($domain) {
            return sprintf(
                'https://%s/content/%s/%s',
                $domain,
                strtolower($content->getContentType()),
                $content->getSlug()
            );
        }

        return null;
    }
}
