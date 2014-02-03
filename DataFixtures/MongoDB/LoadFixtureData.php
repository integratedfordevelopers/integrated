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

use Nelmio\Alice\Fixtures;

use Symfony\Component\Finder\Finder;

/**
 * @author Jan Sanne Mulder <jansanne@e-active.nl>
 */
class LoadFixtureData implements FixtureInterface
{
	/**
	 * @inheritdoc
	 */
	function load(ObjectManager $manager)
	{
		$files = array();

		foreach (Finder::create()->in(__DIR__ . DIRECTORY_SEPARATOR . 'alice')->name('*.yml') as $file) {
			$files[] = $file->getRealpath();
		}

		Fixtures::load($files, $manager, array('providers' => array($this)));
	}
}