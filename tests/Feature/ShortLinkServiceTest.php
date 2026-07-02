<?php

namespace Tests\Feature;

use App\Models\ShortLink;
use App\Models\User;
use App\Services\ShortCodeGenerator;
use App\Services\ShortLinkService;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class ShortLinkServiceTest extends TestCase
{
    use RefreshDatabase;

    public function test_it_creates_short_link_for_user(): void
    {
        $user = User::factory()->create();

        $this->app->instance(
            ShortCodeGenerator::class,
            new class extends ShortCodeGenerator
            {
                public function generate(int $length = 6): string
                {
                    return 'abc123';
                }
            },
        );

        $shortLink = app(ShortLinkService::class)->createForUser(
            $user,
            'https://laravel.com/docs',
        );

        $this->assertSame($user->id, $shortLink->user_id);
        $this->assertSame('https://laravel.com/docs', $shortLink->original_url);
        $this->assertSame('abc123', $shortLink->short_code);
        $this->assertSame(0, $shortLink->clicks_count);

        $this->assertDatabaseHas('short_links', [
            'user_id' => $user->id,
            'original_url' => 'https://laravel.com/docs',
            'short_code' => 'abc123',
            'clicks_count' => 0,
        ]);
    }

    public function test_it_retries_when_generated_short_code_already_exists(): void
    {
        $user = User::factory()->create();

        ShortLink::query()->create([
            'user_id' => $user->id,
            'original_url' => 'https://existing.example.com',
            'short_code' => 'abc123',
        ]);

        $this->app->instance(
            ShortCodeGenerator::class,
            new class extends ShortCodeGenerator
            {
                private array $codes = [
                    'abc123',
                    'def456',
                ];

                public function generate(int $length = 6): string
                {
                    return array_shift($this->codes);
                }
            },
        );

        $shortLink = app(ShortLinkService::class)->createForUser(
            $user,
            'https://new.example.com',
        );

        $this->assertSame('def456', $shortLink->short_code);

        $this->assertDatabaseHas('short_links', [
            'original_url' => 'https://new.example.com',
            'short_code' => 'def456',
        ]);
    }
}
