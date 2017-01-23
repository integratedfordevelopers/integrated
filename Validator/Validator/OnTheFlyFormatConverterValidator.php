<?php

/*
 * This file is part of the Integrated package.
 *
 * (c) e-Active B.V. <integrated@e-active.nl>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Integrated\Bundle\ImageBundle\Validator\Validator;

use Symfony\Component\Validator\Constraint;
use Symfony\Component\Validator\ConstraintValidator;

use Integrated\Bundle\ImageBundle\Converter\Container;
use Integrated\Bundle\ImageBundle\Exception\FormatException;

use Integrated\Bundle\StorageBundle\Form\Upload\StorageIntentUpload;

/**
 * @author Johnny Borg <johnny@e-active.nl>
 */
class OnTheFlyFormatConverterValidator extends ConstraintValidator
{
    /**
     * @var Container
     */
    private $container;

    /**
     * @var string
     */
    private $webFormat;

    /**
     * @param Container $container
     * @param string $webFormat
     */
    public function __construct(Container $container, $webFormat)
    {
        $this->container = $container;
        $this->webFormat = $webFormat;
    }

    /**
     * {@inheritdoc}
     */
    public function validate($value, Constraint $constraint)
    {
        try {
            $this->container->find($this->webFormat, new StorageIntentUpload(null, $value));
        } catch (FormatException $formatException) {
            // There's something wrong with the format or no converter that supports it
            $this->context->buildViolation($formatException->getMessage())->addViolation();
        }
    }
}
