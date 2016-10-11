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

use Symfony\Component\Form\Extension\Core\Type\BaseType;
use Symfony\Component\Form\FormBuilderInterface;

use Integrated\Bundle\ContentBundle\Doctrine\ContentTypeManager;
use Integrated\Bundle\ContentBundle\Document\Content\Relation\Company;

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

    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder->add('company', 'integrated_content_choice', [
            'label' => false,
            'params' => ['_format' => 'json', 'contenttypes' => $this->getContentTypes()]
        ]);

        $builder->add('function');
        $builder->add('department');
    }

    /**
     * Find all contentTypes that are instance of Company
     * @return array
     */
    protected function getContentTypes()
    {
        $contentTypes = $this->contentTypeManager->filterInstanceOf(Company::class);

        return array_map(function($contentType) { return $contentType->getId(); }, $contentTypes);
    }

    public function getName()
    {
        return 'integrated_company_job';
    }
}
