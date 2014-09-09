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

use Doctrine\Common\Persistence\ObjectManager;
use Integrated\Bundle\FormTypeBundle\Form\DataTransformer\Author;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\HttpFoundation\Request;

class AuthorType extends AbstractType
{
    /**
     * @var ObjectManager
     */
    private $om;

    /**
     * @var Request
     */
    private $request;

    /**
     * @param $om
     * @param Request $request
     */
    public function __construct($om, Request $request)
    {
        $this->om      = $om;
        $this->request = $request;
    }

    /**
     * {@inheritdoc}
     */
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $transformer = new Author($this->om, $this->request);
        $builder->addModelTransformer($transformer);
    }

    /**
     * {@inheritdoc}
     */
    public function getParent()
    {
        return 'text';
    }

    /**
     * {@inheritdoc}
     */
    public function getName()
    {
        return 'integrated_author';
    }
}