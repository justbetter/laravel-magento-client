<?php

namespace JustBetter\MagentoClient\Tests\Client;

use Illuminate\Http\Request;
use Illuminate\Support\Arr;
use JustBetter\MagentoClient\Actions\OAuth\ManageKeysFromDb;
use JustBetter\MagentoClient\Models\MagentoOAuth;
use JustBetter\MagentoClient\Tests\TestCase;

class ManageKeysFromDbTest extends TestCase
{
    /** @test */
    public function it_can_get_data(): void
    {
        $keys = MagentoOAuth::factory()->create();

        $request = new Request();
        $request->replace(['oauth_consumer_key' => $keys->oauth_consumer_key]);
        app()->instance('request', $request);

        $action = app(ManageKeysFromDb::class);

        $data = $action->get();

        $this->assertEquals($data, $keys->toArray());
    }

    /** @test */
    public function it_can_set_data(): void
    {
        $keys = MagentoOAuth::factory()->make();

        $request = new Request();
        $request->replace(['oauth_consumer_key' => $keys->oauth_consumer_key]);
        app()->instance('request', $request);

        $action = app(ManageKeysFromDb::class);
        $action->set($keys->toArray());

        $data = $action->get();

        $this->assertEquals(Arr::except($data, ['id', 'created_at', 'updated_at']), $keys->toArray());
    }

    /** @test */
    public function it_can_merge_data(): void
    {
        $keys = MagentoOAuth::factory()->create();

        $request = new Request();
        $request->replace(['oauth_consumer_key' => $keys->oauth_consumer_key]);
        app()->instance('request', $request);

        $new = MagentoOAuth::factory()->make(['oauth_consumer_key' => $keys->oauth_consumer_key]);

        $action = app(ManageKeysFromDb::class);
        $action->merge($new->toArray());

        $data = $action->get();

        $this->assertEquals(array_merge($keys->toArray(), $new->toArray()), $data);
    }
}
