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
     * Get the custom columns for the tenant.
     */
    public static function getCustomColumns(): array
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
