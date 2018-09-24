<?php

/*
 * This file is part of the Integrated package.
 *
 * (c) e-Active B.V. <integrated@e-active.nl>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Integrated\Bundle\WebsiteBundle\Twig\Extension;

/**
 * @author Koen Prins <koen@e-active.nl>
 */
class PeriodExtension extends \Twig_Extension
{
    /**
     * @return array
     */
    public function getFunctions()
    {
        return [
            new \Twig_SimpleFunction(
                'integrated_period_formatter',
                [$this, 'periodFilter'],
                ['needs_environment' => true]
            ),
        ];
    }

    /**
     * @param \Twig_Environment $twig
     * @param \DateTime         $startDate
     * @param \DateTime         $endDate
     *
     * @return string
     */
    public function periodFilter(\Twig_Environment $twig, $startDate, $endDate)
    {
        $filter = $twig->getFilter('localizeddate');

        $period = \call_user_func($filter->getCallable(), $twig, $startDate, 'long', 'short');

        if ($endDate) {
            $period .= ' - ';
            $dateFormat = ($startDate->format('Ymd') == $endDate->format('Ymd') ? 'none' : 'long');
            $period .= \call_user_func($filter->getCallable(), $twig, $endDate, $dateFormat, 'short');
        }

        return $period;
    }
}
