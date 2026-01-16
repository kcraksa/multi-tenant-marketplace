<?php

declare(strict_types=1);

namespace App\Application\Controllers;

use App\Application\Requests\TenantRequest;
use App\Domain\Tenant\Services\TenantService;
use Illuminate\Http\Request;

class TenantController extends Controller
{
    public function __construct(
        private TenantService $tenantService
    ) {}

    /**
     * Display a listing of tenants.
     */
    public function index()
    {
        $tenants = $this->tenantService->getAllTenants();
        return $this->successResponse($tenants);
    }

    /**
     * Store a newly created tenant.
     */
    public function store(TenantRequest $request)
    {
        $result = $this->tenantService->createTenant($request->validated());

        if (!$result['success']) {
            return $this->errorResponse($result['message'], 500);
        }

        return $this->successResponse($result['tenant'], $result['message'], 201);
    }

    /**
     * Display the specified tenant.
     */
    public function show(string $id)
    {
        $tenant = $this->tenantService->getTenant($id);

        if (!$tenant) {
            return $this->errorResponse('Tenant not found', 404);
        }

        return $this->successResponse($tenant);
    }

    /**
     * Update the specified tenant.
     */
    public function update(TenantRequest $request, string $id)
    {
        $result = $this->tenantService->updateTenant($id, $request->validated());

        if (!$result['success']) {
            return $this->errorResponse($result['message'], $result['message'] === 'Tenant not found' ? 404 : 500);
        }

        return $this->successResponse($result['tenant'], $result['message']);
    }

    /**
     * Remove the specified tenant.
     */
    public function destroy(string $id)
    {
        $result = $this->tenantService->deleteTenant($id);

        if (!$result['success']) {
            return $this->errorResponse($result['message'], $result['message'] === 'Tenant not found' ? 404 : 500);
        }

        return $this->successResponse(null, $result['message']);
    }
}
