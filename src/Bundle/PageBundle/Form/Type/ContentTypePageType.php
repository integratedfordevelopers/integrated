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

use Integrated\Bundle\PageBundle\Form\EventListener\ContentTypePageListener;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

use Integrated\Bundle\PageBundle\Services\ContentTypeControllerManager;
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

        if (!preg_match('/Content\\\(.+)Controller$/', get_class($options['controller']), $matchController)) {
            throw new \InvalidArgumentException(sprintf('The "%s" class does not look like a 
            contentTypeController class (it must be in a "Controller\Content" sub-namespace and the
             class name must end with "Controller")', get_class($options['controller'])));
        }
        if (!preg_match('/^(.+)Action$/', $contentTypePage->getControllerAction(), $matchAction)) {
            throw new \InvalidArgumentException(sprintf('The "%s" method does not look like an action method
             (it does not end with Action)', $contentTypePage->getControllerAction()));
        }

        $builder->add('layout', LayoutChoiceType::class, [
            'theme' => $options['theme'],
            'directory' => sprintf('/content/%s/%s', $matchController[1], $matchAction[1])
        ]);

        $builder->addEventSubscriber(new ContentTypePageListener($this->controllerManager));
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

        $resolver->setRequired('controller');

        $resolver->setAllowedTypes('controller', 'object');
    }

    /**
     * {@inheritdoc}
     */
    public function getName()
    {
        return 'integrated_page_content_type_page';
    }
}
