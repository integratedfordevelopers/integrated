<?php

/*
 * This file is part of the Integrated package.
 *
 * (c) e-Active B.V. <integrated@e-active.nl>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Integrated\Bundle\ContentBundle\Solr\Type;

use Doctrine\Persistence\ObjectManager;
use Integrated\Common\Content\ContentInterface;
use Integrated\Common\Converter\ContainerInterface;
use Integrated\Common\Converter\Type\TypeInterface;

/**
 * @author Jan Sanne Mulder <jansanne@e-active.nl>
 */
class ContentType implements TypeInterface
{
    /**
     * @var ObjectManager
     */
    private $manager;

    /**
     * @param ObjectManager $manager
     */
    public function __construct(ObjectManager $manager)
    {
        $this->manager = $manager;
    }

    /**
     * {@inheritdoc}
     */
    public function build(ContainerInterface $container, $data, array $options = [])
    {
        if (!$data instanceof ContentInterface) {
            return; // only process content
        }

        $container->set('id', $data->getContentType().'-'.$data->getId());

        $container->set('type_name', $data->getContentType());
        $container->set('type_class', $this->manager->getClassMetadata(\get_class($data))->getName()); // could be a doctrine proxy object but we need the actual class name.
        $container->set('type_id', $data->getId());
    }

    /**
     * {@inheritdoc}
     */
    public function getName()
    {
        return 'integrated.content';
    }
}
