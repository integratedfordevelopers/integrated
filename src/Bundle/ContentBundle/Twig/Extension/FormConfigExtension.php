<?php

/*
 * This file is part of the Integrated package.
 *
 * (c) e-Active B.V. <integrated@e-active.nl>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Integrated\Bundle\ContentBundle\Twig\Extension;

use Integrated\Bundle\ContentBundle\Document\FormConfig\Embedded\Field\CustomField;
use Integrated\Bundle\ContentBundle\Document\FormConfig\Embedded\Field\DocumentField;
use Integrated\Bundle\ContentBundle\Document\FormConfig\Embedded\Field\RelationField;
use Integrated\Common\FormConfig\FormConfigFieldInterface;
use Symfony\Component\Form\Extension\Core\Type\TextareaType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Twig_Extension;
use Twig_SimpleFilter;

class FormConfigExtension extends Twig_Extension
{
    /**
     * {@inheritdoc}
     */
    public function getFilters()
    {
        return [
            new Twig_SimpleFilter('form_config_label', [$this, 'label']),
            new Twig_SimpleFilter('form_config_type_name', [$this, 'name']),
        ];
    }

    /**
     * @param mixed $value
     *
     * @return string
     */
    public function label($value): string
    {
        if (!$value instanceof FormConfigFieldInterface) {
            return '';
        }

        $label = $value->getOptions()['label'] ?? null;

        if ($label === null) {
            $label = ucfirst(strtolower(trim(preg_replace(
                ['/([A-Z])/', '/[_\s]+/'],
                ['_$1', ' '],
                $value->getName()
            ))));
        }

        return $label;
    }

    /**
     * @param mixed $value
     *
     * @return string
     */
    public function name($value): string
    {
        if ($value instanceof DocumentField) {
            return 'Content';
        }

        if ($value instanceof RelationField) {
            return 'Relation';
        }

        if ($value instanceof CustomField) {
            switch ($value->getType()) {
                case TextareaType::class:
                    return 'Custom (textarea)';

                case TextType::class:
                    return 'Custom (text)';
            }
        }

        return '';
    }
}
