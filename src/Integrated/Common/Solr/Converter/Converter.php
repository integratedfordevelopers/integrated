<?php

/*
 * This file is part of the Integrated package.
 *
 * (c) e-Active B.V. <integrated@e-active.nl>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Integrated\Common\Solr\Converter;

use Exception;

use Integrated\Common\Solr\Converter\Object\ObjectWrapper;
use Integrated\Common\Solr\Converter\Object\WrapperInterface;

use Symfony\Component\ExpressionLanguage\ExpressionLanguage;
use Symfony\Component\Security\Acl\Util\ClassUtils;

/**
 * @author Jan Sanne Mulder <jansanne@e-active.nl>
 */
class Converter implements ConverterInterface
{
    /**
     * @var ConverterSpecificationResolverInterface
     */
    protected $resolver;

    /**
     * @var ExpressionLanguage
     */
    private $el;

    public function __construct(
        ConverterSpecificationResolverInterface $resolver,
        ExpressionLanguage $expressionLanguage = null
    ) {
        $this->resolver = $resolver;
        $this->el = $expressionLanguage;
    }

    /**
     * @return ExpressionLanguage
     */
    protected function getExpressionLanguage()
    {
        if ($this->el === null) {
            $this->el = new ExpressionLanguage();
        }

        return $this->el;
    }

    /**
     * Convert the object to a solr document
     *
     * @param object $object
     * @return array|null
     */
    public function getFields($object)
    {
        $specs = $this->resolver->getSpecification(ClassUtils::getRealClass($object));

        if (!$specs) {
            return null;
        }

        $fields = [];
        $object = new ObjectWrapper($object);

        foreach (array_keys($specs->getFields()) as $field) {
            $fields[$field] = $this->getFieldValue($object, $field, $specs);
        }

        return $fields;
    }

    public function getField($object, $field)
    {
        $specs = $this->resolver->getSpecification(ClassUtils::getRealClass($object));

        if (!$specs) {
            return null;
        }

        return $this->getFieldValue(new ObjectWrapper($object), $field, $specs);
    }

    protected function getFieldValue(WrapperInterface $object, $field, ConverterSpecificationInterface $specs)
    {
        if (!$specs->hasField($field)) {
            return null;
        }

        $expression = $specs->getField($field);

        if ($expression === null) {
            $expression = 'data.' . $field . '.value()';
        } else {
            $expression = (string) $expression;

            $expression = str_replace('$', 'data', $expression);
            $expression = str_replace('[]', '.multi()', $expression);
        }

        try {
            return $this->getExpressionLanguage()->evaluate($expression, ['data' => $object, 'document' => $object]);
        } catch (Exception $e) {
            return null;
        }
    }

    /**
     * Convert the object to a unique id
     *
     * @param object $object
     * @return string|null
     */
    public function getId($object)
    {
        $specs = $this->resolver->getSpecification(ClassUtils::getRealClass($object));

        if (!$specs) {
            return null;
        }

        $id = $specs->getId();

        if ($id === null) {
            return null;
        }

        return $this->getFieldValue(new ObjectWrapper($object), $id, $specs);
    }
}
