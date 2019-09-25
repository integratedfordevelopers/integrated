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

use Doctrine\ODM\MongoDB\DocumentManager;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\Form\FormInterface;
use Symfony\Component\Form\FormView;
use Symfony\Component\OptionsResolver\OptionsResolver;

class ContentRankType extends AbstractType
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
     * @param string          $repositoryClass
     * @param string          $route
     * @param array|null      $params
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
    public function buildView(FormView $view, FormInterface $form, array $options)
    {
        $varNames = ['route', 'params', 'allow_clear'];
        foreach ($varNames as $varName) {
            $view->vars[$varName] = $options[$varName];
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
        return 'integrated_content_rank';
    }
}
