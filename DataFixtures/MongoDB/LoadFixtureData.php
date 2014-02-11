<?php

/*
 * This file is part of the Integrated package.
 *
 * (c) e-Active B.V. <integrated@e-active.nl>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Integrated\Bundle\ContentBundle\DataFixtures\MongoDB;

use Doctrine\Common\Persistence\ObjectManager;
use Doctrine\Common\DataFixtures\FixtureInterface;

use Integrated\Bundle\ContentBundle\Document\ContentType\Embedded\Field;

use Nelmio\Alice\Fixtures;

use Symfony\Component\DependencyInjection\ContainerAware;
use Symfony\Component\Finder\Finder;

/**
 * @author Jan Sanne Mulder <jansanne@e-active.nl>
 */
class LoadFixtureData extends ContainerAware implements FixtureInterface
{
	/**
	 * @inheritdoc
	 */
	public function load(ObjectManager $manager)
	{
		$files = array();

		foreach (Finder::create()->in(__DIR__ . DIRECTORY_SEPARATOR . 'alice')->name('*.yml') as $file) {
			$files[] = $file->getRealpath();
		}

		Fixtures::load($files, $manager, array('providers' => array($this)));
	}

	/**
	 * create a list of field based on the class.
	 *
	 * The field list will always reflect the current field configuration of the
	 * class. It is possible to supply a list of fields that are required and that
	 * should be ignored.
	 *
	 * @param $class
	 * @param array $required set the required flag for these fields
	 * @param array $ignore don't add these fields
	 * @return Field[]
	 */
	public function classfields($class, array $required = [], array $ignore = [])
	{
		$fields = array();

		/** @var \Integrated\Common\Content\Reader\Document $reader */
		$reader = $this->container->get('integrated_content.reader.document');
		$metadata = $reader->readAll();

		if (!isset($metadata[$class])) {
			return $fields;
		}

		$required = array_map('strtolower', $required);
		$ignore = array_map('strtolower', $ignore);

		/** @var \Integrated\Common\ContentType\Mapping\Metadata\ContentType $metadata */
		$metadata = $metadata[$class];

		foreach ($metadata->getFields() as $field) {
			if (in_array(strtolower($field->getName()), $ignore)) { continue; }

			$fields[$field->getName()] = (new Field())
					->setName($field->getName())
					->setType($field->getType())
					->setOptions($field->getOptions() + ['required' => in_array(strtolower($field->getName()), $required)]);
		}

		return $fields;
	}
}