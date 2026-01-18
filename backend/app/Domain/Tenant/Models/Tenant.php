<?php

declare(strict_types=1);

namespace App\Domain\Tenant\Models;

use Stancl\Tenancy\Database\Models\Tenant as BaseTenant;
use Stancl\Tenancy\Contracts\TenantWithDatabase;
use Stancl\Tenancy\Database\Concerns\HasDatabase;
use Stancl\Tenancy\Database\Concerns\HasDomains;

class Tenant extends BaseTenant implements TenantWithDatabase
{
    use HasDatabase, HasDomains;

    /**
     * The attributes that are mass assignable.
     *
     * @var array<string>
     */
    protected $fillable = [
        'id',
        'name',
        'email',
        'phone',
        'address',
        'logo',
        'status',
        'plan',
        'data',
    ];

    /**
     * The attributes that should be cast.
     *
     * @var array<string, string>
     */
    protected $casts = [
        'data' => 'array',
        'status' => 'boolean',
    ];

    /**
     * Columns stored directly on the tenants table instead of the data JSON.
     */
    public static function getCustomColumns(): array
    {
        return array_unique(array_merge(parent::getCustomColumns(), [
            'name',
            'email',
            'phone',
            'address',
            'logo',
            'status',
            'plan',
            'created_at',
            'updated_at',
        ]));
    }

    /**
     * Get the internal columns that should not be stored in data.
     */
    public function getInternalColumns(): array
    {
        return [
            'id',
            'name',
            'email',
            'phone',
            'address',
            'logo',
            'status',
            'plan',
            'data',
            'created_at',
            'updated_at',
        ];
    }

    /**
     * Check if tenant is active.
     */
    public function isActive(): bool
    {
        return $this->status === true;
    }
}
