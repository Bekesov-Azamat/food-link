<?php

namespace App\Services;

use App\Models\LinkClick;
use App\Models\ShortLink;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;

class ClickTrackingService
{
    public function track(ShortLink $shortLink, Request $request): void
    {
        DB::transaction(function () use ($shortLink, $request): void {
            ShortLink::query()
                ->whereKey($shortLink->id)
                ->increment('clicks_count');

            LinkClick::query()->create([
                'short_link_id' => $shortLink->id,
                'ip_address' => $request->ip() ?? 'unknown',
                'user_agent' => $this->limitNullableHeader($request->userAgent()),
                'referer' => $this->limitNullableHeader($request->headers->get('referer')),
            ]);
        });
    }

    private function limitNullableHeader(?string $value): ?string
    {
        if ($value === null) {
            return null;
        }

        return Str::limit($value, 1000, '');
    }
}
