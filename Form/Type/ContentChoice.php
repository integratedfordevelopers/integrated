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

use Integrated\Bundle\FormTypeBundle\Form\DataTransformer\AjaxDocumentTransformer;

/**
 * @author Johan Liefers <johan@e-active.nl>
 */
class ContentChoice extends AbstractType
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
     * AjaxSelect2Type constructor.
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
        $builder->addViewTransformer(new AjaxDocumentTransformer($this->dm, $options), true);
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
            'repositoryClass' => $this->repositoryClass,
            'route' => $this->route,
            'params' => $this->params,
            'multiple' => true,
            'compound' => false,
            'required' => false,
            'placeholder' => null,
            'allow_clear' => false
        ]);
    }

    /**
     * {@inheritdoc}
     */
    public function getName()
    {
        return 'integrated_content_choice';
    }
}
