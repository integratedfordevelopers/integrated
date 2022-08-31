<?php

/*
 * This file is part of the Integrated package.
 *
 * (c) e-Active B.V. <integrated@e-active.nl>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Integrated\Bundle\SlugBundle\Slugger;

/**
 * Utility to generate a slug.
 *
 * @author Ger Jan van den Bosch <gerjan@e-active.nl>
 */
class Slugger implements SluggerInterface
{
    /**
     * {@inheritdoc}
     */
    public function slugify($string, $delimiter = '-')
    {
        if (!$string) {
            return '';
        }

        $locale = setlocale(\LC_ALL, 0);

        setlocale(\LC_ALL, 'en_US.UTF-8');

        $slug = strtolower(
            trim(preg_replace('/[^a-zA-Z0-9\/_|+ -.]/', '', iconv('UTF-8', 'ASCII//TRANSLIT', $string)), $delimiter)
        );
        $slug = preg_replace('/[\/_|+ -.]+/', $delimiter, $slug);
        $slug = trim($slug, $delimiter);

        setlocale(\LC_ALL, $locale); // restore

        return $slug;
    }
}
