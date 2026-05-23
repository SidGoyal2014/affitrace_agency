<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Services\Admin\AdminCustomDomainService;
use App\Services\Admin\AdminOfferService;
use Illuminate\Http\Request;

class CustomDomainController extends Controller
{
    protected $service;
    protected $offerService;

    public function __construct(AdminCustomDomainService $service, AdminOfferService $offerService)
    {
        $this->service      = $service;
        $this->offerService = $offerService;
    }

    /** GET /admin/custom-domain/list */
    public function list()
    {
        $result = $this->service->listDomains();
        return response()->json($result, $result['code']);
    }

    /** POST /admin/custom-domain/add  { domain: "mybrand.com" } */
    public function add(Request $request)
    {
        $request->validate(['domain' => 'required|string|max:255']);
        $result = $this->service->addDomain($request->input('domain'));
        return response()->json($result, $result['code']);
    }

    /** POST /admin/custom-domain/{id}/verify */
    public function verify(int $id)
    {
        $result = $this->service->verifyDomain($id);
        return response()->json($result, $result['code']);
    }

    /** PATCH /admin/custom-domain/{id}/toggle */
    public function toggle(int $id)
    {
        $result = $this->service->toggleActive($id);
        return response()->json($result, $result['code']);
    }

    /** DELETE /admin/custom-domain/{id} */
    public function destroy(int $id)
    {
        $result = $this->service->deleteDomain($id);
        return response()->json($result, $result['code']);
    }

    /**
     * POST /admin/offer/assign-custom-domain
     * Body: { offer_affiliate_id: int, custom_domain_id: int|null }
     */
    public function assignDomainToLink(Request $request)
    {
        $request->validate([
            'offer_affiliate_id' => 'required|integer',
            'custom_domain_id'   => 'nullable|integer',
        ]);

        $result = $this->offerService->updateOfferAffiliateDomain(
            (int) $request->input('offer_affiliate_id'),
            $request->input('custom_domain_id') !== null ? (int) $request->input('custom_domain_id') : null
        );

        return response()->json($result, $result['code']);
    }
}