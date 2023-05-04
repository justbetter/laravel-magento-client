<?php

namespace JustBetter\MagentoClient\Tests\Http\Controllers;

use JustBetter\MagentoClient\Contracts\OAuth\ManagesKeys;
use JustBetter\MagentoClient\Contracts\OAuth\RequestsAccessToken;
use JustBetter\MagentoClient\Tests\TestCase;
use Mockery\MockInterface;

class OAuthControllerTest extends TestCase
{
    /** @test */
    public function it_can_call_the_callback_endpoint(): void
    {
        $payload = [
            'oauth_consumer_key' => '::oauth_consumer_key::',
            'oauth_consumer_secret' => '::oauth_consumer_secret::',
            'oauth_verifier' => '::oauth_verifier::',
        ];

        $this->mock(ManagesKeys::class, function (MockInterface $mock) use ($payload): void {
            $mock
                ->shouldReceive('merge')
                ->with(['callback' => $payload])
                ->once();
        });

        $this
            ->withoutMiddleware()
            ->post(route('magento.oauth.callback'), $payload, [
                'Accept' => 'application/json',
            ])
            ->assertSuccessful();
    }

    /** @test */
    public function it_can_validate_the_callback_endpoint(): void
    {
        $this
            ->withoutMiddleware()
            ->post(route('magento.oauth.callback'), [], [
                'Accept' => 'application/json',
            ])
            ->assertStatus(422);
    }

    /** @test */
    public function it_can_block_the_callback_endpoint_without_oauth_authentication(): void
    {
        $this->post(route('magento.oauth.callback'), [], [
            'Accept' => 'application/json',
        ])->assertStatus(403);
    }

    /** @test */
    public function it_can_call_the_identity_endpoint(): void
    {
        $this->mock(RequestsAccessToken::class, function (MockInterface $mock): void {
            $mock
                ->shouldReceive('request')
                ->with('::oauth_consumer_key::')
                ->once();
        });

        $route = route('magento.oauth.identity', [
            'oauth_consumer_key' => '::oauth_consumer_key::',
            'success_call_back' => '::success_call_back::',
        ]);

        $this->withoutMiddleware()->get($route, [
            'Accept' => 'application/json',
        ])->assertRedirect('::success_call_back::');
    }

    /** @test */
    public function it_can_validate_the_identity_endpoint(): void
    {
        $this
            ->withoutMiddleware()
            ->get(route('magento.oauth.identity'), [
                'Accept' => 'application/json',
            ])
            ->assertStatus(422);
    }

    /** @test */
    public function it_can_block_the_identity_endpoint_without_oauth_authentication(): void
    {
        $this->get(route('magento.oauth.identity'), [
            'Accept' => 'application/json',
        ])->assertStatus(403);
    }
}
