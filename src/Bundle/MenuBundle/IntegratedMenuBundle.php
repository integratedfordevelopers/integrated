<?php

/*
 * This file is part of the Integrated package.
 *
 * (c) e-Active B.V. <integrated@e-active.nl>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Integrated\Bundle\MenuBundle;

use Integrated\Bundle\MenuBundle\DependencyInjection\IntegratedMenuExtension;
use Symfony\Component\HttpKernel\Bundle\Bundle;

/**
 * @author Jeroen van Leeuwen <jeroen@e-active.nl>
 */
class IntegratedMenuBundle extends Bundle
{
    /**
     * {@inheritdoc}
     */
    public function getContainerExtension()
    {
        return new IntegratedMenuExtension();
    }
}
