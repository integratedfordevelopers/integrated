<?php

/*
 * This file is part of the Integrated package.
 *
 * (c) e-Active B.V. <integrated@e-active.nl>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Integrated\Bundle\ContentBundle\Form\Type;

use Doctrine\Common\Persistence\ObjectRepository;
use Integrated\Common\Form\Type\PermissionsType as CommonPermissionsType;
use Integrated\Bundle\ContentBundle\Form\DataTransformer\ChannelPermissionTransformer;

class ChannelPermissionsType extends CommonPermissionsType
{
    /**
     * @param ObjectRepository $repository
     */
    public function __construct(ObjectRepository $repository)
    {
        parent::__construct($repository);
    }

    /**
     * @return string
     */
    protected function getTransformer()
    {
        return new ChannelPermissionTransformer($this->repository);
    }
}
