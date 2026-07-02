<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\SoftDeletes;

class ShortLink extends Model
{
    use SoftDeletes;

    protected $fillable = [
        'user_id',
        'original_url',
        'short_code',
        'clicks_count',
    ];

    protected function casts(): array
    {
        return [
            'clicks_count' => 'integer',
            'deleted_at' => 'datetime',
        ];
    }

    public function shortUrl(): string
    {
        return url($this->short_code);
    }

    /**
     * @return BelongsTo<User, $this>
     */
    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    /**
     * @return HasMany<LinkClick, $this>
     */
    public function clicks(): HasMany
    {
        return $this->hasMany(LinkClick::class);
    }
}
