<?php

/*
 * This file is part of the Integrated package.
 *
 * (c) e-Active B.V. <integrated@e-active.nl>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Integrated\Common\Converter\Type;

/**
 * @author Jan Sanne Mulder <jansanne@e-active.nl>
 */
class ResolvedTypeFactory implements ResolvedTypeFactoryInterface
{
    /**
     * {@inheritdoc}
     */
    public function createType(TypeInterface $type, array $extensions)
    {
        return new ResolvedType($type, $extensions);
    }
}
