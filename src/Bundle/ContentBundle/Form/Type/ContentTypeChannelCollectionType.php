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

use Doctrine\Persistence\ObjectRepository;
use Integrated\Bundle\ContentBundle\Document\Channel\Channel;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;

/**
 * @author Jan Sanne Mulder <jansanne@e-active.nl>
 */
class ContentTypeChannelCollectionType extends AbstractType
{
    /**
     * @var ObjectRepository
     */
    private $repository;

    /**
     * @var null
     */
    private $channels = null;

    /**
     * @param ObjectRepository $repository
     */
    public function __construct(ObjectRepository $repository)
    {
        $this->repository = $repository;
    }

    /**
     * {@inheritdoc}
     */
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        foreach ($this->getChannels() as $channel) {
            $builder->add($channel->getId(), ContentTypeChannelType::class, ['channel' => $channel]);
        }
    }

    /**
     * {@inheritdoc}
     */
    public function getBlockPrefix()
    {
        return 'integrated_content_type_channel_collection';
    }

    /**
     * @return Channel[]
     */
    public function getChannels()
    {
        if ($this->channels === null) {
            $this->channels = $this->repository->findAll();
        }

        return $this->channels;
    }
}
