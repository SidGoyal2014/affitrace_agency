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

	public function list_affiliates()
	{
		return response()->json($this->adminService->listAffiliates());
	}

	public function list_employees()
	{
		return response()->json($this->adminService->listEmployees());
	}

	public function search_users(Request $request)
	{
		$request->validate([
			'q'     => 'required|string|min:2|max:100',
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

}
