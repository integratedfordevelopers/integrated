<?php

/*
 * This file is part of the Integrated package.
 *
 * (c) e-Active B.V. <integrated@e-active.nl>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Integrated\Bundle\ContentBundle\Controller;

use Integrated\Bundle\ContentBundle\Solr\Query\SuggestionQuery;
use Solarium\Client;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\DependencyInjection\ContainerInterface;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Serializer\Serializer;

/**
 * @author Jan Sanne Mulder <jansanne@e-active.nl>
 */
class SearchController extends AbstractController
{
    /**
     * @var Client
     */
    protected $client;

    /**
     * @var Serializer
     */
    protected $serializer;

    /**
     * @param Client             $client
     * @param Serializer         $serializer
     * @param ContainerInterface $container
     */
    public function __construct(Client $client, Serializer $serializer, ContainerInterface $container)
    {
        $this->client = $client;
        $this->serializer = $serializer;
        $this->container = $container;
    }

    /**
     * @param string  $query
     * @param Request $request
     *
     * @return Response
     */
    public function suggestionAction($query, Request $request)
    {
        $response = ['query' => ''];

        if ($query = trim($query)) {
            $response = $this->client->select(new SuggestionQuery($query));
        }

        return new Response($this->serializer->serialize($response, $request->getRequestFormat('json')));
    }
}
