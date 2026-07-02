<?php

namespace App\Filament\Resources\ShortLinkResource\Pages;

use App\Filament\Resources\ShortLinkResource;
use App\Models\ShortLink;
use App\Models\User;
use App\Services\ShortLinkService;
use Filament\Actions;
use Filament\Resources\Pages\ListRecords;

class ListShortLinks extends ListRecords
{
    protected static string $resource = ShortLinkResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\CreateAction::make()
                ->label('New Short Link')
                ->modalHeading('Create Short Link')
                ->modalSubmitActionLabel('Create Short Link')
                ->createAnother(false)
                ->successNotificationTitle('Short link created')
                ->using(function (array $data): ShortLink {
                    /** @var User $user */
                    $user = auth()->user();

                    return app(ShortLinkService::class)->createForUser(
                        $user,
                        (string) $data['original_url'],
                    );
                }),
        ];
    }
}
