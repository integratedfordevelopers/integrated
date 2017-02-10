<?php

/*
 * This file is part of the Integrated package.
 *
 * (c) e-Active B.V. <integrated@e-active.nl>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Integrated\Bundle\CommentBundle\Twig\Extension;

use Integrated\Bundle\CommentBundle\Util\StripTagsUtil;

/**
 * @author Johan Liefers <johan@e-active.nl>
 */
class CommentExtension extends \Twig_Extension
{
    /**
     * {@inheritdoc}
     */
    public function getFilters()
    {
        return [
            new \Twig_SimpleFilter('remove_comments', [$this, 'escape'], ['is_safe' => ['html']]),
        ];
    }

    /**
     * @param string $content
     * @return string
     */
    public function escape($content)
    {
        return StripTagsUtil::replaceCommentWith($content, StripTagsUtil::ONLY_CONTENT_REPLACEMENT);
    }

    /**
     * {@inheritdoc}
     */
    public function getName()
    {
        return 'integrated_comment';
    }
}
