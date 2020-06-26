<?php

/*
 * This file is part of the Integrated package.
 *
 * (c) e-Active B.V. <integrated@e-active.nl>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Integrated\Bundle\ContentBundle\Bulk;

use Integrated\Bundle\ContentBundle\Form\Type\BulkActionDeleteType;
use Integrated\Common\Bulk\Form\Config;
use Integrated\Common\Bulk\Form\ConfigProviderInterface;

class DeleteFormProvider implements ConfigProviderInterface
{
    /**
     * {@inheritdoc}
     */
    public function getConfig(array $content)
    {
        $config = [];

        $config[] = new Config(
            DeleteHandler::class,
            'delete',
            BulkActionDeleteType::class,
            [
                'label' => 'Delete',
            ],
            new DeleteFormActionMatcher(DeleteHandler::class)
        );

        return $config;
    }
}
