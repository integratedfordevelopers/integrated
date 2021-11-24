<?php

/*
 * This file is part of the Integrated package.
 *
 * (c) e-Active B.V. <integrated@e-active.nl>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Integrated\Bundle\ContentBundle\Twig;

use Knp\Bundle\PaginatorBundle\Pagination\SlidingPagination;
use Symfony\Component\Routing\Generator\UrlGeneratorInterface;
use Twig\Extension\AbstractExtension;
use Twig\TwigFilter;

/**
 * @author Jeroen van Leeuwen <jeroen@e-active.nl>
 */
class JsonPaginationExtension extends AbstractExtension
{
    /**
     * @var UrlGeneratorInterface
     */
    protected $generator;

    /**
     * @param UrlGeneratorInterface $generator
     */
    public function __construct(UrlGeneratorInterface $generator)
    {
        $this->generator = $generator;
    }

    /**
     * {@inheritdoc}
     */
    public function getFilters()
    {
        return [
            new TwigFilter('json_pagination', [$this, 'paginate', ['is_safe' => 'json']]),
        ];
    }

    /**
     * @param SlidingPagination $pagination
     *
     * @return array
     */
    public function paginate(SlidingPagination $pagination)
    {
        $paginationData = $pagination->getPaginationData();

        $return = [
            'page' => $pagination->getCurrentPageNumber(),
            'pageCount' => $paginationData['pageCount'],
            'numFound' => $pagination->getTotalItemCount(),
            'numItemsPerPage' => $pagination->getItemNumberPerPage(),
            'next' => null,
            'previous' => null,
            'pages' => [],
        ];

        $params = $pagination->getQuery(['_format' => 'json']);

        if (isset($paginationData['previous'])) {
            $href = $this->generator->generate(
                $pagination->getRoute(),
                array_merge(
                    $params,
                    [$pagination->getPaginatorOption('pageParameterName') => $paginationData['previous']]
                )
            );
            $return['previous'] = ['href' => $href];
        }

        if (isset($paginationData['next'])) {
            $href = $this->generator->generate(
                $pagination->getRoute(),
                array_merge(
                    $params,
                    [$pagination->getPaginatorOption('pageParameterName') => $paginationData['next']]
                )
            );

            $return['next'] = ['href' => $href];
        }

        if ($paginationData['pageCount'] > 0) {
            foreach ($paginationData['pagesInRange'] as $page) {
                $href = $this->generator->generate(
                    $pagination->getRoute(),
                    array_merge(
                        $params,
                        [$pagination->getPaginatorOption('pageParameterName') => $page]
                    )
                );

                $return['pages'][$page] = ['href' => $href];
            }
        }

        return $return;
    }

    /**
     * {@inheritdoc}
     */
    public function getName()
    {
        return 'integrated_content_json_pagination_extension';
    }
}
