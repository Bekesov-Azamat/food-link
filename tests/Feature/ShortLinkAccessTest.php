<?php

namespace Tests\Feature;

use App\Filament\Resources\ShortLinkResource;
use App\Models\ShortLink;
use App\Models\User;
use App\Policies\ShortLinkPolicy;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class ShortLinkAccessTest extends TestCase
{
    use RefreshDatabase;

    public function test_user_can_only_query_own_short_links_in_filament_resource(): void
    {
        $currentUser = User::factory()->create();
        $otherUser = User::factory()->create();

        $ownShortLink = ShortLink::query()->create([
            'user_id' => $currentUser->id,
            'original_url' => 'https://own.example.com',
            'short_code' => 'own123',
        ]);

        $otherShortLink = ShortLink::query()->create([
            'user_id' => $otherUser->id,
            'original_url' => 'https://other.example.com',
            'short_code' => 'oth123',
        ]);

        $this->actingAs($currentUser);

        $visibleIds = ShortLinkResource::getEloquentQuery()
            ->pluck('id')
            ->all();

        $this->assertContains($ownShortLink->id, $visibleIds);
        $this->assertNotContains($otherShortLink->id, $visibleIds);
    }

    public function test_policy_blocks_access_to_other_users_short_link(): void
    {
        $currentUser = User::factory()->create();
        $otherUser = User::factory()->create();

        $otherShortLink = ShortLink::query()->create([
            'user_id' => $otherUser->id,
            'original_url' => 'https://other.example.com',
            'short_code' => 'oth123',
        ]);

        $policy = new ShortLinkPolicy;

        $this->assertFalse($policy->view($currentUser, $otherShortLink));
        $this->assertFalse($policy->update($currentUser, $otherShortLink));
        $this->assertFalse($policy->delete($currentUser, $otherShortLink));
    }
}
