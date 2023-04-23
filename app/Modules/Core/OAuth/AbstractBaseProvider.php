<?php

namespace App\Modules\Core\OAuth;

use App\Modules\Core\OAuth\Credentials\CredentialsFactory;
use App\Modules\Core\OAuth\Credentials\CredentialsInterface;
use App\Modules\Core\OAuth\Storage\TokenStorageInterface;
use App\Modules\Core\OAuth\Uri\Uri;
use App\Modules\Core\OAuth\Uri\UriInterface;
use App\Modules\Core\XClient\XClient;
use Exception;

abstract class AbstractBaseProvider implements ProviderInterface
{
    protected CredentialsInterface $credentials;

    public function __construct(protected TokenStorageInterface $storage, protected XClient $client)
    {
        $this->credentials = app(CredentialsFactory::class)->make($this->service());
        $this->client->init();
    }

    /**
     * @param  string|UriInterface  $path
     * @param  UriInterface|null  $baseApiUri
     *
     * @return UriInterface
     * @throws Exception
     */
    protected function determineRequestUriFromPath($path, ?UriInterface $baseApiUri = null): UriInterface
    {
        if ($path instanceof UriInterface) {
            $uri = $path;
        } elseif (stripos($path, 'http://') === 0 || stripos($path, 'https://') === 0) {
            $uri = new Uri($path);
        } else {
            if ($baseApiUri === null) {
                throw new Exception(
                    'An absolute URI must be passed to ServiceInterface::request as no baseApiUri is set.'
                );
            }

            $uri = clone $baseApiUri;
            if (false !== strpos($path, '?')) {
                $parts = explode('?', $path, 2);
                $path = $parts[0];
                $query = $parts[1];
                $uri->setQuery($query);
            }

            if ($path[0] === '/') {
                $path = substr($path, 1);
            }

            $uri->setPath($uri->getPath().$path);
        }

        return $uri;
    }

    /**
     * Accessor to the storage adapter to be able to retrieve tokens.
     *
     * @return TokenStorageInterface
     */
    public function getStorage(): TokenStorageInterface
    {
        return $this->storage;
    }
}
