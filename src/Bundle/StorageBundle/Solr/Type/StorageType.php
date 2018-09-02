<?php

/*
 * This file is part of the Integrated package.
 *
 * (c) e-Active B.V. <integrated@e-active.nl>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Integrated\Bundle\StorageBundle\Solr\Type;

use Integrated\Bundle\ContentBundle\Document\Content\Embedded\Storage\Metadata;
use Integrated\Common\Content\Document\Storage\Embedded\StorageInterface;
use Integrated\Common\Converter\ContainerInterface;
use Integrated\Common\Converter\Exception\UnexpectedTypeException;
use Integrated\Common\Converter\Type\TypeInterface;
use Symfony\Component\PropertyAccess\PropertyAccess;
use Symfony\Component\PropertyAccess\PropertyAccessor;

/**
 * @author Johnny Borg <johnny@e-active.nl>
 */
class StorageType implements TypeInterface
{
    /**
     * @var PropertyAccessor
     */
    private $reader;

    public function __construct()
    {
        $this->reader = PropertyAccess::createPropertyAccessor();
    }

    /**
     * {@inheritdoc}
     */
    public function build(ContainerInterface $container, $data, array $options = [])
    {
        foreach ($options as $key => $path) {
            if ($object = $this->reader->getValue($data, $path)) {
                if ($object instanceof StorageInterface) {
                    // The stuff we'll be adding in the solr document
                    $json = [];

                    $reflection = new \ReflectionClass($object);
                    foreach ($reflection->getProperties() as $property) {
                        // Mandatory, properties might be inaccessible
                        $property->setAccessible(true);

                        $value = $property->getValue($object);

                        // In some cases the value has an
                        if ($value instanceof Metadata) {
                            $value = $value->storageData()->toArray();
                        }

                        $json[$property->getName()] = $value;
                    }

                    // Update the result document (the joke is we're throwing it in the container)
                    $container->set($key, json_encode($json));
                } else {
                    // Throw and release
                    throw new UnexpectedTypeException(
                        \is_object($object) ? \get_class($object) : \gettype($object),
                        'anything with a StorageInterface'
                    );
                }
            }
        }
    }

    /**
     * {@inheritdoc}
     */
    public function getName()
    {
        return 'integrated.storage';
    }
}
