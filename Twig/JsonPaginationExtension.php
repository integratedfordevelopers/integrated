<?php

namespace Integrated\Bundle\ContentBundle\Twig;

use Symfony\Component\Routing\Generator\UrlGeneratorInterface;
use Knp\Bundle\PaginatorBundle\Pagination\SlidingPagination;

class JsonPaginationExtension extends \Twig_Extension
{
    /**
     * @var UrlGeneratorInterface
     */
    protected $generator;

    public function __construct(UrlGeneratorInterface $generator)
    {
        $this->generator = $generator;
    }


    public function getFilters()
    {
        return array(
            new \Twig_SimpleFilter('json_pagination', array($this, 'paginate', array('is_safe' => 'json')))
        );
    }



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