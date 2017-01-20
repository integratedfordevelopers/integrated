<?php

/*
 * This file is part of the Integrated package.
 *
 * (c) e-Active B.V. <integrated@e-active.nl>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Integrated\Bundle\FormTypeBundle\Form\Type;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\Form\FormInterface;
use Symfony\Component\Form\FormView;
use Symfony\Component\OptionsResolver\OptionsResolver;

use Doctrine\ODM\MongoDB\DocumentManager;

use Integrated\Bundle\FormTypeBundle\Form\DataTransformer\ContentChoicesTransformer;
use Integrated\Bundle\FormTypeBundle\Form\DataTransformer\ContentChoiceTransformer;

/**
 * @author Johan Liefers <johan@e-active.nl>
 */
class ContentChoiceType extends AbstractType
{
    /**
     * @var DocumentManager
     */
    protected $dm;

    /**
     * @var string
     */
    protected $repositoryClass;

    /**
     * @var string
     */
    protected $route;

    /**
     * @var array|null
     */
    protected $params;

    /**
     * @param DocumentManager $dm
     * @param string $repositoryClass
     * @param string $route
     * @param array|null $params
     */
    public function __construct(DocumentManager $dm, $repositoryClass, $route, array $params = null)
    {
        $this->dm = $dm;
        $this->repositoryClass = $repositoryClass;
        $this->route = $route;
        $this->params = $params;
    }

    /**
     * {@inheritdoc}
     */
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        if ($options['multiple']) {
            $builder->addViewTransformer(
                new ContentChoicesTransformer($this->dm->getRepository($options['repository_class'])),
                true
            );
        } else {
            $builder->addViewTransformer(
                new ContentChoiceTransformer($this->dm->getRepository($options['repository_class'])),
                true
            );
        }
    }

    /**
     * {@inheritdoc}
     */
    public function buildView(FormView $view, FormInterface $form, array $options)
    {
        $varNames = ['multiple', 'route', 'params', 'allow_clear'];
        foreach ($varNames as $varName) {
            $view->vars[$varName] = $options[$varName];
        }

        if ($options['multiple']) {
            $view->vars['full_name'] .= '[]';
            $view->vars['attr']['multiple'] = 'multiple';
        }
        $view->vars['attr']['data-placeholder'] = $options['placeholder'];
    }

    /**
     * {@inheritdoc}
     */
    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults([
            'repository_class' => $this->repositoryClass, // repository for finding the contentItems, default: IntegratedContentBundle:Content\Content
            'route' => $this->route, // api route for getting the ajax results, default: integrated_content_content_index
            'params' => $this->params, // additional parameters for the api route, default: ['_format' => 'json']
            'multiple' => true,
            'compound' => false,
            'required' => false,
            'placeholder' => null,
            'allow_clear' => false, // if set to true the user is able to clear the selection
        ]);
    }

    /**
     * {@inheritdoc}
     */
    public function getBlockPrefix()
    {
        return 'integrated_content_choice';
    }
}
