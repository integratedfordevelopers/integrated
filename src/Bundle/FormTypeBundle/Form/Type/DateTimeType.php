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

use Integrated\Bundle\AssetBundle\Manager\AssetManager;
use Integrated\Bundle\FormTypeBundle\Form\DataTransformer\DateTime;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormBuilderInterface;

/**
 * @author Björn Borneman <bjorn@e-active.nl>
 */
class DateTimeType extends AbstractType
{
    /**
     * @var AssetManager
     */
    protected $styleSheetManager;

    /**
     * @var AssetManager
     */
    protected $javascriptManager;

    /**
     * @param AssetManager $styleSheetManager
     * @param AssetManager $javascriptManager
     */
    public function __construct(AssetManager $styleSheetManager, AssetManager $javascriptManager)
    {
        $this->styleSheetManager = $styleSheetManager;
        $this->javascriptManager = $javascriptManager;
    }

    /**
     * {@inheritdoc}
     */
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder->addModelTransformer(new DateTime());
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
        return 'integrated_datetime';
    }
}
