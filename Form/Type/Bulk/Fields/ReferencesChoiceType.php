<?php

/*
 * This file is part of the Integrated package.
 *
 * (c) e-Active B.V. <integrated@e-active.nl>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Integrated\Bundle\ContentBundle\Form\Type\Bulk\Fields;

use Doctrine\ODM\MongoDB\DocumentManager;
use Integrated\Bundle\ContentBundle\Document\Bulk\Action\RelationAction;
use Integrated\Bundle\ContentBundle\Form\DataTransformer\ReferencesToArrayTransformer;
use Integrated\Common\Content\Form\Event\FormEvent;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\Form\FormEvents;
use Symfony\Component\OptionsResolver\OptionsResolver;

/**
 * @author Patrick Mestebeld <patrick@e-active.nl>
 */
class ReferencesChoiceType extends AbstractType
{
    /**
     * @var DocumentManager
     */
    protected $dm;

    /**
     * ReferencesType constructor.
     * @param DocumentManager $dm
     */
    public function __construct(DocumentManager $dm)
    {
        $this->dm = $dm;
    }

    /**
     * @param FormBuilderInterface $builder
     * @param array $options
     */
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $transformer = new ReferencesToArrayTransformer($this->dm);
        $builder->addModelTransformer($transformer);
    }

    /**
     * @param OptionsResolver $resolver
     */
    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults([
            'multiple' => true,
            'required' => false,
        ]);
    }

    /**
     * @return mixed
     */
    public function getParent()
    {
        return ChoiceType::class;
    }
}
