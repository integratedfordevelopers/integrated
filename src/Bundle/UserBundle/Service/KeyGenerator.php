<?php
/*
 * This file is part of the Integrated package.
 *
 * (c) e-Active B.V. <integrated@e-active.nl>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Integrated\Bundle\UserBundle\Service;

use Integrated\Bundle\UserBundle\Doctrine\UserManager;
use Integrated\Bundle\UserBundle\Model\UserInterface;

class KeyGenerator
{
    /**
     * @var UserManager
     */
    private $userManager;

    /**
     * @param UserManager $userManager
     */
    public function __construct(UserManager $userManager)
    {
        $this->userManager = $userManager;
    }

    /**
     * @param int           $timestamp
     * @param UserInterface $user
     *
     * @return string
     */
    public function generateKey(int $timestamp, UserInterface $user): string
    {
        return sha1($timestamp.$user->getPassword().$user->getId());
    }

    /**
     * @param int    $id
     * @param int    $timestamp
     * @param string $key
     *
     * @return bool
     */
    public function isValidKey(int $id, int $timestamp, string $key): bool
    {
        if ($timestamp > time() || $timestamp < (time() - 24 * 3600)) {
            return false;
        }

        if (!$user = $this->userManager->find($id)) {
            return false;
        }

        return $key === sha1($timestamp.$user->getPassword().$user->getId());
    }
}
