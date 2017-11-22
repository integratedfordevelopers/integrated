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

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormBuilderInterface;

use Doctrine\ODM\MongoDB\DocumentManager;

use Integrated\Bundle\ContentBundle\Form\DataTransformer\ImageTransformer;

/**
 * @author Ger Jan van den Bosch <gerjan@e-active.nl>
 */
class ImageChoiceType extends AbstractType
{
    /**
     * @var \Doctrine\ODM\MongoDB\DocumentRepository
     */
    private $repository;

    /**
     * @param DocumentManager $manager
     */
    public function __construct(DocumentManager $manager)
    {
        $this->repository = $manager->getRepository('IntegratedContentBundle:Content\Image');
    }

    /**
     * {@inheritdoc}
     */
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder->addModelTransformer(new ImageTransformer($this->repository));
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
        return 'integrated_image_choice';
    }
}
