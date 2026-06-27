<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Services\Admin\AdminService;

class AdminController extends Controller
{
	protected $adminService;

	public function __construct(AdminService $adminService)
	{
		$this->adminService = $adminService;
	}

	public function list_advertisers()
	{
		return response()->json($this->adminService->listAdvertisers());
	}

	public function list_affiliates(Request $request)
	{
		return response()->json($this->adminService->listAffiliates($request));
	}

	public function list_employees()
	{
		return response()->json($this->adminService->listEmployees());
	}

	public function search_users(Request $request)
	{
		$request->validate([
			'q' => 'required|string|min:2|max:100',
			'limit' => 'sometimes|integer|min:1|max:50',
		]);

		$result = $this->adminService->searchUsers(
			$request->q,
			(int) $request->input('limit', 20)
		);

		return response()->json($result);
	}

	public function profile(Request $request)
	{
		// Auth check handled by middleware
		return response()->json($this->adminService->getProfileData($request->user()));
	}

	function update_profile(Request $request)
	{
		$result = $this->adminService->updateProfile($request);
		return response()->json($result, $result['code'] ?? 200);
	}


	public function edit_affiliate($id)
	{
		$result = $this->adminService->editAffiliate($id);
		if (isset($result['code']) && $result['code'] != 200) {
			return response()->json($result, $result['code']);
		}
		return response()->json($result);
	}


	public function save_affiliate(Request $request)
	{
		$result = $this->adminService->saveAffiliate($request);
		return response()->json($result, $result['code'] ?? 200);
	}

	public function create_employee()
	{
		return response()->json([
			'module_name' => 'Create Employee',
			'title' => 'Create Employee'
		]);
	}

	public function save_employee(Request $request)
	{
		$result = $this->adminService->saveEmployee($request);
		return response()->json($result, $result['code'] ?? 200);
	}

	public function edit_employee($id)
	{
		$result = $this->adminService->editEmployee($id);
		if (isset($result['code']) && $result['code'] != 200) {
			return response()->json($result, $result['code']);
		}
		return response()->json($result);
	}

	public function create_affiliate()
	{
		return response()->json([
			'module_name' => 'Create Affiliate',
			'title' => 'Create Affiliate'
		]);
	}

	public function create_advertiser()
	{
		return response()->json([
			'module_name' => 'Create Advertiser',
			'title' => 'Create Advertiser',
			'form_action_url' => 'doRegister'
		]);
	}

	public function save_advertiser(Request $request)
	{
		$result = $this->adminService->saveAdvertiser($request);
		return response()->json($result, $result['code'] ?? 200);
	}

	public function edit_advertiser($id)
	{
		$result = $this->adminService->editAdvertiser($id);
		if (isset($result['code']) && $result['code'] != 200) {
			return response()->json($result, $result['code']);
		}
		return response()->json($result);
	}

	function update_advertiser(Request $request)
	{
		return $this->adminService->updateAdvertiserProfile($request);
	}

	function update_affiliate(Request $request)
	{
		return $this->adminService->updateAffiliateProfile($request);
	}

	function update_employee(Request $request)
	{
		return $this->adminService->updateEmployeeProfile($request);
	}

	public function update_affiliate_status(Request $request, $id)
	{
		$request->validate([
			'status' => 'required|string|in:active,pending,rejected,suspended',
			'reason' => 'nullable|string'
		]);
		return response()->json($this->adminService->updateAffiliateStatus($id, $request->status, $request->reason));
	}

	public function bulk_affiliates_status(Request $request)
	{
		$request->validate([
			'ids' => 'required|array',
			'ids.*' => 'integer',
			'status' => 'required|string|in:active,pending,rejected,suspended',
			'reason' => 'nullable|string'
		]);
		return response()->json($this->adminService->bulkAffiliatesStatus($request->ids, $request->status, $request->reason));
	}

	public function delete_affiliate($id)
	{
		return response()->json($this->adminService->deleteAffiliate($id));
	}

	public function assign_affiliate_manager(Request $request, $id)
	{
		$request->validate([
			'employee_id' => 'nullable|integer'
		]);
		return response()->json($this->adminService->assignAffiliateManager($id, $request->employee_id));
	}

	public function get_affiliate_stats($id)
	{
		return response()->json($this->adminService->getAffiliateStats($id));
	}

	public function save_payout_override(Request $request)
	{
		$request->validate([
			'offer_id' => 'required|integer',
			'affiliate_id' => 'required|integer',
			'payout' => 'nullable|numeric|min:0',
			'revenue' => 'nullable|numeric|min:0'
		]);
		return response()->json($this->adminService->savePayoutOverride($request));
	}

	public function get_affiliate_referrals($id)
	{
		return response()->json($this->adminService->getAffiliateReferrals($id));
	}

	public function export_affiliates(Request $request)
	{
		return $this->adminService->exportAffiliates($request);
	}

	public function upload_affiliate_doc(Request $request)
	{
		$request->validate([
			'uid' => 'required|integer',
			'doc' => 'required|file|max:10240'
		]);
		return response()->json($this->adminService->uploadAffiliateDoc($request));
	}

	public function update_advertiser_status(Request $request, $id)
	{
		$request->validate([
			'status' => 'required|string|in:active,pending,rejected,suspended',
			'reason' => 'nullable|string'
		]);
		return response()->json($this->adminService->updateAdvertiserStatus($id, $request->status, $request->reason));
	}

	public function update_advertiser_notes_tags(Request $request, $id)
	{
		$request->validate([
			'notes' => 'nullable|string',
			'tags' => 'nullable|string'
		]);
		return response()->json($this->adminService->updateAdvertiserNotesTags($id, $request->notes, $request->tags));
	}

	public function get_advertiser_offers($id)
	{
		return response()->json($this->adminService->getAdvertiserOffers($id));
	}

	public function get_advertiser_stats($id)
	{
		return response()->json($this->adminService->getAdvertiserStats($id));
	}

	public function get_advertiser_invoices($id)
	{
		return response()->json($this->adminService->getAdvertiserInvoices($id));
	}

	public function export_advertisers(Request $request)
	{
		return $this->adminService->exportAdvertisers($request);
	}

	public function get_settings(Request $request)
	{

		$user = $request->user();
		if (strtolower($user->user_type) !== 'admin' && !$user->hasModuleAccess('settings', 'can_view')) {
			return response()->json(['message' => 'You do not have permission to view settings.'], 403);
		}

		return response()->json($this->adminService->getSettingsData());
	}

	public function update_settings(Request $request)
	{

		$user = $request->user();
		if (strtolower($user->user_type) !== 'admin' && !$user->hasModuleAccess('settings', 'can_edit')) {
			return response()->json(['message' => 'You do not have permission to edit settings.'], 403);
		}

		$result = $this->adminService->saveSettings($request);
		return response()->json($result, $result['code'] ?? 200);
	}

}


