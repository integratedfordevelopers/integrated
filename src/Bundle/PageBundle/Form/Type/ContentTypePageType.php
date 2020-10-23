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

use Integrated\Bundle\ChannelBundle\Form\Type\ChannelChoiceType;
use Integrated\Bundle\PageBundle\Document\Page\ContentTypePage;
use Integrated\Bundle\PageBundle\Form\EventListener\ContentTypePageListener;
use Integrated\Bundle\PageBundle\Resolver\ThemeResolver;
use Integrated\Bundle\PageBundle\Services\ContentTypeControllerManager;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

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
     * @var ThemeResolver
     */
    private $themeResolver;

    /**
     * ContentTypePageType constructor.
     *
     * @param ContentTypeControllerManager $controllerManager
     * @param ThemeResolver                $themeResolver
     */
    public function __construct(ContentTypeControllerManager $controllerManager, ThemeResolver $themeResolver)
    {
        $this->controllerManager = $controllerManager;
        $this->themeResolver = $themeResolver;
    }

    /**
     * {@inheritdoc}
     */
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        /** @var ContentTypePage $contentTypePage */
        $contentTypePage = $builder->getData();

        $builder->add('channel', ChannelChoiceType::class, [
            'useObject' => true,
            'disabled' => true,
        ]);

        $builder->add('path', TextType::class, [
            'label' => 'URL',
        ]);

        if (!preg_match('/Content\\\(.+)Controller$/', \get_class($options['controller']), $matchController)) {
            throw new \InvalidArgumentException(sprintf('The "%s" class does not look like a
            contentTypeController class (it must be in a "Controller\Content" sub-namespace and the
             class name must end with "Controller")', \get_class($options['controller'])));
        }
        if (!preg_match('/^(.+)Action$/', $contentTypePage->getControllerAction(), $matchAction)) {
            throw new \InvalidArgumentException(sprintf('The "%s" method does not look like an action method
             (it does not end with Action)', $contentTypePage->getControllerAction()));
        }

        $builder->add('layout', LayoutChoiceType::class, [
            'theme' => $this->themeResolver->getTheme($builder->getData()->getChannel()),
            'directory' => strtolower(sprintf('/content/%s/%s', $matchController[1], $matchAction[1])),
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
        ]);

        $resolver->setRequired('controller');

        $resolver->setAllowedTypes('controller', 'object');
    }

    /**
     * {@inheritdoc}
     */
    public function getBlockPrefix()
    {
        return 'integrated_page_content_type_page';
    }
}
