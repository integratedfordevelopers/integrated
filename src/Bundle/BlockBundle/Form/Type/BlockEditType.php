<?php

/*
 * This file is part of the Integrated package.
 *
 * (c) e-Active B.V. <integrated@e-active.nl>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Integrated\Bundle\BlockBundle\Form\Type;

use Integrated\Bundle\BlockBundle\Document\Block\Block;
use Integrated\Bundle\BlockBundle\Form\DataTransformer\GroupTransformer;
use Integrated\Bundle\BlockBundle\Locator\LayoutLocator;
use Integrated\Bundle\FormTypeBundle\Form\Type\SaveCancelType;
use Integrated\Bundle\UserBundle\Form\Type\GroupType;
use Integrated\Bundle\UserBundle\Model\GroupManagerInterface;
use Integrated\Common\Form\Type\MetadataType;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\Form\FormEvent;
use Symfony\Component\Form\FormEvents;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Security\Core\Authorization\AuthorizationCheckerInterface;

class BlockEditType extends AbstractType
{
    /**
     * @var LayoutLocator
     */
    private $layoutLocator;

    /**
     * @var AuthorizationCheckerInterface
     */
    private $authorizationChecker;

    /**
     * @var GroupManagerInterface
     */
    private $groupManager;

    /**
     * @param LayoutLocator                 $layoutLocator
     * @param AuthorizationCheckerInterface $authorizationChecker
     * @param GroupManagerInterface         $groupManager
     */
    public function __construct(
        LayoutLocator $layoutLocator,
        AuthorizationCheckerInterface $authorizationChecker,
        GroupManagerInterface $groupManager
    ) {
        $this->layoutLocator = $layoutLocator;
        $this->authorizationChecker = $authorizationChecker;
        $this->groupManager = $groupManager;
    }

    /**
     * {@inheritdoc}
     */
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $layouts = $this->layoutLocator->getLayouts($options['type']);
        if (\count($layouts) === 1) {
            $builder->addEventListener(FormEvents::POST_SUBMIT, function (FormEvent $event) use ($layouts) {
                $data = $event->getData();
                if ($data instanceof Block) {
                    $data->setLayout($layouts[0]);
                }
            });
        } else {
            $builder->add('layout', LayoutChoiceType::class, [
                'type' => $options['type'],
            ]);
        }

        if ($this->authorizationChecker->isGranted('ROLE_WEBSITE_MANAGER') || $this->authorizationChecker->isGranted('ROLE_ADMIN')) {
            $builder->add('groups', GroupType::class, [
                'required' => false,
                'multiple' => true,
                'label' => 'User group access',
                'attr' => [
                    'class' => 'select2',
                    'data-placeholder' => 'Block managers only',
                ],
            ]);

            $builder->get('groups')->addModelTransformer(new GroupTransformer($this->groupManager));
        }

        if ($options['method'] == 'PUT') {
            $builder->add('actions', SaveCancelType::class, [
                'cancel_route' => 'integrated_block_block_index',
                'label' => 'Create',
                'button_class' => '',
            ]);
        } else {
            $builder->add('actions', SaveCancelType::class, ['cancel_route' => 'integrated_block_block_index']);
        }
    }

    /**
     * {@inheritdoc}
     */
    public function configureOptions(OptionsResolver $options)
    {
        $options->setRequired(['type']);
    }

    /**
     * {@inheritdoc}
     */
    public function getParent()
    {
        return MetadataType::class;
    }
}
