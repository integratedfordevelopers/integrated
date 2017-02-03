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
    /**
     * Searches for integrated comment span tag and replaces it with a html comment tag
     * @param $content
     * @return string
     */
    public static function replaceSpanWithCommentTag($content)
    {
        $replacement = '<!--integrated-comment=$2-->$3<!--end-integrated-comment-->';

        return self::replaceSpanWith($content, $replacement);
    }

    /**
     * Searches for integrated comment span tag and replaces it with $replacement
     * @param $content
     * @param $replacement $2 commentId, $3 content
     * @return string
     */
    public static function replaceSpanWith($content, $replacement)
    {
        $pattern = '/(\<span class\=\"integrated-comment\" data-comment-id\=\"([\s\S]+?)\"\>)([\s\S]+?)(\<\/span\>)/';

        return preg_replace($pattern, $replacement, $content);
    }

    /**
     * Searches for comment tag and replaces it with span tag
     * @param $content
     * @return string
     */
    public static function replaceCommentWithSpan($content)
    {
        $replacement = '<span class="integrated-comment" data-comment-id="$2">$3</span>';

        return self::replaceCommentWith($content, $replacement);
    }

    /**
     * Cleans up any comments of integrated comment-bundle, for example to strip comments on front-end
     * @param $content
     * @return string
     */
    public static function removeComments($content)
    {
        return self::replaceCommentWith($content, '$3');
    }

    /**
     * Searches for comment tag and replaces it with $replacement
     * @param $content
     * @param $replacement $2 commentId, $3 content
     * @return string
     */
    public static function replaceCommentWith($content, $replacement)
    {
        $pattern = '/(\<\!\-\-integrated\-comment\=([\s\S]+?)\-\-\>)([\s\S]+?)(\<\!\-\-end\-integrated\-comment\-\-\>)/';

        return preg_replace($pattern, $replacement, $content);
    }
}
