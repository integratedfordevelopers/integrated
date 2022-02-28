<?php

/*
 * This file is part of the Integrated package.
 *
 * (c) e-Active B.V. <integrated@e-active.nl>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Integrated\Common\Content\Serializer;

use Symfony\Component\Serializer\Encoder\JsonEncoder;

/**
 * @author Jan Sanne Mulder <jansanne@e-active.nl>
 */
class JsonLDEncoder extends JsonEncoder
{
    /**
     * @var string
     */
    public const FORMAT = 'json-ld';

    /**
     * {@inheritdoc}
     */
    public function supportsEncoding($format)
    {
        return self::FORMAT === $format;
    }

    /**
     * {@inheritdoc}
     */
    public function supportsDecoding($format)
    {
        return self::FORMAT === $format;
    }
}
