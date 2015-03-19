<?php namespace SocialNorm\Facebook;

use SocialNorm\Exceptions\InvalidAuthorizationCodeException;
use SocialNorm\Providers\OAuth2Provider;

class FacebookProvider extends OAuth2Provider
{
    protected $authorizeUrl = "https://www.facebook.com/dialog/oauth";
    protected $accessTokenUrl = "https://graph.facebook.com/oauth/access_token";
    protected $userDataUrl = "https://graph.facebook.com/me";
    protected $scope = [
        'email',
    ];

    protected function getAuthorizeUrl()
    {
        return $this->authorizeUrl;
    }

    protected function getAccessTokenBaseUrl()
    {
        return $this->accessTokenUrl;
    }

    protected function getUserDataUrl()
    {
        return $this->userDataUrl;
    }

    protected function parseTokenResponse($response)
    {
        parse_str($response);
        if (! isset($access_token)) {
            throw new InvalidAuthorizationCodeException;
        }
        return $access_token;
    }

    protected function parseUserDataResponse($response)
    {
        return json_decode($response, true);
    }

    protected function userId()
    {
        return $this->getProviderUserData('id');
    }

    protected function nickname()
    {
        return $this->getProviderUserData('username');
    }

    protected function fullName()
    {
        return $this->getProviderUserData('name');
    }

    protected function avatar()
    {
        return 'https://graph.facebook.com/'.$this->userId().'/picture';
    }

    protected function email()
    {
        return $this->getProviderUserData('email');
    }
}
