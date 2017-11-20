<?php

/*
 * This file is part of the Integrated package.
 *
 * (c) e-Active B.V. <integrated@e-active.nl>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Integrated\Common\Bulk\Form;

use Integrated\Common\Content\ContentInterface;

/**
 * @author Jan Sanne Mulder <jansanne@e-active.nl>
 */
interface ConfigProviderInterface
{
    /**
     * @param ContentInterface[] $content
     * @return ConfigInterface[]
     */
    public function getConfig(array $content);
}
