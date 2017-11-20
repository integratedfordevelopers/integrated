<?php

/*
 * This file is part of the Integrated package.
 *
 * (c) e-Active B.V. <integrated@e-active.nl>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Integrated\Common\Solr\Converter\Resolver;

use Symfony\Component\Finder\SplFileInfo;
use Integrated\Common\Solr\Converter\ConverterSpecificationInterface;

/**
 * @author Jan Sanne Mulder <jansanne@e-active.nl>
 */
interface FileResolverReaderInterface
{
    /**
     * @param SplFileInfo $file
     * @return ConverterSpecificationInterface[]
     */
    public function read(SplFileInfo $file);
}
