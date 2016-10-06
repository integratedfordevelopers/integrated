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
use Integrated\Bundle\ContentBundle\Document\Content\Relation\Company;
use Symfony\Component\Form\Extension\Core\EventListener\ResizeFormListener;
use Symfony\Component\Form\Extension\Core\Type\BaseType;

use Symfony\Component\Form\FormBuilderInterface;

/**
 * @author Johan Liefers <johan@e-active.nl>
 */
class CompanyJobType extends BaseType
{
    /**
     * @var ManagerRegistry
     */
    protected $mr;

    public function __construct(ManagerRegistry $mr)
    {
        $this->mr = $mr;
    }

    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder->add('company', 'integrated_content_choice', [
            'label' => false,
            'params' => ['_format' => 'json', 'contenttypes' => $this->getContentTypes()]
        ]);

//        $subForm = $builder->create('job');
//        $subForm->add('function');
//        $subForm->add('department');
//
//        $builder->addEventSubscriber(new ResizeFormListener($subForm, [], true, true));
    }

    /**
     * Find all contentTypes that are instance of Company
     * @return array
     */
    protected function getContentTypes()
    {
        $contentTypes = $this->mr->getManager()->getRepository('IntegratedContentBundle:ContentType\ContentType')->findAll();
        $contentTypeNames = [];

        foreach ($contentTypes as $contentType) {
            if (is_a($contentType->getClass(), Company::class, true)) {
                $contentTypeNames[] = $contentType->getId();
            }
        }

        return $contentTypeNames;
    }
//
//    public function configureOptions(OptionsResolver $resolver)
//    {
//        $resolver->setDefault('label', false);
//    }

    public function getName()
    {
        return 'integrated_company_job';
    }
}
