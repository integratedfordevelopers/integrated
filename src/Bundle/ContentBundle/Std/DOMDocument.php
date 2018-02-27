<?php

/*
 * This file is part of the Integrated package.
 *
 * (c) e-Active B.V. <integrated@e-active.nl>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Integrated\Bundle\ContentBundle\Std;

use Integrated\Bundle\ContentBundle\Std\Exception\InvalidHTMLException;

/**
 * @author Johnny Borg <johnny@e-active.nl>
 */
class DOMDocument extends \DOMDocument
{
    /**
     * {@inheritdoc}
     *
     * @throws \Integrated\Bundle\ContentBundle\Std\Exception\InvalidHTMLException
     */
    public function loadHTML($source, $options = 0)
    {
        // Allow HTML5 tags to be passed as HTML
        libxml_use_internal_errors(true);

        // Normally errors will be thrown here
        $html = parent::loadHTML($source, $options);

        // Walk over the errors we've got during the loadHTML method
        foreach (libxml_get_errors() as $error) {
            if (!preg_match('/^Tag \w+ invalid\n$/', $error->message)) {
                throw InvalidHTMLException::unresolvedLibXmlError($error->message);
            }
        }

        // Clear the tags (if any)
        libxml_clear_errors();

        // Set back the error handling
        libxml_use_internal_errors(false);

        return $html;
    }
}
