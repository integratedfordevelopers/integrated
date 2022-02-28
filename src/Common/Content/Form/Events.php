<?php

/*
 * This file is part of the Integrated package.
 *
 * (c) e-Active B.V. <integrated@e-active.nl>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Integrated\Common\Content\Form;

/**
 * @author Jan Sanne Mulder <jansanne@e-active.nl>
 */
final class Events
{
    private function __construct()
    {
        // don't allow creation
    }

    public const PRE_BUILD = 'form.pre.build';

    public const POST_BUILD = 'form.post.build';

    public const PRE_BUILD_FIELD = 'form.pre.build.field';

    public const BUILD_FIELD = 'form.build.field';

    public const POST_BUILD_FIELD = 'form.post.build.field';

    /**
     * The pre view event is called at the start of the form
     * type buildView method.
     *
     * This event allows for changing of the options
     */
    public const PRE_VIEW = 'form.pre.view';

    /**
     * The post view event is called at the end of the form type
     * finishView method.
     */
    public const POST_VIEW = 'form.post.view';
}
