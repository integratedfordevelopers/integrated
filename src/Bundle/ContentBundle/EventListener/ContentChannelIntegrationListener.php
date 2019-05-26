<?php

/*
 * This file is part of the Integrated package.
 *
 * (c) e-Active B.V. <integrated@e-active.nl>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Integrated\Bundle\ContentBundle\EventListener;

use Doctrine\Common\Persistence\ObjectRepository;
use Integrated\Bundle\ContentBundle\Document\Channel\Channel;
use Integrated\Bundle\ContentBundle\Form\EventListener\ChannelDefaultDataListener;
use Integrated\Bundle\ContentBundle\Form\EventListener\ChannelEnforcerListener;
use Integrated\Bundle\ContentBundle\Form\EventListener\ChannelPermissionListener;
use Integrated\Bundle\ContentBundle\Form\Type\PrimaryChannelType;
use Integrated\Common\Content\Form\Event\BuilderEvent;
use Integrated\Common\Content\Form\Events;
use Integrated\Common\Security\PermissionInterface;
use Symfony\Component\EventDispatcher\EventSubscriberInterface;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\Security\Core\Authorization\AuthorizationCheckerInterface;

/**
 * @author Jan Sanne Mulder <jansanne@e-active.nl>
 */
class ContentChannelIntegrationListener implements EventSubscriberInterface
{
    /**
     * @var ObjectRepository
     */
    private $repository;

    /**
     * @var AuthorizationCheckerInterface
     */
    private $authorizationChecker;

    /**
     * @param ObjectRepository              $repository
     * @param AuthorizationCheckerInterface $authorizationChecker
     */
    public function __construct(ObjectRepository $repository, AuthorizationCheckerInterface $authorizationChecker)
    {
        $this->repository = $repository;
        $this->authorizationChecker = $authorizationChecker;
    }

    /**
     * {@inheritdoc}
     */
    public static function getSubscribedEvents()
    {
        return [
            Events::POST_BUILD => ['buildForm', -60],
        ];
    }

    public function buildForm(BuilderEvent $event)
    {
        $options = $this->getConfig($event->getContentType()->getOption('channels'));

        if (isset($options['disabled']) && $options['disabled'] >= 2) {
            return;
        }

        $builder = $event->getBuilder();

        // check if the channels should be a form type or be enforced by a listener

        if (isset($options['disabled']) && $options['disabled'] == 1) {
            $enforce = [];

            foreach ($options['defaults'] as $channel) {
                $enforce[$channel['id']] = $channel['id'];
            }

            $enforce = array_values($enforce);

            // A empty channel list can also be enforced so no check for a empty
            // enforce array.

            $builder->addEventSubscriber(new ChannelEnforcerListener($this->getChannels($enforce), ChannelEnforcerListener::SET));
        } else {
            $channels = [];

            foreach ($options['defaults'] as $channel) {
                $channels[$channel['id']] = isset($channel['enforce']) ? $channel['enforce'] : false;
            }

            $choices = $this->getChannels();

            $enforce = [];
            $default = [];
            $notPermitted = [];

            foreach ($choices as $index => $value) {
                $authorizedRead = $this->authorizationChecker->isGranted(PermissionInterface::READ, $value);
                $authorizedWrite = $this->authorizationChecker->isGranted(PermissionInterface::WRITE, $value);

                if (isset($channels[$value->getId()])) {
                    if ($channels[$value->getId()]) {
                        $enforce[$value->getId()] = $value;
                        $default[$value->getId()] = $value;
                    } elseif ($authorizedWrite) {
                        $default[$value->getId()] = $value;
                    }
                }

                if (!$authorizedWrite) {
                    $notPermitted[$value->getId()] = $value;
                }

                if (!$authorizedRead && !isset($enforce[$value->getId()])) {
                    unset($choices[$index]);
                }

                if (isset($options['restricted']) && count($options['restricted']) > 0 && !in_array($value->getId(), $options['restricted'])) {
                    unset($choices[$index]);
                }
            }

            unset($channels);

            $operand = ChannelEnforcerListener::SET;

            if ($choices) {
                $operand = ChannelEnforcerListener::ADD;

                $builder->add('channels', ChoiceType::class, [
                    'required' => false,

                    'choices' => $choices,
                    'choice_value' => 'id',
                    'choice_label' => 'name',

                    'multiple' => true,
                    'expanded' => true,
                    'attr' => ['class' => 'channel-options'],
                    'choice_attr' => function ($value) use ($enforce) {
                        if ($value instanceof Channel && (isset($enforce[$value->getId()]) || !$this->authorizationChecker->isGranted(PermissionInterface::WRITE, $value))) {
                            return ['disabled' => 'disabled'];
                        }

                        return [];
                    },
                ]);

                $builder->add('primaryChannel', PrimaryChannelType::class);

                $builder->addEventSubscriber(new ChannelDefaultDataListener($default));
                $builder->addEventSubscriber(new ChannelPermissionListener($notPermitted));
            }

            $builder->addEventSubscriber(new ChannelEnforcerListener($enforce, $operand));
        }
    }

    protected function getConfig($options)
    {
        // @TODO should probably validate the options in some way
        // @TODO should be possible to override the options

        if ($options) {
            return $options;
        }

        // @TODO make the default options configurable

        return [
            'disabled' => 0,
            'defaults' => [],
        ];
    }

    /**
     * @param array $ids
     *
     * @return Channel[]
     */
    protected function getChannels(array $ids = null)
    {
        if ($ids === []) {
            return [];
        }

        $criteria = [];

        if ($ids) {
            $criteria['$or'] = [];

            foreach ($ids as $id) {
                $criteria['$or'][] = ['id' => $id];
            }
        }

        return $this->repository->findBy($criteria);
    }
}
