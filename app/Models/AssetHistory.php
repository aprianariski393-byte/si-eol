<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class AssetHistory extends Model
{
    use HasFactory;

    protected $fillable = [
        'asset_id',
        'user_id',
        'action',
        'old_value',
        'new_value',
    ];
    protected function casts(): array
    {
        return [
            'old_value' => 'array', // Otomatis handle JSON ke Array di PHP
            'new_value' => 'array',
        ];
    }

    public function asset(): BelongsTo
    {
        return $this->belongsTo(Asset::class);
    }
    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }
}
