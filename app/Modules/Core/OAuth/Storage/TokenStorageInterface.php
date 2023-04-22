<?php

namespace App\Modules\Core\OAuth\Storage;

use App\Modules\Core\OAuth\Token\TokenInterface;

interface TokenStorageInterface
{

    public function retrieveAccessToken(string $service): TokenInterface;

    public function storeAccessToken(string $service, TokenInterface $token): self;

    public function hasAccessToken(string $service): bool;

    public function clearToken(string $service): self;

    public function clearAllTokens(): self;

    public function storeAuthorizationState(string $service, string $state): self;

    public function hasAuthorizationState(string $service): bool;


    public function retrieveAuthorizationState(string $service): string;

    /**
     * Clear the authorization state of a given service.
     *
     * @param string $service
     *
     * @return TokenStorageInterface
     */
    public function clearAuthorizationState(string $service): self;

    /**
     * Delete *ALL* user authorization states. Use with care. Most of the time you will likely
     * want to use clearAuthorization() instead.
     *
     * @return TokenStorageInterface
     */
    public function clearAllAuthorizationStates(): self;
}
