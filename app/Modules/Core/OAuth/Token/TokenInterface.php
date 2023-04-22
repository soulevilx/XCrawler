<?php

namespace App\Modules\Core\OAuth\Token;

interface TokenInterface
{
    /**
     * Denotes an unknown end of life time.
     */
    const EOL_UNKNOWN = -9001;

    /**
     * Denotes a token which never expires, should only happen in OAuth1.
     */
    const EOL_NEVER_EXPIRES = -9002;

    public function getAccessToken(): string;

    /**
     * @return int
     */
    public function getEndOfLife(): int;

    public function getExtraParams(): array;

    /**
     * @param string $accessToken
     */
    public function setAccessToken(string $accessToken);

    /**
     * @param int $endOfLife
     */
    public function setEndOfLife(int $endOfLife);

    /**
     * @param int $lifetime
     */
    public function setLifetime(int $lifetime);

    public function setExtraParams(array $extraParams);

    public function getRefreshToken(): string;

    public function setRefreshToken(string $refreshToken): void;
}
