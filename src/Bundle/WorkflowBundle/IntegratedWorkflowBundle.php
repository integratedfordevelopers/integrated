<?php

/*
 * This file is part of the Integrated package.
 *
 * (c) e-Active B.V. <integrated@e-active.nl>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Integrated\Bundle\WorkflowBundle;

use Integrated\Bundle\WorkflowBundle\DependencyInjection\IntegratedWorkflowExtension;
use Symfony\Component\HttpKernel\Bundle\Bundle;

/**
 * @author Jan Sanne Mulder <jansanne@e-active.nl>
 */
class IntegratedWorkflowBundle extends Bundle
{
    /**
     * {@inheritdoc}
     */
    public function getContainerExtension()
    {
        return new IntegratedWorkflowExtension();
    }
}
