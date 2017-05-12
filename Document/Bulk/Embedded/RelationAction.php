<?php

/*
* This file is part of the Integrated package.
*
* (c) e-Active B.V. <integrated@e-active.nl>
*
* For the full copyright and license information, please view the LICENSE
* file that was distributed with this source code.
*/

namespace Integrated\Bundle\ContentBundle\Document\Bulk\Embedded;

use Doctrine\Common\Collections\ArrayCollection;
use Integrated\Bundle\ContentBundle\Bulk\ActionInterface;
use Integrated\Bundle\ContentBundle\Document\Content\Article;
use Integrated\Bundle\ContentBundle\Document\Content\Content;
use Integrated\Bundle\ContentBundle\Document\Content\File;
use Integrated\Bundle\ContentBundle\Document\Content\Relation\Company;
use Integrated\Bundle\ContentBundle\Document\Content\Relation\Person;
use Integrated\Bundle\ContentBundle\Document\Content\Taxonomy;
use Integrated\Bundle\ContentBundle\Document\Relation\Relation;
use Integrated\Bundle\ContentBundle\Form\Type\Bulk\Fields\ReferencesChoiceType;
use Integrated\Bundle\ContentBundle\Validator\Constraints\ContainsLegitReferences;

abstract class RelationAction implements ActionInterface
{
    /**
     * @var string
     */
    protected $id;

    /**
     * @var Relation
     */
    protected $relation;

    /**
     * @var ArrayCollection
     */
    protected $references;

    /**
     * RelationAction constructor.
     * @param Relation $relation
     */
    public function __construct(Relation $relation)
    {
        $this->relation = $relation;
        $this->references = new ArrayCollection();
    }

    /**
     * @return Relation
     */
    public function getRelation()
    {
        return $this->relation;
    }

    /**
     * @param Relation $relation
     * @return $this
     */
    public function setRelation(Relation $relation)
    {
        $this->relation = $relation;
        return $this;
    }

    /**
     * @return ArrayCollection
     */
    public function getReferences()
    {
        return $this->references;
    }

    /**
     * @param ArrayCollection $references
     * @return $this
     */
    public function setReferences(ArrayCollection $references)
    {
        $this->references = $references;
        return $this;
    }

    /**
     * @return string
     */
    public function getTargetName()
    {
        return $this->getRelation()->getName();
    }

    /**
     * @return array
     */
    public function getChangeNames()
    {
        $changeNames = [];

        foreach ($this->getReferences() as $reference) {
            if ($reference instanceof Content) {
                if ($reference instanceof Article || $reference instanceof Taxonomy || $reference instanceof File) {
                    $changeNames[] = $reference->getTitle();
                } elseif ($reference instanceof Person) {
                    $changeNames[] = $reference->getFirstName() . " " . $reference->getLastName();
                } elseif ($reference instanceof Company) {
                    $changeNames[] = $reference->getName();
                } else {
                    $changeNames[] = $reference->getId();
                }
            }
        }

        return $changeNames;
    }

    /**
     * @return array
     */
    public function getFieldsPreBuildConfig()
    {
        $contentTypes = [];

        foreach ($this->relation->getTargets() as $contentType) {
            $contentTypes[] = [
                'type' => $contentType->getType(),
                'name' => $contentType->getName(),
            ];
        }

        $references = [];

        foreach ($this->getReferences() as $reference) {
            if ($reference instanceof Content) {
                $references[$reference->getId()] = $reference;
            }
        }

        $configs['references'] = [
            'field_remove' => true,
            'field_name' => 'references',
            'field_type' => ReferencesChoiceType::class,
            'field_options' => [
                'label' => $this->getTypeOfAction() . " " . $this->getTargetName(),
                'choices' => $references,
                'attr' => [
                    'class' => 'relation-items integrated_select2',
                    'data-multiple' => 1,
                    'data-id' => $this->relation->getId(),
                    'data-types' => json_encode($contentTypes),
                ],
            ]
        ];

        return $configs;
    }

    /**
     * @param array $data
     * @return array
     */
    public function getFieldsPostBuildConfig($data)
    {
        $choices = [];

        if (isset($data['references'])) {
            $choices = is_array($data['references']) ? array_combine($data['references'], $data['references']) : $data['references'];
        }

        $configs['references'] = [
            'field_remove' => true,
            'field_name' => 'references',
            'field_type' => ReferencesChoiceType::class,
            'field_options' => [
                'label' => $this->getTypeOfAction() . " " . $this->getTargetName(),
                'choices' => $choices,
                'constraints' => new ContainsLegitReferences([
                    'relation' => $this->relation
                ])
            ]
        ];

        return $configs;
    }
}