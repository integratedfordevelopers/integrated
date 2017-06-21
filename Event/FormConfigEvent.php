<?php

/*
 * This file is part of the Integrated package.
 *
 * (c) e-Active B.V. <integrated@e-active.nl>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Integrated\Bundle\ChannelBundle\Event;

use Integrated\Bundle\ChannelBundle\Model\Config;

use Symfony\Component\Form\Form;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;

/**
 * @author Jan Sanne Mulder <jansanne@e-active.nl>
 */
class FormConfigEvent extends ConfigEvent
{
    /**
     * @var Form
     */
    private $form;

    /**
     * @var Response
     */
    private $response = null;

    /**
     * @param Config  $config
     * @param Request $request
     * @param Form    $form
     */
    public function __construct(Config $config, Request $request, Form $form)
    {
        parent::__construct($config, $request);

        $this->form = $form;
    }

    /**
     * @return Form
     */
    public function getForm()
    {
        return $this->form;
    }

    /**
     * @return Response
     */
    public function getResponse()
    {
        return $this->response;
    }

    /**
     * @param Response $response
     */
    public function setResponse(Response $response = null)
    {
        $this->response = $response;
    }
}
