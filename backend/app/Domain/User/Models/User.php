<?php

declare(strict_types=1);

namespace App\Domain\User\Models;

use App\Domain\Cart\Models\Cart;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Laravel\Sanctum\HasApiTokens;
use Spatie\Permission\Traits\HasRoles;

class User extends Authenticatable
{
    use HasApiTokens, HasFactory, Notifiable, HasRoles;

    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
    protected $fillable = [
        'name',
        'email',
        'password',
        'phone',
        'address',
        'city',
        'state',
        'country',
        'postal_code',
        'is_active',
    ];

    /**
     * The attributes that should be hidden for serialization.
     *
     * @var array<int, string>
     */
    protected $hidden = [
        'password',
        'remember_token',
    ];

    /**
     * The attributes that should be cast.
     *
     * @var array<string, string>
     */
    protected $casts = [
        'email_verified_at' => 'datetime',
        'password' => 'hashed',
        'is_active' => 'boolean',
    ];

    /**
     * Get the carts for the user.
     */
    public function carts(): HasMany
    {
        return $this->hasMany(Cart::class);
    }

    /**
     * Get the active cart for the user.
     */
    public function activeCart()
    {
        return $this->carts()->where('status', 'active')->first();
    }

    /**
     * Check if user is admin.
     */
    public function isAdmin(): bool
    {
        return $this->hasRole('admin');
    }

    /**
     * Check if user is customer.
     */
    public function isCustomer(): bool
    {
        return $this->hasRole('customer');
    }

    /**
     * Override apiTokens to use central database connection.
     */
    public function apiTokens()
    {
        return $this->morphMany(\App\Models\PersonalAccessToken::class, 'tokenable');
    }

    /**
     * Override createToken to use central database.
     */
    public function createToken(string $name, array $abilities = ['*'], \DateTimeInterface $expiresAt = null)
    {
        $originalConnection = $this->getConnectionName();
        $this->setConnection('mysql');

        try {
            $plainTextToken = \Illuminate\Support\Str::random(40);

            $token = $this->apiTokens()->create([
                'name' => $name,
                'token' => hash('sha256', $plainTextToken),
                'abilities' => $abilities,
                'expires_at' => $expiresAt,
            ]);

            return new \Laravel\Sanctum\NewAccessToken($token, $token->getKey() . '|' . $plainTextToken);
        } finally {
            $this->setConnection($originalConnection);
        }
    }
}
