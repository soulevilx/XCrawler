<?php

namespace App\Modules\Core\OAuth\OAuth1\Providers;

use App\Modules\Core\Helpers\Random;
use App\Modules\Core\Models\Integration;
use App\Modules\Core\OAuth\AbstractBaseProvider;
use App\Modules\Core\OAuth\Events\RetrievedRequestToken;
use App\Modules\Core\OAuth\OAuth1\Signature\Signature;
use App\Modules\Core\OAuth\OAuth1\Signature\SignatureInterface;
use App\Modules\Core\OAuth\OAuth1\Token\TokenInterface;
use App\Modules\Core\OAuth\Storage\TokenStorageInterface;
use App\Modules\Core\OAuth\Uri\UriInterface;
use App\Modules\Core\XClient\Response\BaseResponse;
use App\Modules\Core\XClient\Response\XClientResponseInterface;
use App\Modules\Core\XClient\XClient;
use DateTime;
use Illuminate\Contracts\Container\BindingResolutionException;
use Illuminate\Support\Facades\Event;

abstract class AbstractProvider extends AbstractBaseProvider implements ProviderInterface
{
    public const SIGNATURE_METHOD = 'HMAC-SHA1';

    protected SignatureInterface $signature;

    /**
     * {@inheritdoc}
     * @throws BindingResolutionException
     */
    public function __construct(
        protected TokenStorageInterface $storage,
        protected XClient $client,
        protected ?UriInterface $baseApiUri = null
    ) {
        parent::__construct($this->storage, $this->client);

        $this->signature = app(Signature::class, ['credentials' => $this->credentials]);
        $this->signature->setHashingAlgorithm($this->getSignatureMethod());
    }

    /**
     * {@inheritdoc}
     */
    public function requestRequestToken(): TokenInterface
    {
        $authorizationHeader = ['Authorization' => $this->buildAuthorizationHeaderForTokenRequest()];
        $headers = [...$authorizationHeader, ...$this->getExtraOAuthHeaders()];
        $this->client->setHeaders($headers);

        $token = $this->parseRequestTokenResponse(
            $this->client->post($this->getRequestTokenEndpoint())->getBody()->getContents()
        );
        $this->storage->storeAccessToken($this->service(), $token);

        Event::dispatch(new RetrievedRequestToken($token));

        return $token;
    }

    /**
     * {@inheritdoc}
     */
    public function getAuthorizationUri(array $additionalParameters = []): UriInterface
    {
        // Build the url
        $url = clone $this->getAuthorizationEndpoint();
        foreach ($additionalParameters as $key => $val) {
            $url->addToQuery($key, $val);
        }

        return $url;
    }

    public function retrieveAccessToken(string $verifier, ?TokenInterface $requestToken = null): TokenInterface
    {
        $token = $this->storage->retrieveAccessToken($this->service());

        // If no request token is provided, try to get it from this object.
        if ($requestToken === null) {
            $requestToken = $token->getAccessToken();
        }

        $accessToken = $this->requestAccessToken($requestToken, $verifier, $token->getAccessTokenSecret());

        if ($accessToken) {
            Integration::updateOrCreate([
                'service' => $this->service(),
            ], [
                'token_secret' => $accessToken->getAccessTokenSecret(),
                'token' => $accessToken->getAccessToken(),
                'data' => json_encode($accessToken)
            ]);
        }

        return $accessToken;
    }

    /**
     * {@inheritdoc}
     */
    public function requestAccessToken($token, $verifier, $tokenSecret = null): TokenInterface
    {
        if ($tokenSecret === null) {
            $storedRequestToken = $this->storage->retrieveAccessToken($this->service());
            $tokenSecret = $storedRequestToken->getRequestTokenSecret();
        }
        $this->signature->setTokenSecret($tokenSecret);

        $bodyParams = [
            'oauth_verifier' => $verifier,
        ];

        $authorizationHeader = [
            'Authorization' => $this->buildAuthorizationHeaderForAPIRequest(
                'POST',
                $this->getAccessTokenEndpoint(),
                $this->storage->retrieveAccessToken($this->service()),
                $bodyParams
            ),
        ];

        $this->client->setHeaders(array_merge($authorizationHeader, $this->getExtraOAuthHeaders()));
        $responseBody = $this->client->request(
            $this->getAccessTokenEndpoint(), $bodyParams,
            'POST',
        );

        $token = $this->parseAccessTokenResponse($responseBody->getBody()->getContents());

        $this->storage->storeAccessToken($this->service(), $token);

        return $token;
    }

    /**
     * Refreshes an OAuth1 access token.
     *
     * @return TokenInterface $token
     */
    public function refreshAccessToken(TokenInterface $token): TokenInterface
    {
    }

    /**
     * Sends an authenticated API request to the path provided.
     * If the path provided is not an absolute URI, the base API Uri (must be passed into constructor) will be used.
     *
     * @param  string|UriInterface  $path
     * @param  string  $method  HTTP method
     * @param  array  $body  Request body if applicable (key/value pairs)
     * @param  array  $extraHeaders  Extra headers if applicable.
     *                                          These will override service-specific any defaults.
     *
     * @return XClientResponseInterface
     */
    public function request($path, array $body = [], array $extraHeaders = [], string $method = 'GET'): XClientResponseInterface
    {
        $uri = $this->determineRequestUriFromPath($path, $this->baseApiUri);

        $token = $this->storage->retrieveAccessToken($this->service());
        $extraHeaders = array_merge($this->getExtraApiHeaders(), $extraHeaders);
        $authorizationHeader = [
            'Authorization' => $this->buildAuthorizationHeaderForAPIRequest($method, $uri, $token, $body),
        ];

        $this->client->setHeaders(array_merge($authorizationHeader, $extraHeaders));

        $response = $this->client->request($uri, $body, $method);

        return new BaseResponse($response);
    }

    /**
     * Return any additional headers always needed for this service implementation's OAuth calls.
     *
     * @return array
     */
    protected function getExtraOAuthHeaders(): array
    {
        return [];
    }

    /**
     * Return any additional headers always needed for this service implementation's API calls.
     *
     * @return array
     */
    protected function getExtraApiHeaders(): array
    {
        return [];
    }

    /**
     * Builds the authorization header for getting an access or request token.
     *
     * @return string
     */
    protected function buildAuthorizationHeaderForTokenRequest(array $extraParameters = []): string
    {
        $parameters = [...$this->getBasicAuthorizationHeaderInfo(), ...$extraParameters];
        $parameters['oauth_signature'] = $this->signature->getSignature(
            $this->getRequestTokenEndpoint(),
            $parameters,
        );

        $authorizationHeader = 'OAuth ';
        $delimiter = '';
        foreach ($parameters as $key => $value) {
            $authorizationHeader .= $delimiter.rawurlencode($key).'="'.rawurlencode($value).'"';

            $delimiter = ', ';
        }

        return $authorizationHeader;
    }

    /**
     * Builds the authorization header for an authenticated API request.
     *
     * @param  string  $method
     * @param  UriInterface  $uri  The uri the request is headed
     * @param  array  $bodyParams  Request body if applicable (key/value pairs)
     *
     * @return string
     */
    protected function buildAuthorizationHeaderForAPIRequest(
        $method,
        UriInterface $uri,
        TokenInterface $token,
        array $bodyParams = null
    ): string {
        $this->signature->setTokenSecret($token->getAccessTokenSecret());
        $authParameters = $this->getBasicAuthorizationHeaderInfo();
        if (isset($authParameters['oauth_callback'])) {
            unset($authParameters['oauth_callback']);
        }

        $authParameters = array_merge($authParameters, ['oauth_token' => $token->getAccessToken()]);

        $signatureParams = (is_array($bodyParams)) ? array_merge($authParameters, $bodyParams) : $authParameters;
        $authParameters['oauth_signature'] = $this->signature->getSignature($uri, $signatureParams, $method);

        if (is_array($bodyParams) && isset($bodyParams['oauth_session_handle'])) {
            $authParameters['oauth_session_handle'] = $bodyParams['oauth_session_handle'];
            unset($bodyParams['oauth_session_handle']);
        }

        $authorizationHeader = 'OAuth ';
        $delimiter = '';

        foreach ($authParameters as $key => $value) {
            $authorizationHeader .= $delimiter.rawurlencode($key).'="'.rawurlencode($value).'"';
            $delimiter = ', ';
        }

        return $authorizationHeader;
    }

    /**
     * Builds the authorization header array.
     *
     * @return array
     */
    protected function getBasicAuthorizationHeaderInfo()
    {
        $dateTime = new DateTime();
        $headerParameters = [
            'oauth_callback' => $this->credentials->getCallbackUrl(),
            'oauth_consumer_key' => $this->credentials->getConsumerId(),
            'oauth_nonce' => Random::generateNonce(),
            'oauth_signature_method' => $this->getSignatureMethod(),
            'oauth_timestamp' => $dateTime->format('U'),
            'oauth_version' => $this->getVersion(),
        ];

        return $headerParameters;
    }

    /**
     * @return string
     */
    protected function getSignatureMethod()
    {
        return self::SIGNATURE_METHOD;
    }

    /**
     * This returns the version used in the authorization header of the requests.
     *
     * @return string
     */
    protected function getVersion()
    {
        return '1.0';
    }

    /**
     * Parses the request token response and returns a TokenInterface.
     * This is only needed to verify the `oauth_callback_confirmed` parameter. The actual
     * parsing logic is contained in the access token parser.
     *
     * @abstract
     *
     * @param  string  $responseBody
     *
     * @return TokenInterface
     */
    abstract protected function parseRequestTokenResponse(string $responseBody): TokenInterface;

    /**
     * Parses the access token response and returns a TokenInterface.
     *
     * @abstract
     *
     * @param  string  $responseBody
     *
     * @return TokenInterface
     */
    abstract protected function parseAccessTokenResponse(string $responseBody): TokenInterface;
}
