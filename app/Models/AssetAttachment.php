<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class AssetAttachment extends Model
{
    use HasFactory;
    protected $fillable = [
        'asset_id',
        'title',
        'file_path',
        'file_type',
    ];
    public function asset(): BelongsTo
    {
        return $this->belongsTo(Asset::class);
    }
}
