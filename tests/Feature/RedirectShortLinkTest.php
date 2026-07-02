<?php

namespace Tests\Feature;

use App\Models\ShortLink;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class RedirectShortLinkTest extends TestCase
{
    use RefreshDatabase;

    public function test_it_redirects_to_original_url_and_tracks_click(): void
    {
        $user = User::factory()->create();

        $shortLink = ShortLink::query()->create([
            'user_id' => $user->id,
            'original_url' => 'https://example.com/page',
            'short_code' => 'abc123',
        ]);

        $response = $this
            ->withHeaders([
                'User-Agent' => 'PHPUnit Browser',
                'Referer' => 'https://referrer.example.com',
            ])
            ->get('/abc123');

        $response->assertRedirect('https://example.com/page');

        $this->assertDatabaseHas('short_links', [
            'id' => $shortLink->id,
            'clicks_count' => 1,
        ]);

        $this->assertDatabaseHas('link_clicks', [
            'short_link_id' => $shortLink->id,
            'user_agent' => 'PHPUnit Browser',
            'referer' => 'https://referrer.example.com',
        ]);
    }

    public function test_unknown_short_code_returns_not_found(): void
    {
        $this->get('/missing1')
            ->assertNotFound();
    }

    public function test_deleted_short_link_returns_not_found(): void
    {
        $user = User::factory()->create();

        $shortLink = ShortLink::query()->create([
            'user_id' => $user->id,
            'original_url' => 'https://example.com/page',
            'short_code' => 'abc123',
        ]);

        $shortLink->delete();

        $this->get('/abc123')
            ->assertNotFound();

        $this->assertDatabaseMissing('link_clicks', [
            'short_link_id' => $shortLink->id,
        ]);
    }
}
