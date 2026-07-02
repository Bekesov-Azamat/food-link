<?php

namespace App\Filament\Resources;

use App\Filament\Resources\ShortLinkResource\Pages;
use App\Filament\Resources\ShortLinkResource\RelationManagers\ClicksRelationManager;
use App\Http\Requests\StoreShortLinkRequest;
use App\Models\ShortLink;
use App\Models\User;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;

class ShortLinkResource extends Resource
{
    protected static ?string $model = ShortLink::class;

    protected static ?string $navigationIcon = 'heroicon-o-link';

    protected static ?string $navigationLabel = 'Short Links';

    protected static ?string $modelLabel = 'Short Link';

    protected static ?string $pluralModelLabel = 'Short Links';

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                Forms\Components\TextInput::make('original_url')
                    ->label('Original URL')
                    ->rules(StoreShortLinkRequest::originalUrlRules())
                    ->dehydrateStateUsing(fn (string $state): string => trim($state))
                    ->placeholder('https://example.com/page')
                    ->helperText('Only http and https URLs are allowed.')
                    ->columnSpanFull(),

                Forms\Components\TextInput::make('short_code')
                    ->label('Short Code')
                    ->disabled()
                    ->dehydrated(false)
                    ->visible(fn (?ShortLink $record): bool => $record !== null),

                Forms\Components\TextInput::make('clicks_count')
                    ->label('Clicks')
                    ->disabled()
                    ->dehydrated(false)
                    ->visible(fn (?ShortLink $record): bool => $record !== null),
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\TextColumn::make('original_url')
                    ->label('Original URL')
                    ->limit(60)
                    ->searchable(),

                Tables\Columns\TextColumn::make('short_code')
                    ->label('Short URL')
                    ->formatStateUsing(fn (ShortLink $record): string => $record->shortUrl())
                    ->copyable()
                    ->copyMessage('Short URL copied')
                    ->openUrlInNewTab()
                    ->url(fn (ShortLink $record): string => $record->shortUrl()),

                Tables\Columns\TextColumn::make('clicks_count')
                    ->label('Clicks')
                    ->sortable(),

                Tables\Columns\TextColumn::make('created_at')
                    ->label('Created')
                    ->dateTime()
                    ->sortable(),
            ])
            ->defaultSort('created_at', 'desc')
            ->filters([])
            ->actions([
                Tables\Actions\Action::make('copy_short_url')
                    ->label('Copy')
                    ->icon('heroicon-o-document-duplicate')
                    ->color('gray')
                    ->action(fn (): null => null)
                    ->extraAttributes(fn (ShortLink $record): array => [
                        'data-copy-url' => $record->shortUrl(),
                        'x-on:click' => 'navigator.clipboard.writeText($el.dataset.copyUrl)',
                    ]),

                Tables\Actions\ViewAction::make(),
                Tables\Actions\EditAction::make(),
                Tables\Actions\DeleteAction::make(),
            ])
            ->bulkActions([]);
    }

    /**
     * @return Builder<ShortLink>
     */
    public static function getEloquentQuery(): Builder
    {
        /** @var User $user */
        $user = auth()->user();

        return parent::getEloquentQuery()
            ->where('user_id', $user->id)
            ->withoutGlobalScopes([
                //
            ]);
    }

    public static function getRelations(): array
    {
        return [ClicksRelationManager::class];
    }

    public static function getPages(): array
    {
        return [
            'index' => Pages\ListShortLinks::route('/'),
            'view' => Pages\ViewShortLink::route('/{record}'),
            'edit' => Pages\EditShortLink::route('/{record}/edit'),
        ];
    }
}
