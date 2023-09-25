<?php

namespace JustBetter\MagentoClient\Tests\Models;

use Illuminate\Foundation\Testing\RefreshDatabase;
use JustBetter\MagentoClient\Models\MagentoOAuth;
use JustBetter\MagentoClient\Tests\TestCase;


class MagentoOAuthTest extends TestCase
{
    use RefreshDatabase;

    /** @test */
    function it_can_save_oauth_keys_in_db()
    {
        $keys = MagentoOAuth::factory()->create();
        $this->assertModelExists($keys);
    }
}