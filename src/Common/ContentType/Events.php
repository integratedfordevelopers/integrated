<?php

/*
 * This file is part of the Integrated package.
 *
 * (c) e-Active B.V. <integrated@e-active.nl>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Integrated\Common\ContentType;

/**
 * @author Johan Liefers <johan@e-active.nl>
 */
final class Events
{
    public const CONTENT_TYPE_CREATED = 'integrated.content_type.created';

    public const CONTENT_TYPE_UPDATED = 'integrated.content_type.updated';

    public const CONTENT_TYPE_DELETED = 'integrated.content_type.deleted';
}
