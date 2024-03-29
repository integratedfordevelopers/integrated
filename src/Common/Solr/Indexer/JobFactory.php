<?php

/*
 * This file is part of the Integrated package.
 *
 * (c) e-Active B.V. <integrated@e-active.nl>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Integrated\Common\Solr\Indexer;

use Doctrine\Persistence\ObjectManager;
use Integrated\Common\Content\ContentInterface;
use Integrated\Common\Solr\Exception\OutOfBoundsException;
use Symfony\Component\Serializer\SerializerInterface;

/**
 * @author Jan Sanne Mulder <jansanne@e-active.nl>
 */
class JobFactory implements JobFactoryInterface
{
    public const ADD = 'ADD';
    public const DELETE = 'DELETE';

    /**
     * @var SerializerInterface
     */
    private $serializer;

    /**
     * @var ObjectManager
     */
    private $manager;

    /**
     * @var string
     */
    private $serializerFormat;

    /**
     * constructor.
     *
     * @param SerializerInterface $serializer
     * @param ObjectManager       $manager
     * @param string              $format
     */
    public function __construct(SerializerInterface $serializer, ObjectManager $manager, $format = 'json')
    {
        $this->serializer = $serializer;
        $this->manager = $manager;
        $this->serializerFormat = $format;
    }

    /**
     * {@inheritdoc}
     *
     * @return Job
     */
    public function create($action, ContentInterface $content)
    {
        $job = new Job(strtoupper($action));

        if ($job->getAction() == self::ADD) {
            $job->setOption('document.id', sprintf('%s-%s', $content->getContentType(), $content->getId()));

            $job->setOption('document.data', $this->serializer->serialize($content, $this->serializerFormat));
            $job->setOption('document.class', $this->manager->getClassMetadata(\get_class($content))->getName());
            $job->setOption('document.format', $this->serializerFormat);

            return $job;
        }

        if ($job->getAction() == self::DELETE) {
            return $job->setOption('id', sprintf('%s-%s', $content->getContentType(), $content->getId()));
        }

        throw new OutOfBoundsException(sprintf(
            'The action "%s" does not exist, valid actions are "%s"',
            $job->getAction(),
            'ADD, DELETE'
        ));
    }
}
