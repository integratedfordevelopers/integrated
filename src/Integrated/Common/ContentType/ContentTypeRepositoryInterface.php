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
 * @author Jeroen van Leeuwen <jeroen@e-activ.nl>
 */
interface ContentTypeRepositoryInterface
{
    /**
     * @param string $id
     * @return ContentTypeInterface
     */
    public function find($id);
}
