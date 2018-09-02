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

use Doctrine\Common\Collections\ArrayCollection;
use Integrated\Bundle\ImageBundle\Converter\Container;
use Integrated\Bundle\ImageBundle\Exception\FormatException;
use Integrated\Bundle\SolrBundle\Process\Exception\LogicException;
use Integrated\Bundle\StorageBundle\Form\Upload\StorageIntentUpload;
use Symfony\Component\HttpFoundation\File\UploadedFile;
use Symfony\Component\Validator\Constraint;
use Symfony\Component\Validator\ConstraintValidator;

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
     * @var ArrayCollection
     */
    private $webFormats;

    /**
     * @var string
     */
    private $convert;

    /**
     * @param Container $container
     * @param string    $convert
     * @param array     $webFormats
     */
    public function __construct(Container $container, $convert, $webFormats)
    {
        $this->container = $container;
        $this->convert = $convert;
        $this->webFormats = new ArrayCollection($webFormats);
    }

    /**
     * {@inheritdoc}
     */
    public function validate($value, Constraint $constraint)
    {
        if (null === $value) {
            return;
        }

        try {
            if ($value instanceof UploadedFile) {
                // Skip known webformats
                if (!$this->webFormats->contains($value->getClientOriginalExtension())) {
                    $this->container->find($this->convert, new StorageIntentUpload(null, $value));
                }
            } else {
                // This not happen
                throw new LogicException(
                    sprintf(
                        'Type of value must be %s but the given is %s',
                        UploadedFile::class,
                        \is_object($value) ? \get_class($value) : \gettype($value)
                    )
                );
            }
        } catch (FormatException $formatException) {
            // There's something wrong with the format or no converter that supports it
            $this->context->buildViolation($formatException->getMessage())->addViolation();
        } catch (\Exception $e) {
            // Pass the error along
            $this->context->buildViolation($e->getMessage())->addViolation();
        }
    }
}
