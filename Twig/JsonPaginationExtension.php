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

use Symfony\Component\Routing\Generator\UrlGeneratorInterface;
use Knp\Bundle\PaginatorBundle\Pagination\SlidingPagination;

/**
 * @author Jeroen van Leeuwen <jeroen@e-active.nl>
 */
class JsonPaginationExtension extends \Twig_Extension
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
        return array(
            new \Twig_SimpleFilter('json_pagination', array($this, 'paginate', array('is_safe' => 'json')))
        );
    }

    /**
     * @param SlidingPagination $pagination
     * @return array
     */
    public function paginate(SlidingPagination $pagination)
    {
        $paginationData = $pagination->getPaginationData();

        $return = array(
            'page' => $pagination->getCurrentPageNumber(),
            'pageCount' => $paginationData['pageCount'],
            'numFound' => $pagination->getTotalItemCount(),
            'numItemsPerPage' => $pagination->getItemNumberPerPage(),
            'next' => null,
            'previous' => null,
            'pages' => array()
        );

        $params = $pagination->getQuery(array('_format' => 'json'));

        if (isset($paginationData['previous'])) {
            $href = $this->generator->generate(
                $pagination->getRoute(),
                array_merge(
                    $params,
                    array($pagination->getPaginatorOption('pageParameterName') => $paginationData['previous'])
                )
            );
            $return['previous'] = array('href' => $href);
        }

        if (isset($paginationData['next'])) {
            $href = $this->generator->generate(
                $pagination->getRoute(),
                array_merge(
                    $params,
                    array($pagination->getPaginatorOption('pageParameterName') => $paginationData['next'])
                )
            );

            $return['next'] = array('href' => $href);
        }

        if ($paginationData['pageCount'] > 0) {
            foreach ($paginationData['pagesInRange'] as $page) {
                $href = $this->generator->generate(
                    $pagination->getRoute(),
                    array_merge(
                        $params,
                        array($pagination->getPaginatorOption('pageParameterName') => $page)
                    )
                );

                $return['pages'][$page] = array('href' => $href);
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