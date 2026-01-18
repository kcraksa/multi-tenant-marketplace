<?php

declare(strict_types=1);

namespace App\Http\Middleware;

use App\Domain\Tenant\Models\Domain;
use App\Domain\Tenant\Models\Tenant;
use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Str;
use Stancl\Tenancy\Facades\Tenancy;

class InitializeTenancyByHeader
{
    public function __construct(private Domain $domainModel)
    {
    }

    public function handle(Request $request, Closure $next)
    {
        $tenantIdentifier = $this->extractTenantIdentifier($request);

        if (!$tenantIdentifier) {
            return $next($request);
        }

        $normalizedIdentifier = $this->normalizeIdentifier($tenantIdentifier);

        if (!$normalizedIdentifier) {
            return $next($request);
        }

        $tenant = $this->resolveTenant($normalizedIdentifier);

        if (!$tenant) {
            abort(404, 'Tenant not found for provided identifier');
        }

        Tenancy::initialize($tenant);

        try {
            return $next($request);
        } finally {
            Tenancy::end();
        }
    }

    private function extractTenantIdentifier(Request $request): ?string
    {
        $value = $request->header('X-Tenant-Domain')
            ?? $request->header('X-Tenant')
            ?? $request->header('X-Tenant-Id')
            ?? $request->header('X-Tenant-Slug');

        if ($value !== null) {
            $value = trim((string) $value);

            if ($value !== '') {
                return $value;
            }
        }

        $host = $request->getHost();

        if ($host && !$this->isCentralDomain($host)) {
            return $host;
        }

        return null;
    }

    private function normalizeIdentifier(string $value): ?string
    {
        $normalized = strtolower(trim($value));

        if ($normalized === '') {
            return null;
        }

        $normalized = preg_replace('#^https?://#', '', $normalized);
        $normalized = preg_replace('#/.*$#', '', $normalized);

        if (str_contains($normalized, ':')) {
            $normalized = explode(':', $normalized)[0];
        }

        if (str_starts_with($normalized, 'www.')) {
            $normalized = substr($normalized, 4);
        }

        $normalized = rtrim($normalized, '.');

        return $normalized !== '' ? $normalized : null;
    }

    private function resolveTenant(string $identifier): ?Tenant
    {
        if ($tenant = $this->resolveByDomain($identifier)) {
            return $tenant;
        }

        return $this->resolveByTenantIds($identifier);
    }

    private function resolveByDomain(string $identifier): ?Tenant
    {
        foreach ($this->buildDomainCandidates($identifier) as $candidate) {
            $domain = $this->domainModel->newQuery()
                ->where('domain', $candidate)
                ->first();

            if ($domain) {
                return $domain->tenant;
            }
        }

        if (!str_contains($identifier, '.')) {
            $domain = $this->domainModel->newQuery()
                ->where(function ($query) use ($identifier) {
                    $query->where('domain', 'like', $identifier . '.%')
                          ->orWhere('domain', 'like', $identifier . '%');
                })
                ->first();

            if ($domain) {
                return $domain->tenant;
            }
        }

        return null;
    }

    private function buildDomainCandidates(string $identifier): array
    {
        $candidates = [$identifier];

        if (!str_contains($identifier, '.')) {
            foreach ($this->getDomainSuffixCandidates() as $suffix) {
                $suffix = trim((string) $suffix);

                if ($suffix !== '') {
                    $candidates[] = $identifier . '.' . $suffix;
                }
            }
        }

        return array_values(array_unique(array_filter($candidates)));
    }

    private function getDomainSuffixCandidates(): array
    {
        $suffixes = [];

        if ($configured = config('tenancy.tenant_domain_suffix')) {
            $suffixes[] = $configured;
        }

        foreach (config('tenancy.central_domains', []) as $domain) {
            if ($domain) {
                $suffixes[] = $domain;
            }
        }

        $suffixes[] = 'localhost';

        return array_values(array_unique($suffixes));
    }

    private function resolveByTenantIds(string $identifier): ?Tenant
    {
        if ($tenant = Tenant::find($identifier)) {
            return $tenant;
        }

        $slug = Str::slug($identifier, '_');

        if ($slug === '') {
            return null;
        }

        $candidates = array_unique(array_filter([
            $slug,
            'tenant_' . $slug,
        ]));

        foreach ($candidates as $candidate) {
            if ($tenant = Tenant::find($candidate)) {
                return $tenant;
            }
        }

        return Tenant::where('id', 'like', 'tenant_' . $slug . '%')->first();
    }

    private function isCentralDomain(string $host): bool
    {
        $centralDomains = config('tenancy.central_domains', []);

        return in_array($host, $centralDomains, true);
    }
}
