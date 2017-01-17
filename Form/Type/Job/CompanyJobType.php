<?php

/*
 * This file is part of the Integrated package.
 *
 * (c) e-Active B.V. <integrated@e-active.nl>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Integrated\Bundle\ContentBundle\Form\Type\Job;

use Integrated\Bundle\FormTypeBundle\Form\Type\ContentChoiceType;
use Symfony\Component\Form\Extension\Core\Type\BaseType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

use Integrated\Bundle\ContentBundle\Doctrine\ContentTypeManager;
use Integrated\Bundle\ContentBundle\Document\Content\Relation\Company;
use Integrated\Bundle\ContentBundle\Document\Content\Embedded\Job;

/**
 * @author Johan Liefers <johan@e-active.nl>
 */
class CompanyJobType extends BaseType
{
    /**
     * @var ContentTypeManager
     */
    private $contentTypeManager;

    /**
     * @param ContentTypeManager $contentTypeManager
     */
    public function __construct(ContentTypeManager $contentTypeManager)
    {
        $this->contentTypeManager = $contentTypeManager;
    }

    /**
     * @param FormBuilderInterface $builder
     * @param array $options
     */
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder->add('company', ContentChoiceType::class, [
            'params' => ['_format' => 'json', 'contenttypes' => $this->getContentTypes()],
            'multiple' => false,
        ]);

        $builder->add('function');
        $builder->add('department');
    }

    /**
     * Find all contentTypes (ids) that are instance of Company
     * @return array
     */
    protected function getContentTypes()
    {
        $contentTypes = $this->contentTypeManager->filterInstanceOf(Company::class);

        return array_map(function ($contentType) {
            return $contentType->getId();
        }, $contentTypes);
    }

    /**
     * @param OptionsResolver $resolver
     */
    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefault('data_class', Job::class);
    }

    /**
     * @return string
     */
    public function getBlockPrefix()
    {
        return 'integrated_company_job';
    }
}
