<?php

namespace Integrated\Common\Storage;

use Doctrine\Common\Collections\ArrayCollection;

/**
 * @author Johnny Borg <johnny@e-active.nl>
 */
interface DecisionInterface
{
    /**
     * @param object $class
     * @return ArrayCollection
     */
    public function getFilesystems($class);
}
