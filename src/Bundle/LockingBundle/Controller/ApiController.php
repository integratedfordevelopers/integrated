<?php

/*
 * This file is part of the Integrated package.
 *
 * (c) e-Active B.V. <integrated@e-active.nl>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Integrated\Bundle\LockingBundle\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Integrated\Common\Locks\Resource;
use Integrated\Common\Locks;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;

/**
 * @author Jan Sanne Mulder <jansanne@e-active.nl>
 */
class ApiController extends AbstractController
{
    /**
     * @param Request $request
     *
     * @return JsonResponse
     */
    public function refresh(Request $request)
    {
        if (!$this->has('integrated_locking.dbal.manager')) {
            $response = [
                'code' => 403,
                'message' => 'Locking is not enabled',
            ];

            return new JsonResponse($response, $response['code']);
        }

        if (!$owner = $this->getUser()) {
            $response = [
                'code' => 401,
                'message' => 'Valid user is required',
            ];

            return new JsonResponse($response, $response['code']);
        }

        $owner = Resource::fromAccount($owner);

        // get the lock and check if the lock is set by the current use else do nothing

        if (!$lock = $request->query->get('lock')) {
            $response = [
                'code' => 400,
                'message' => 'Missing lock identifier',
            ];

            return new JsonResponse($response, $response['code']);
        }

        /** @var Locks\ManagerInterface $service */
        $service = $this->get('integrated_locking.dbal.manager');

        if (!$lock = $service->find($lock)) {
            $response = [
                'code' => 404,
                'message' => 'The lock could not be found',
            ];

            return new JsonResponse($response, $response['code']);
        }

        $response = [
            'code' => 200,
            'message' => 'The lock could not be extended',
            'lock' => null,
        ];

        if ($owner->equals($lock->getRequest()->getOwner())) {
            // only the owner can extends the lock.

            if ($lock = $service->refresh($lock)) {
                $response['message'] = 'The lock is extended';
                $response['lock'] = $lock->getId();
            }
        }

        return new JsonResponse($response, $response['code']);
    }
}
