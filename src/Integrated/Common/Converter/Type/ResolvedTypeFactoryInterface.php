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
interface ResolvedTypeFactoryInterface
{
    /**
     * Create a resolved type from the given type and extensions.
     *
     * @param TypeInterface            $type
     * @param TypeExtensionInterface[] $extensions
     *
     * @return ResolvedTypeInterface
     */
    public function createType(TypeInterface $type, array $extensions);
}
