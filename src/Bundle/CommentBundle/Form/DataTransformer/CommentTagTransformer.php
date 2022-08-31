<?php

/*
 * This file is part of the Integrated package.
 *
 * (c) e-Active B.V. <integrated@e-active.nl>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Integrated\Bundle\CommentBundle\Form\DataTransformer;

use Integrated\Bundle\CommentBundle\Util\StripTagsUtil;
use Symfony\Component\Form\DataTransformerInterface;
use Symfony\Component\Form\Exception\TransformationFailedException;

/**
 * @author Johan Liefers <johan@e-active.nl>
 */
class CommentTagTransformer implements DataTransformerInterface
{
    /**
     * Transforms html comments to span elements.
     *
     * @param string|null $content
     *
     * @return string|null
     *
     * @throws TransformationFailedException
     */
    public function transform($content)
    {
        if (null === $content) {
            return null;
        }

        if (!\is_string($content)) {
            throw new TransformationFailedException(sprintf(
                'Expected string, %s given',
                \gettype($content)
            ));
        }

        // replace comments with span so that we can add a nice style to the commented section
        $content = StripTagsUtil::replaceCommentWith($content, StripTagsUtil::SPAN_REPLACEMENT);

        return $content;
    }

    /**
     * Transforms span elements to html comments.
     *
     * @param string|null $content
     *
     * @return string|null
     *
     * @throws TransformationFailedException
     */
    public function reverseTransform($content)
    {
        if (null === $content) {
            return null;
        }

        if (!\is_string($content)) {
            throw new TransformationFailedException(sprintf(
                'Expected string, %s given',
                \gettype($content)
            ));
        }

        // replace span with comment tag, so no weird style is added on front-end
        $content = StripTagsUtil::replaceSpanWith($content, StripTagsUtil::COMMENT_REPLACEMENT);

        return $content;
    }
}
