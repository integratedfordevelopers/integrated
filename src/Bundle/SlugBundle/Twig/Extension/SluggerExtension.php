<?php

/*
 * This file is part of the Integrated package.
 *
 * (c) e-Active B.V. <integrated@e-active.nl>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Integrated\Bundle\SlugBundle\Twig\Extension;

use Integrated\Bundle\SlugBundle\Slugger\SluggerInterface;

/**
 * @author Ger Jan van den Bosch <gerjan@e-active.nl>
 */
class SluggerExtension extends \Twig_Extension
{
    /**
     * @var SluggerInterface
     */
    private $slugger;

    /**
     * @param SluggerInterface $slugger
     */
    public function __construct(SluggerInterface $slugger)
    {
        $this->slugger = $slugger;
    }

    /**
     * {@inheritdoc}
     */
    public function getFilters()
    {
        return [
            new \Twig_SimpleFilter('slugify', [$this, 'slugify']),
        ];
    }

    /**
     * @param string $string
     * @param string $delimiter
     *
     * @return string
     */
    public function slugify($string, $delimiter = '-')
    {
        return $this->slugger->slugify($string, $delimiter);
    }

    /**
     * {@inheritdoc}
     */
    public function getName()
    {
        return 'integrated_content_slugger_extension';
    }
}
