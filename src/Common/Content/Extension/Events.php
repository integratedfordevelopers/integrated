<?php

/*
 * This file is part of the Integrated package.
 *
 * (c) e-Active B.V. <integrated@e-active.nl>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Integrated\Common\Content\Extension;

/**
 * @author Jan Sanne Mulder <jansanne@e-active.nl>
 */
final class Events
{
    private function __construct()
    {
    }

    public const METADATA = 'extension.metadata';

    public const PRE_READ = 'extension.read.pre';

    public const POST_READ = 'extension.read.post';

    public const PRE_CREATE = 'extension.create.pre';

    public const POST_CREATE = 'extension.create.post';

    public const PRE_UPDATE = 'extension.update.pre';

    public const POST_UPDATE = 'extension.update.post';

    public const PRE_DELETE = 'extension.delete.pre';

    public const POST_DELETE = 'extension.delete.post';
}
