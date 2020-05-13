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

use Doctrine\Bundle\MongoDBBundle\Form\Type\DocumentType;
use Doctrine\ODM\MongoDB\Repository\DocumentRepository;
use Integrated\Bundle\FormTypeBundle\Form\DataTransformer\CollectionToDocumentTransformer;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\Form\FormInterface;
use Symfony\Component\Form\FormView;
use Symfony\Component\OptionsResolver\Options;
use Symfony\Component\OptionsResolver\OptionsResolver;

/**
 * @author Johan Liefers <johan@e-active.nl>
 */
class RelationReferencesType extends AbstractType
{
    /**
     * {@inheritdoc}
     */
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $formType = $options['options']['form_type'];
        unset($options['options']['form_type']);

        $builder->add('references', $formType, $options['options']);

        if (!$options['options']['multiple']) {
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
            'options' => [],
        ]);

        $optionsNormalizer = function (Options $options, $value) {
            $commonDefaults = [
                'content_types' => [],
                'multiple' => false,
                'form_type' => DocumentType::class,
            ];

            $resolver = new OptionsResolver();
            $resolver->setDefined(array_keys($value));

            $resolver->setAllowedTypes('content_types', ['array']);

            if (!isset($value['content_types'])) {
                throw new \Exception('The option "content_types" is a required option for "integrated_relation_choice"');
            }

            $additionalDefaults = [];
            if (isset($value['form_type'])) {
                if ('integrated_content_choice' == $value['form_type']) {
                    $additionalDefaults = [
                        'params' => ['_format' => 'json', 'contenttypes' => $value['content_types']],
                        'allow_clear' => false,
                        'route' => null,
                    ];
                }
            } else {
                $additionalDefaults = [
                    'class' => 'Integrated\Bundle\ContentBundle\Document\Content\Content',
                    'attr' => ['class' => 'relation_select2'],
                    'query_builder' => function (DocumentRepository $dr) use ($value) {
                        return $dr->createQueryBuilder()
                            ->field('contentType')->in($value['content_types'])
                            ->field('disabled')->equals(false)
                            ->field('publishTime.startDate')->lte(new \DateTime())
                            ->field('publishTime.endDate')->gte(new \DateTime())
                            ->sort('title');
                    },
                ];
            }

            $resolver->setDefaults(array_merge($commonDefaults, $additionalDefaults));

            $options = $resolver->resolve($value);
            unset($options['content_types']);

            return $options;
        };

        $resolver->setNormalizer('options', $optionsNormalizer);
    }

    /**
     * {@inheritdoc}
     */
    public function getBlockPrefix()
    {
        return 'integrated_relation_references';
    }
}
