<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class SoftwareLicenseDetail extends Model
{
    use HasFactory;
    protected $fillable = [
        'asset_id',
        'license_type',
        'seats_count',
        'activated_at',
        'is_active',
    ];
    protected function casts(): array
    {
        return [
            'activated_at' => 'date',
            'is_active' => 'boolean',
            'seats_count' => 'integer',
        ];
    }

    public function asset(): BelongsTo
    {
        return $this->belongsTo(Asset::class);
    }
}
