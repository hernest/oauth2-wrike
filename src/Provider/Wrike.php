<?php

namespace MichaelKaefer\OAuth2\Client\Provider;

use League\OAuth2\Client\Provider\AbstractProvider;
use Psr\Http\Message\ResponseInterface;
use League\OAuth2\Client\Provider\Exception\IdentityProviderException;
use League\OAuth2\Client\Token\AccessToken;
use League\OAuth2\Client\Tool\BearerAuthorizationTrait;

class Wrike extends AbstractProvider
{
    use BearerAuthorizationTrait;
    
    /**
     * Get authorization url to begin OAuth flow
     *
     * @return string
     */
    public function getBaseAuthorizationUrl()
    {
        return 'https://www.wrike.com/oauth2/authorize';
    }
    
    /**
     * Get access token url to retrieve token
     *
     * @param array $params
     *
     * @return string
     */
    public function getBaseAccessTokenUrl(array $params)
    {
        return 'https://www.wrike.com/oauth2/token';
    }
    
    /**
     * Get provider url to fetch user details
     *
     * @param AccessToken $token
     *
     * @return string
     */
    public function getResourceOwnerDetailsUrl(AccessToken $token)
    {
        return 'https://www.wrike.com/api/v3/contacts?me=true';
    }
    
    /**
     * Get the default scopes used by this provider.
     *
     * @return array
     */
    protected function getDefaultScopes()
    {
        return [];
    }
    
    /**
     * Check a provider response for errors.
     *
     * @param  ResponseInterface $response
     * @param  array|string $data
     * 
     * @throws IdentityProviderException
     */
    protected function checkResponse(ResponseInterface $response, $data)
    {
        if (isset($data['error'])) {
            throw new IdentityProviderException($data['errorDescription'], $response->getStatusCode(), $response);
        }
    }
    
    /**
     * Generate a user object from a successful user details request.
     *
     * @param array $response
     * @param AccessToken $token
     *
     * @return League\OAuth2\Client\Provider\ResourceOwnerInterface
     */
    protected function createResourceOwner(array $response, AccessToken $token)
    {
        return new WrikeResourceOwner($response);
    }
    
    /**
     * Returns a prepared request for requesting an access token.
     *
     * @param array $params
     * 
     * @return Psr\Http\Message\RequestInterface
     */
    protected function getAccessTokenRequest(array $params)
    {
        $request = parent::getAccessTokenRequest($params);
        $uri = $request->getUri()
            ->withUserInfo($this->clientId, $this->clientSecret);
        return $request->withUri($uri);
    }
}

