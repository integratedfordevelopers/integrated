<?php

/*
 * This file is part of the Integrated package.
 *
 * (c) e-Active B.V. <integrated@e-active.nl>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Integrated\Bundle\ThemeBundle\Scraper;

use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\Cache\Adapter\ApcuAdapter;
use Twig\Loader\LoaderInterface;

class ScraperPageLoader implements LoaderInterface
{
    /**
     * @var EntityManagerInterface
     */
    private $entityManager;

    /**
     * @var ApcuAdapter
     */
    private $cache;

    /**
     * @var Array
     */
    private $pages = null;

    public function __construct(EntityManagerInterface $entityManager)
    {

        $this->entityManager = $entityManager;
        $this->cache = new ApcuAdapter('integrated.template.scraper');
    }

    public function getSourceContext($name)
    {
        if (false === $source = $this->getValue('source', $name)) {
            throw new \Twig\Error\LoaderError(sprintf('Template "%s" does not exist.', $name));
        }

        return new \Twig\Source($source, $name);
    }

    public function exists($name)
    {
        return false;
        return $name === $this->getValue('name', $name);
    }

    public function getCacheKey($name)
    {
        return $name;
    }

    public function isFresh($name, $time)
    {
        if (false === $lastModified = $this->getValue('last_modified', $name)) {
            return false;
        }

        return $lastModified <= $time;
    }

    protected function getValue($column, $name)
    {
        $sth = $this->dbh->prepare('SELECT '.$column.' FROM templates WHERE name = :name');
        $sth->execute([':name' => (string) $name]);

        return $sth->fetchColumn();
    }
}
