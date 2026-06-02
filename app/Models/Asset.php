<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\HasOne;

class Asset extends Model
{
    use HasFactory;
    protected $fillable = [
        'asset_code',
        'name',
        'category_id',
        'vendor_id',
        'brand',
        'model_number',
        'serial_number',
        'asset_type',
        'purchase_date',
        'purchase_cost',
        'useful_life_years',
        'eol_date',
        'is_subscription',
        'subscription_expiry',
        'status_id',
        'location_id',
        'department_id',
        'is_critical',
        'description',
    ];
    protected function casts(): array
    {
        return [
            'purchase_date' => 'date',
            'eol_date' => 'date',
            'subscription_expiry' => 'date',
            'purchase_cost' => 'decimal:2',
            'is_subscription' => 'boolean',
            'is_critical' => 'boolean',
        ];
    }

    // --- Relations (Belongs To) ---
    public function category(): BelongsTo
    {
        return $this->belongsTo(Category::class);
    }
    public function vendor(): BelongsTo
    {
        return $this->belongsTo(Vendor::class);
    }
    public function status(): BelongsTo
    {
        return $this->belongsTo(Status::class);
    }
    public function location(): BelongsTo
    {
        return $this->belongsTo(Location::class);
    }
    public function department(): BelongsTo
    {
        return $this->belongsTo(Department::class);
    }

    // --- Relations (Has Many / Has One) ---
    public function softwareLicenseDetail(): HasOne
    {
        return $this->hasOne(SoftwareLicenseDetail::class);
    }
    public function attachments(): HasMany
    {
        return $this->hasMany(AssetAttachment::class);
    }
    public function maintenanceLogs(): HasMany
    {
        return $this->hasMany(MaintenanceLog::class);
    }
    public function histories(): HasMany
    {
        return $this->hasMany(AssetHistory::class);
    }
}
