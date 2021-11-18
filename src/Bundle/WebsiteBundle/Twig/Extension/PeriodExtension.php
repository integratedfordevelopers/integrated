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

use Twig\Environment;
use Twig\Extension\AbstractExtension;
use Twig\TwigFunction;

/**
 * @author Koen Prins <koen@e-active.nl>
 */
class PeriodExtension extends AbstractExtension
{
    /**
     * @return array
     */
    public function getFunctions()
    {
        return [
            new TwigFunction(
                'integrated_period_formatter',
                [$this, 'periodFilter'],
                ['needs_environment' => true]
            ),
        ];
    }

    /**
     * @param Environment       $twig
     * @param \DateTime         $startDate
     * @param \DateTime         $endDate
     *
     * @return string
     */
    public function periodFilter(Environment $twig, $startDate, $endDate)
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
