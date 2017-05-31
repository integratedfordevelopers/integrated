<?php

/*
 * This file is part of the Integrated package.
 *
 * (c) e-Active B.V. <integrated@e-active.nl>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Integrated\Bundle\ContentBundle\Bulk\ActionHandler;
use Integrated\Common\Content\ContentInterface;

/**
 * @author Jeroen van Leeuwen <jeroen@e-active.nl>
 * @author Patrick Mestebeld <patrick@e-active.nl>
 */
interface ActionHandlerInterface
{
    /**
     * @param ContentInterface $content
     * @param array $options
     * @return void
     */
    public function execute(ContentInterface $content, array $options);
}
