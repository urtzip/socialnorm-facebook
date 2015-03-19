<?php

use Mockery as M;
use SocialNorm\Facebook\FacebookProvider;
use SocialNorm\Request;

use GuzzleHttp\Client as HttpClient;
use GuzzleHttp\Subscriber\Mock as SubscriberMock;

class FacebookProviderTest extends TestCase
{
    private function getStubbedHttpClient($responses = [])
    {
        $client = new HttpClient;
        $mockSubscriber = new SubscriberMock($responses);
        $client->getEmitter()->attach($mockSubscriber);
        return $client;
    }

    /** @test */
    public function it_can_retrieve_a_normalized_user()
    {
        $client = $this->getStubbedHttpClient([
            __DIR__ . '/_fixtures/facebook_accesstoken.txt',
            __DIR__ . '/_fixtures/facebook_user.txt',
        ]);

        $provider = new FacebookProvider([
            'client_id' => 'abcdefgh',
            'client_secret' => '12345678',
            'redirect_uri' => 'http://example.com/login',
        ], $client, new Request(['code' => 'abc123']));

        $user = $provider->getUser();

        $this->assertEquals('187903669', $user->id);
        $this->assertEquals('john.doe', $user->nickname);
        $this->assertEquals('John Doe', $user->full_name);
        $this->assertEquals('example@example.com', $user->email);
        $this->assertEquals('https://graph.facebook.com/187903669/picture', $user->avatar);
        $this->assertEquals(
            'nrQpCDgUzGXbh7K6KwDBmj5uEBM4ElZDzYRth3ZouKUHDcZtX0dRcWAnIVpCzVjlCKbFTJ69gLA3nUKcFiQZmrtgwfN5L4cVMCfDPfnpGo4aoThPG6LdnlTiigkTLxPU3tnLokQqYyo5TmGK8Pxwdwfx3StUxFBSnCenFikR6riEYtUYMKfXuILy0hvRAZUZCGdXOzIgCTw9jKNegtWpgdoiacgkFHN',
            $user->access_token
        );
    }

    /**
     * @test
     * @expectedException SocialNorm\Exceptions\ApplicationRejectedException
     */
    public function it_fails_to_retrieve_a_user_when_the_authorization_code_is_omitted()
    {
        $client = $this->getStubbedHttpClient([
            __DIR__ . '/_fixtures/facebook_accesstoken.txt',
            __DIR__ . '/_fixtures/facebook_user.txt',
        ]);

        $provider = new FacebookProvider([
            'client_id' => 'abcdefgh',
            'client_secret' => '12345678',
            'redirect_uri' => 'http://example.com/login',
        ], $client, new Request([]));

        $user = $provider->getUser();
    }
}
