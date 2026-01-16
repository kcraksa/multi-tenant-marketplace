<?php

declare(strict_types=1);

namespace App\Domain\Tenant\Services;

use App\Domain\Tenant\Models\Tenant;
use Illuminate\Support\Facades\Artisan;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;

class TenantService
{
    public function createTenant(array $data): array
    {
        try {
            DB::beginTransaction();

            // Create tenant
            $tenant = Tenant::create([
                'id' => $data['id'] ?? Str::uuid()->toString(),
                'name' => $data['name'],
                'email' => $data['email'],
                'phone' => $data['phone'] ?? null,
                'address' => $data['address'] ?? null,
                'logo' => $data['logo'] ?? null,
                'status' => $data['status'] ?? true,
                'plan' => $data['plan'] ?? 'basic',
                'data' => $data['data'] ?? null,
            ]);

            // Create domain for tenant
            if (isset($data['domain'])) {
                $tenant->domains()->create([
                    'domain' => $data['domain'],
                ]);
            }

            // Run tenant migrations
            Artisan::call('tenants:migrate', [
                '--tenants' => [$tenant->id],
            ]);

            // Seed initial data if needed
            if (isset($data['seed']) && $data['seed']) {
                Artisan::call('tenants:seed', [
                    '--tenants' => [$tenant->id],
                ]);
            }

            DB::commit();

            return [
                'success' => true,
                'tenant' => $tenant->load('domains'),
                'message' => 'Tenant created successfully',
            ];
        } catch (\Exception $e) {
            DB::rollBack();
            
            return [
                'success' => false,
                'message' => 'Failed to create tenant: ' . $e->getMessage(),
            ];
        }
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
        return Tenant::whereHas('domains', function ($query) use ($domain) {
            $query->where('domain', $domain);
        })->with('domains')->first();
    }
}
