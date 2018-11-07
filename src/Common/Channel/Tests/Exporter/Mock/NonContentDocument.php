<?php

namespace Integrated\Common\Channel\Tests\Exporter\Mock;

use Doctrine\Common\Collections\ArrayCollection;
use Integrated\Common\Content\ConnectorInterface;
use Integrated\Common\Content\ConnectorTrait;

class NonContentDocument implements ConnectorInterface
{
    use ConnectorTrait;

    public function __construct()
    {
        $this->connectors = new ArrayCollection();
    }
}
