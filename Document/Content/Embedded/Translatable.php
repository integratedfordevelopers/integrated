<?php

/*
* This file is part of the Integrated package.
*
* (c) e-Active B.V. <integrated@e-active.nl>
*
* For the full copyright and license information, please view the LICENSE
* file that was distributed with this source code.
*/

namespace Integrated\Bundle\ContentBundle\Document\Content\Embedded;

use Doctrine\ODM\MongoDB\Mapping\Annotations as ODM;

/**
 * Embedded document Translatable
 *
 * @author Jeroen van Leeuwen <jeroen@e-active.nl>
 * @ODM\EmbeddedDocument
 */
class Translatable
{
    /**
     * @var array
     * @ODM\Hash
     */
    protected $translations = array();

    /**
     * @var string
     */
    private $currentTranslation = '';

    /**
     * @return array
     */
    public function getTranslations()
    {
        return $this->translations;
    }

    /**
     * @param array $translations
     * @return $this
     */
    public function setTranslations(array $translations)
    {
        $this->translations = $translations;
        return $this;
    }

    /**
     * @param $currentTranslation
     * @return $this
     */
    public function setCurrentTranslation($currentTranslation)
    {
        $this->currentTranslation = $currentTranslation;
        return $this;
    }

    /**
     * @return string
     */
    public function __toString()
    {
        return $this->currentTranslation;
    }
}