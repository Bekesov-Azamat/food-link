<?php

namespace App\Services;

use App\Models\ShortLink;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;

class RedirectService
{
    public function __construct(
        private readonly ClickTrackingService $clickTrackingService,
    ) {}

    public function redirect(string $shortCode, Request $request): RedirectResponse
    {
        $shortLink = ShortLink::query()
            ->where('short_code', $shortCode)
            ->firstOrFail();

        $this->clickTrackingService->track($shortLink, $request);

        return redirect()->away($shortLink->original_url);
    }
}
