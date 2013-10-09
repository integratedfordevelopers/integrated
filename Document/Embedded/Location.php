<?php
namespace Integrated\Bundle\ContentBundle\Document\Embedded;

use Doctrine\ODM\MongoDB\Mapping\Annotations as ODM;

/**
 * Embedded document Location
 *
 * @package Integrated\Bundle\ContentBundle\Document\Embedded
 * @author Jeroen van Leeuwen <jeroen@e-active.nl>
 * @ODM\EmbeddedDocument
 */
class Location
{
    /**
     * @var float
     * @ODM\Float
     */
    protected $latitude;

    /**
     * @var float
     * @ODM\Float
     */
    protected $longitude;

    /**
     * Get the latitude of the document
     *
     * @return float
     */
    public function getLatitude()
    {
        return $this->latitude;
    }

    /**
     * Set the latitude of the document
     *
     * @param float $latitude
     * @return $this
     */
    public function setLatitude($latitude)
    {
        $this->latitude = $latitude;
        return $this;
    }

    /**
     * Get the longitude of the document
     *
     * @return float
     */
    public function getLongitude()
    {
        return $this->longitude;
    }

    /**
     * Set the longitude of the document
     *
     * @param float $longitude
     * @return $this
     */
    public function setLongitude($longitude)
    {
        $this->longitude = $longitude;
        return $this;
    }
}