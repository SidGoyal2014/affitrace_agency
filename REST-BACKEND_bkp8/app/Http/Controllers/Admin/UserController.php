<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Services\Admin\AdminUserService;

class UserController extends Controller
{
	protected $userService;

	public function __construct(AdminUserService $userService)
	{
		$this->userService = $userService;
	}

	public function login(Request $request)
	{
		return $this->userService->login($request);
	}


}//UserController
