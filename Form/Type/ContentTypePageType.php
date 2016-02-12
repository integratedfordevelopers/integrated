<?php

/*
 * This file is part of the Integrated package.
 *
 * (c) e-Active B.V. <integrated@e-active.nl>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Integrated\Bundle\PageBundle\Form\Type;

use Integrated\Bundle\PageBundle\ContentType\ContentTypeControllerManager;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\Form\FormEvent;
use Symfony\Component\Form\FormEvents;
use Symfony\Component\OptionsResolver\OptionsResolver;

use Integrated\Bundle\PageBundle\Document\Page\ContentTypePage;
/**
 * @author Johan Liefers <johan@e-active.nl>
 */
class ContentTypePageType extends AbstractType
{
    /**
     * @var ContentTypeControllerManager
     */
    protected $controllerManager;

    /**
    /**
     * ContentTypePageType constructor.
     * @param ContentTypeControllerManager $controllerManager
     */
    public function __construct(ContentTypeControllerManager $controllerManager)
    {
        $this->controllerManager = $controllerManager;
    }

    /**
     * {@inheritdoc}
     */
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        /** @var ContentTypePage $contentTypePage */
        $contentTypePage = $builder->getData();

        $builder->add('path', 'text', [
            'label' => 'URL'
        ]);

        $builder->add('layout', 'integrated_page_layout_choice', [
            'theme' => $options['theme'],
            'directory' => sprintf('/content/%s', $contentTypePage->getContentType()->getId())
        ]);

        $builder->addEventListener(FormEvents::PRE_SET_DATA, function (FormEvent $event) {
            $contentTypePage = $event->getData();

            if (!$contentTypePage instanceof ContentTypePage) {
                return;
            }

            $className = $contentTypePage->getContentType()->getClass();
            $controller = $this->controllerManager->getController($className);

            if (!is_array($controller)) {
                throw new \Exception(sprintf('Controller service for class "%s" is not defined', $className));
            }

            $contentTypePage->setControllerService($controller['service']);

            if (count($controller['controller_actions']) > 1) {
                $event->getForm()->add('controller_action', 'choice', [
                    'choices' => array_combine($controller['controller_actions'], $controller['controller_actions'])
                ]);
            } else {
                $contentTypePage->setControllerAction($controller['controller_actions'][0]);
            }
        });
    }

    /**
     * {@inheritdoc}
     */
    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults([
            'data_class' => 'Integrated\Bundle\PageBundle\Document\Page\ContentTypePage',
            'theme' => 'default',
        ]);
    }

    /**
     * {@inheritdoc}
     */
    public function getName()
    {
        return 'integrated_page_content_type_page';
    }
}
