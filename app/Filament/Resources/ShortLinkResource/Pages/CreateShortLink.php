<?php

namespace App\Filament\Resources\ShortLinkResource\Pages;

use App\Filament\Resources\ShortLinkResource;
use App\Models\ShortLink;
use App\Models\User;
use App\Services\ShortLinkService;
use Filament\Resources\Pages\CreateRecord;

class CreateShortLink extends CreateRecord
{
    protected static string $resource = ShortLinkResource::class;

    protected function handleRecordCreation(array $data): ShortLink
    {
        /** @var User $user */
        $user = auth()->user();

        return app(ShortLinkService::class)->createForUser(
            $user,
            $data['original_url'],
        );
    }
}
