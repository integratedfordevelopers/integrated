<?php

/*
 * This file is part of the Integrated package.
 *
 * (c) e-Active B.V. <integrated@e-active.nl>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Integrated\Bundle\FormTypeBundle\Form\Type\RelationChoice;

use Integrated\Bundle\FormTypeBundle\Form\DataTransformer\CollectionToDocumentTransformer;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\Form\FormInterface;
use Symfony\Component\Form\FormView;
use Symfony\Component\OptionsResolver\OptionsResolver;

use Doctrine\ODM\MongoDB\DocumentRepository;

use Integrated\Bundle\ContentBundle\Document\Content\Embedded\Relation;

/**
 * @author Johan Liefers <johan@e-active.nl>
 */
class RelationType extends AbstractType
{
    /**
     * @var array
     */
    protected $contentTypes;

    /**
     * {@inheritdoc}
     */
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $this->contentTypes = $options['contentTypes'];

        $commonOptions = [
            'label' => $options['label'],
            'multiple' => $options['multiple'],
            'required' => $options['required'],
            'attr' => $options['attr'] ?: ['class' => 'relation_select2'],
        ];

        if ($options['ajax']) {
            $builder->add('references', 'integrated_content_choice', array_merge($commonOptions, [
                'allow_clear' => $options['allow_clear'],
                'route' => $options['route'],
                'params' => $options['params'] ?: ['_format' => 'json', 'contenttypes' => $this->contentTypes],
            ]));
        } else {
            $builder->add('references', 'document', array_merge($commonOptions, [
                'class' => 'Integrated\Bundle\ContentBundle\Document\Content\Content',
                'query_builder' => function (DocumentRepository $dr) {
                    return $dr->createQueryBuilder()
                        ->field('contentType')->in($this->contentTypes);
                },
            ]));
        }

        if (!$options['multiple']) {
            $builder->get('references')->addModelTransformer(new CollectionToDocumentTransformer(), true);
        }

    }

    /**
     * {@inheritdoc}
     */
    public function buildView(FormView $view, FormInterface $form, array $options)
    {
        //label is rendered with the reference
        $view->vars = array_replace($view->vars, ['label' => false]);
    }

    /**
     * {@inheritdoc}
     */
    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults([
            'data_class' => 'Integrated\Bundle\ContentBundle\Document\Content\Embedded\Relation',
            'contentTypes' => [],
            'label' => false,
            'multiple' => true,
            'ajax' =>  false,
            'route' => null, //only for ajax
            'params' => [], //only for ajax
            'allow_clear' => false, //only for ajax
        ]);
    }

    /**
     * {@inheritdoc}
     */
    public function getName()
    {
        return 'integrated_relation_choice';
    }
}
