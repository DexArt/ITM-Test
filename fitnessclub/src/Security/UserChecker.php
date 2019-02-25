<?php
/**
 * Created by PhpStorm.
 * User: desig
 * Date: 14-Feb-19
 * Time: 16:33
 */

namespace App\Security;
use App\Entity\User;
use Symfony\Component\Security\Core\Exception\CustomUserMessageAuthenticationException;
use Symfony\Component\Security\Core\User\UserCheckerInterface;
use Symfony\Component\Security\Core\User\UserInterface;

class UserChecker implements UserCheckerInterface
{
    public function checkPreAuth(UserInterface $user)
    {
        if (!$user instanceof User) {
            return;
        }

        if (!$user->getIsActive()) {

            throw new CustomUserMessageAuthenticationException(
                'Your account blocked. Sorry about that!'
            );
        }

        if ($user->getisDeleted()) {

            throw new CustomUserMessageAuthenticationException(
                'Your account deleted. Sorry about that!'
            );
        }

    }

    public function checkPostAuth(UserInterface $user)
    {
        // TODO: Implement checkPostAuth() method.
    }

}