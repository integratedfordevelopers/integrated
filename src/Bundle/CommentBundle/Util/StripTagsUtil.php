<?php

/*
 * This file is part of the Integrated package.
 *
 * (c) e-Active B.V. <integrated@e-active.nl>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Integrated\Bundle\CommentBundle\Util;

/**
 * @author Johan Liefers <johan@e-active.nl>
 */
class StripTagsUtil
{
    public const COMMENT_REPLACEMENT = '<!--integrated-comment=$2-->$3<!--end-integrated-comment-->';
    public const SPAN_REPLACEMENT = '<span class="integrated-comment" data-comment-id="$2">$3</span>';
    public const ONLY_CONTENT_REPLACEMENT = '$3';

    /**
     * Searches for integrated comment span tag and replaces it with $replacement.
     *
     * @param $content
     * @param $replacement $2 commentId, $3 content
     *
     * @return string
     */
    public static function replaceSpanWith($content, $replacement)
    {
        $pattern = '/(\<span class\=\"integrated-comment\" data-comment-id\=\"([\s\S]+?)\"\>)([\s\S]+?)(\<\/span\>)/';

        return preg_replace($pattern, $replacement, $content);
    }

    /**
     * Searches for comment tag and replaces it with $replacement.
     *
     * @param $content
     * @param $replacement $2 commentId, $3 content
     *
     * @return string
     */
    public static function replaceCommentWith($content, $replacement)
    {
        $pattern = '/(\<\!\-\-integrated\-comment\=([\s\S]+?)\-\-\>)'
            .'([\s\S]+?)(\<\!\-\-end\-integrated\-comment\-\-\>)/';

        return preg_replace($pattern, $replacement, $content);
    }
}
