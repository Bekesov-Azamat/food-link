<?php

namespace App\Services;

use App\Models\ShortLink;
use App\Models\User;
use Illuminate\Database\UniqueConstraintViolationException;
use RuntimeException;

class ShortLinkService
{
    public const MAX_GENERATION_ATTEMPTS = 5;

    public function __construct(
        private readonly ShortCodeGenerator $shortCodeGenerator,
    ) {}

    public function createForUser(User $user, string $originalUrl): ShortLink
    {
        for ($attempt = 1; $attempt <= self::MAX_GENERATION_ATTEMPTS; $attempt++) {
            try {
                return ShortLink::query()->create([
                    'user_id' => $user->id,
                    'original_url' => $originalUrl,
                    'short_code' => $this->shortCodeGenerator->generate(),
                    'clicks_count' => 0,
                ]);
            } catch (UniqueConstraintViolationException) {
                continue;
            }
        }

        throw new RuntimeException('Unable to generate a unique short code.');
    }
}
