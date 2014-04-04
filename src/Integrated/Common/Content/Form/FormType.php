<?php

/*
 * This file is part of the Integrated package.
 *
 * (c) e-Active B.V. <integrated@e-active.nl>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Integrated\Common\Content\Form;

use Integrated\Common\Content\Reader;
use Integrated\Common\ContentType\ContentTypeInterface;

use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\Form\FormInterface;
use Symfony\Component\Form\FormView;

use Symfony\Component\OptionsResolver\OptionsResolverInterface;

/**
 * @author Jan Sanne Mulder <jansanne@e-active.nl>
 */
class FormType implements FormTypeInterface
{
    /**
     * @var ContentTypeInterface
     */
    protected $contentType;

    /**
     * @var Reader\Document
     */
    protected $reader;

	/**
	 * @var string
	 */
	protected $name = null;

	/**
	 * @param ContentTypeInterface $contentType
     * @param Reader\Document $reader
	 */
	public function __construct(ContentTypeInterface $contentType, Reader\Document $reader)
	{
		$this->contentType = $contentType;
        $this->reader = $reader;
	}

	/**
	 * {@inheritdoc}
	 */
	public function buildForm(FormBuilderInterface $builder, array $options)
	{
        $documents = $this->reader->readAll();
        if (!isset($documents[$this->contentType->getClass()])) {
            // TODO throw exception
        }

        /* @var $document \Integrated\Common\ContentType\Mapping\Metadata\ContentType */
        $document = $documents[$this->contentType->getClass()];

        foreach ($document->getFields() as $field) {
            if ($this->contentType->hasField($field->getName())) {
                $field = $this->contentType->getField($field->getName());
                $builder->add(
                    $builder->create($field->getName(), $field->getType(), $field->getOptions())
                );
            }
        }

        $builder->add(
            'relations',
            new RelationType($this->contentType->getRelations())
        );

		// submit buttons
		$builder->add('save', 'submit');
		$builder->add('back', 'submit');
	}

	/**
	 * {@inheritdoc}
	 */
	public function buildView(FormView $view, FormInterface $form, array $options)
	{
	}

	/**
	 * {@inheritdoc}
	 */
	public function finishView(FormView $view, FormInterface $form, array $options)
	{
	}

	/**
	 * {@inheritdoc}
	 */
	public function setDefaultOptions(OptionsResolverInterface $resolver)
	{
		$resolver->setDefaults(array(
			'data_class' => $this->contentType->getClass(),
			'empty_data' => function(FormInterface $form) {
				return $this->contentType->create();
			},
		));
	}

	/**
	 * {@inheritdoc}
	 */
	public function getType()
	{
		return $this->contentType;
	}

	/**
	 * {@inheritdoc}
	 */
	public function getParent()
	{
		return 'form';
	}

	/**
	 * {@inheritdoc}
	 */
	public function getName()
	{
		if (null === $this->name) {
			$this->name = preg_replace('#[^a-zA-Z0-9\-_]#', '_', $this->contentType->getClass() . '__' . $this->contentType->getType());
			$this->name = strtolower($this->name);
		}

		return $this->name;
	}
}