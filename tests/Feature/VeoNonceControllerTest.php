<?php

use App\Services\Veo\VeoNonceFetcher;
use Mockery\MockInterface;

test('veo nonce api returns only the nonce', function () {
    $this->mock(VeoNonceFetcher::class, function (MockInterface $mock): void {
        $mock->shouldReceive('fetch')
            ->once()
            ->andReturn('nonce_abc123');
    });

    $response = $this->getJson(route('veo.nonce'));

    $response->assertOk()
        ->assertExactJson([
            'nonce' => 'nonce_abc123',
        ]);
});

test('veo nonce api returns null when nonce is unavailable', function () {
    $this->mock(VeoNonceFetcher::class, function (MockInterface $mock): void {
        $mock->shouldReceive('fetch')
            ->once()
            ->andReturnNull();
    });

    $response = $this->getJson(route('veo.nonce'));

    $response->assertOk()
        ->assertExactJson([
            'nonce' => null,
        ]);
});
