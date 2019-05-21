<?php

/*
 * This file is part of the Integrated package.
 *
 * (c) e-Active B.V. <integrated@e-active.nl>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Integrated\Bundle\ContentBundle\Form\Type;

use Doctrine\Common\Persistence\ManagerRegistry;
use Integrated\Bundle\ContentBundle\Doctrine\ContentTypeManager;
use Integrated\Bundle\ContentBundle\Document\Content\Relation\Person;
use Integrated\Bundle\ContentBundle\Document\ContentType\ContentType;
use Integrated\Bundle\ContentBundle\Form\DataTransformer\AuthorTransformer;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\Form\FormInterface;
use Symfony\Component\Form\FormView;
use Symfony\Component\OptionsResolver\OptionsResolver;

/**
 * @author Jurre de Jongh <jurre@e-active.nl>
 */
class AuthorType extends AbstractType
{
    /**
     * @var ManagerRegistry
     */
    private $mr;

    /**
     * @var ContentTypeManager
     */
    private $contentTypeManager;

    /**
     * @param ManagerRegistry    $mr
     * @param ContentTypeManager $contentTypeManager
     */
    public function __construct(ManagerRegistry $mr, ContentTypeManager $contentTypeManager)
    {
        $this->mr = $mr;
        $this->contentTypeManager = $contentTypeManager;
    }

    /**
     * {@inheritdoc}
     */
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $transformer = new AuthorTransformer($this->mr);

        $builder->addModelTransformer($transformer);
    }

    public function buildView(FormView $view, FormInterface $form, array $options)
    {
        $contentTypes = [];

        foreach ($this->contentTypeManager->filterInstanceOf(Person::class) as $contentType) {
            if ($contentType instanceof ContentType) {
                $contentTypes[] = $contentType->getId();
            }
        }

        $view->vars['contentTypes'] = $contentTypes;
    }

    /**
     * {@inheritdoc}
     */
    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults([
            'multiple' => true,
        ]);
    }

    /**
     * {@inheritdoc}
     */
    public function getParent()
    {
        return TextType::class;
    }

    /**
     * {@inheritdoc}
     */
    public function getBlockPrefix()
    {
        return 'integrated_author';
    }
}
