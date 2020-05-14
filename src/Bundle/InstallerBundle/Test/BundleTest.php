<?php

namespace Integrated\Bundle\InstallerBundle\Test;

use Symfony\Component\Finder\Finder;

class BundleTest
{
    const BUNDLES_DIRECTORY = '/../../';

    /**
     * @var array
     */
    private $bundles;

    /**
     * Migrations constructor.
     *
     * @param array $bundles
     */
    public function __construct(array $bundles)
    {
        $this->bundles = $bundles;
    }

    /**
     * @return array
     */
    public function execute()
    {
        $directory = realpath(__DIR__ . self::BUNDLES_DIRECTORY);

        $finder = new Finder();

        $finder->directories()->in($directory)->depth(0);

        $errors = [];
        foreach ($finder as $directory) {
            $directory = $directory->getFilename();
            if (isset($this->bundles['Integrated'.$directory])) {
                //bundle found
                continue;
            }
            $errors[] = 'Integrated'.$directory.' has not been loaded';
        }

        return $errors;
    }
}
