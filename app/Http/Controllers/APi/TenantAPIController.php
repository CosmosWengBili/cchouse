<?php

namespace App\Http\Controllers\API;

use App\Http\Requests\API\CreateTenantAPIRequest;
use App\Http\Requests\API\UpdateTenantAPIRequest;
use App\Models\Tenant;
use App\Repositories\TenantRepository;
use Illuminate\Http\Request;
use App\Http\Controllers\AppBaseController;
use Response;

/**
 * Class TenantController
 * @package App\Http\Controllers\API
 */

class TenantAPIController extends AppBaseController
{
    /** @var  TenantRepository */
    private $tenantRepository;

    public function __construct(TenantRepository $tenantRepo)
    {
        $this->tenantRepository = $tenantRepo;
    }

    /**
     * Display a listing of the Tenant.
     * GET|HEAD /tenants
     *
     * @param Request $request
     * @return Response
     */
    public function index(Request $request)
    {
        $tenants = $this->tenantRepository->all(
            $request->except(['skip', 'limit']),
            $request->get('skip'),
            $request->get('limit')
        );

        return $this->sendResponse($tenants->toArray(), 'Tenants retrieved successfully');
    }

    /**
     * Store a newly created Tenant in storage.
     * POST /tenants
     *
     * @param CreateTenantAPIRequest $request
     *
     * @return Response
     */
    public function store(CreateTenantAPIRequest $request)
    {
        $input = $request->all();

        $tenant = $this->tenantRepository->create($input);

        return $this->sendResponse($tenant->toArray(), 'Tenant saved successfully');
    }

    /**
     * Display the specified Tenant.
     * GET|HEAD /tenants/{id}
     *
     * @param int $id
     *
     * @return Response
     */
    public function show($id)
    {
        /** @var Tenant $tenant */
        $tenant = $this->tenantRepository->find($id);

        if (empty($tenant)) {
            return $this->sendError('Tenant not found');
        }

        return $this->sendResponse($tenant->toArray(), 'Tenant retrieved successfully');
    }

    /**
     * Update the specified Tenant in storage.
     * PUT/PATCH /tenants/{id}
     *
     * @param int $id
     * @param UpdateTenantAPIRequest $request
     *
     * @return Response
     */
    public function update($id, UpdateTenantAPIRequest $request)
    {
        $input = $request->all();

        /** @var Tenant $tenant */
        $tenant = $this->tenantRepository->find($id);

        if (empty($tenant)) {
            return $this->sendError('Tenant not found');
        }

        $tenant = $this->tenantRepository->update($input, $id);

        return $this->sendResponse($tenant->toArray(), 'Tenant updated successfully');
    }

    /**
     * Remove the specified Tenant from storage.
     * DELETE /tenants/{id}
     *
     * @param int $id
     *
     * @throws \Exception
     *
     * @return Response
     */
    public function destroy($id)
    {
        /** @var Tenant $tenant */
        $tenant = $this->tenantRepository->find($id);

        if (empty($tenant)) {
            return $this->sendError('Tenant not found');
        }

        $tenant->delete();

        return $this->sendResponse($id, 'Tenant deleted successfully');
    }
}
