<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Asset extends Model
{
    use HasFactory;
    protected $fillable = [
        'asset_code',
        'name',
        'category',
        'brand',
        'serial_number',
        'purchase_date',
        'eol_date',
        'created_by',
        'status',
        'department',
        'description',
        'attachments',
    ];
    protected function casts(): array
    {
        return [
            'purchase_date' => 'date',
            'eol_date' => 'date',
            'attachments' => 'array',
        ];
    }

    public function maintenanceLogs(): HasMany
    {
        return $this->hasMany(MaintenanceLog::class);
    }

    public function creator(): BelongsTo
    {
        return $this->belongsTo(User::class, 'created_by');
    }
}
