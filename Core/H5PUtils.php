<?php

namespace Emmedy\H5PBundle\Core;

use Symfony\Component\Security\Core\Authentication\Token\Storage\TokenStorageInterface;
use Symfony\Component\Security\Core\User\UserInterface;

class H5PUtils
{
    /**
     * @var TokenStorageInterface
     */
    private $tokenStorage;

    public function __construct(TokenStorageInterface $tokenStorage)
    {
        $this->tokenStorage = $tokenStorage;
    }

    /**
     * Fetch the current user if not present return the anonymous user
     * @return string|UserInterface|null
     */
    public function getCurrentOrAnonymousUser()
    {
        $token = $this->tokenStorage->getToken();

        if ($token && $token->getUser() !== 'anon.') {
            return $token->getUser();
        }
        return 'anon.';
    }

    public function getUserId(UserInterface $user)
    {
        if (method_exists($user, 'getId')) {
            return $user->getId();
        }
        return $user->getUserIdentifier();
    }
}
