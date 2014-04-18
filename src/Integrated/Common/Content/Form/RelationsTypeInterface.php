<?php

/*
 * This file is part of the Integrated package.
 *
 * (c) e-Active B.V. <integrated@e-active.nl>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Integrated\Common\Content\Form;

use Symfony\Component\Form\FormTypeInterface as BaseFormTypeInterface;

/**
 * @author Jeroen van Leeuwen <jeroen@e-active.nl>
 */
interface RelationsTypeInterface extends BaseFormTypeInterface
{
    public function setRelations($relations);
}