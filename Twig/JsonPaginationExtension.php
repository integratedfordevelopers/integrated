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
            'pageCount' => ceil($pagination->getTotalItemCount() / $pagination->getItemNumberPerPage()),
            'numFound' => $pagination->getTotalItemCount(),
            'numItemsPerPage' => $pagination->getItemNumberPerPage(),
            'next' => null,
            'previous' => null,
        );


        $current = $pagination->getCurrentPageNumber();
        $params = $pagination->getQuery(array('_format' => 'json'));

        if ($current - 1 > 0) {
            $return['previous'] = array(
                    'href' => $this->generator->generate(
                        $pagination->getRoute(),
                        array_merge(
                            $params,
                            array($pagination->getPaginatorOption('pageParameterName') => $current - 1)
                        )
                    )
            );
        }

        if ($current + 1 <= $return['pageCount']) {

            $return['next'] = array(
                'href' => $this->generator->generate(
                        $pagination->getRoute(),
                        array_merge(
                            $params,
                            array($pagination->getPaginatorOption('pageParameterName') => $current + 1)
                        )
                    )
            );
        }

        foreach ($pagination as $page) {
            echo get_class($page);
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