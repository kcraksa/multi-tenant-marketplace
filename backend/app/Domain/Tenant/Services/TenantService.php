<?php

declare(strict_types=1);

namespace App\Domain\Tenant\Services;

use App\Domain\Tenant\Models\Tenant;
use App\Domain\User\Models\User;
use Illuminate\Support\Facades\Artisan;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Str;
use Spatie\Permission\Models\Role;
use Spatie\Permission\PermissionRegistrar;
use Stancl\Tenancy\Facades\Tenancy;

class TenantService
{
    public function createTenant(array $data): array
    {
        $tenant = null;
        $normalizedDomain = $this->normalizeDomain($data['domain'] ?? null);

        try {
            $tenant = Tenant::create([
                'id' => $this->resolveTenantId($data, $normalizedDomain),
                'name' => $data['name'],
                'email' => $data['email'],
                'phone' => $data['phone'] ?? null,
                'address' => $data['address'] ?? null,
                'logo' => $data['logo'] ?? null,
                'status' => $data['status'] ?? true,
                'plan' => $data['plan'] ?? 'basic',
                'data' => $data['data'] ?? null,
            ]);

            if ($normalizedDomain) {
                $tenant->domains()->create([
                    'domain' => $normalizedDomain,
                ]);
            }

            $generatedPassword = $this->setupTenant($tenant, (bool) ($data['seed'] ?? false), $data);

            return [
                'success' => true,
                'tenant' => $tenant->load('domains'),
                'message' => 'Tenant created successfully',
                'admin_credentials' => $generatedPassword ? [
                    'email' => $data['email'],
                    'password' => $generatedPassword,
                ] : null,
            ];
        } catch (\Throwable $e) {
            if ($tenant && $tenant->exists) {
                $tenant->delete();
            }

            return [
                'success' => false,
                'message' => 'Failed to create tenant: ' . $e->getMessage(),
            ];
        }
    }

    private function setupTenant(Tenant $tenant, bool $seed, array $data): ?string
    {
        Artisan::call('tenants:migrate', [
            '--tenants' => [$tenant->id],
        ]);

        if ($seed) {
            Artisan::call('tenants:seed', [
                '--tenants' => [$tenant->id],
            ]);
        }

        return $this->createPrimaryTenantUser($tenant, $data);
    }

    public function updateTenant(string $id, array $data): array
    {
        try {
            $tenant = Tenant::find($id);

            if (!$tenant) {
                return [
                    'success' => false,
                    'message' => 'Tenant not found',
                ];
            }

            $tenant->update($data);

            return [
                'success' => true,
                'tenant' => $tenant->load('domains'),
                'message' => 'Tenant updated successfully',
            ];
        } catch (\Exception $e) {
            return [
                'success' => false,
                'message' => 'Failed to update tenant: ' . $e->getMessage(),
            ];
        }
    }

    public function deleteTenant(string $id): array
    {
        try {
            $tenant = Tenant::find($id);

            if (!$tenant) {
                return [
                    'success' => false,
                    'message' => 'Tenant not found',
                ];
            }

            // Delete tenant (this will also drop the database)
            $tenant->delete();

            return [
                'success' => true,
                'message' => 'Tenant deleted successfully',
            ];
        } catch (\Exception $e) {
            return [
                'success' => false,
                'message' => 'Failed to delete tenant: ' . $e->getMessage(),
            ];
        }
    }

    public function getAllTenants()
    {
        return Tenant::with('domains')->latest()->get();
    }

    public function getTenant(string $id): ?Tenant
    {
        return Tenant::with('domains')->find($id);
    }

    public function getTenantByDomain(string $domain): ?Tenant
    {
        $normalizedDomain = $this->normalizeDomain($domain);

        if (!$normalizedDomain) {
            return null;
        }

        return Tenant::whereHas('domains', function ($query) use ($normalizedDomain) {
            $query->where('domain', $normalizedDomain);
        })->with('domains')->first();
    }

    private function createPrimaryTenantUser(Tenant $tenant, array $data): ?string
    {
        if (empty($data['email'])) {
            return null;
        }

        $plainPassword = $data['password'] ?? Str::random(12);

        Tenancy::initialize($tenant);

        try {
            app(PermissionRegistrar::class)->forgetCachedPermissions();

            $adminRole = Role::firstOrCreate([
                'name' => 'admin',
                'guard_name' => 'web',
            ]);

            $user = User::updateOrCreate(
                ['email' => $data['email']],
                [
                    'name' => $data['name'] ?? 'Administrator',
                    'password' => Hash::make($plainPassword),
                    'phone' => $data['phone'] ?? null,
                    'address' => $data['address'] ?? null,
                    'city' => $data['city'] ?? null,
                    'state' => $data['state'] ?? null,
                    'country' => $data['country'] ?? null,
                    'postal_code' => $data['postal_code'] ?? null,
                    'is_active' => true,
                ]
            );

            $user->syncRoles([$adminRole->name]);
        } finally {
            Tenancy::end();
        }

        return $plainPassword;
    }

    private function resolveTenantId(array $data, ?string $normalizedDomain = null): string
    {
        if (!empty($data['id'])) {
            return (string) $data['id'];
        }

        $domainForId = $normalizedDomain ?? $this->normalizeDomain($data['domain'] ?? null);

        return $this->generateTenantId($domainForId);
    }

    private function generateTenantId(?string $domain): string
    {
        if ($domain) {
            $slug = Str::slug($domain, '_');

            if ($slug !== '') {
                return $this->ensureUniqueTenantId('tenant_' . $slug);
            }
        }

        return $this->ensureUniqueTenantId('tenant_' . Str::uuid()->toString());
    }

    private function normalizeDomain(?string $domain): ?string
    {
        if (!$domain) {
            return null;
        }

        $trimmed = strtolower(trim($domain));

        if ($trimmed === '') {
            return null;
        }

        $trimmed = preg_replace('#^https?://#', '', $trimmed);
        $trimmed = preg_replace('#/.*$#', '', $trimmed);
        $trimmed = rtrim($trimmed ?? '', '.');

        if ($trimmed === '') {
            return null;
        }

        if (str_contains($trimmed, ':')) {
            $trimmed = explode(':', $trimmed)[0];
        }

        if (!str_contains($trimmed, '.') && $this->shouldAppendDomainSuffix()) {
            $trimmed .= '.' . $this->getDomainSuffix();
        }

        return $trimmed;
    }

    private function shouldAppendDomainSuffix(): bool
    {
        if (config('tenancy.tenant_domain_suffix')) {
            return true;
        }

        return app()->environment('local');
    }

    private function getDomainSuffix(): string
    {
        if ($configured = config('tenancy.tenant_domain_suffix')) {
            return $configured;
        }

        $centralDomains = config('tenancy.central_domains', []);

        foreach ($centralDomains as $domain) {
            if ($domain && $domain !== '127.0.0.1') {
                return $domain;
            }
        }

        return 'localhost';
    }

    private function ensureUniqueTenantId(string $baseId): string
    {
        $candidate = $baseId;
        $counter = 1;

        while (Tenant::whereKey($candidate)->exists()) {
            $candidate = $baseId . '_' . $counter;
            $counter++;
        }

        return $candidate;
    }
}
