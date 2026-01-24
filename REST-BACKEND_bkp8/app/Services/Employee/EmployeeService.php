<?php

namespace App\Services\Employee;

use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Auth;

class EmployeeService
{
    public function getProfile($user)
    {
        $logged_in_user_type = $user->user_type;

        return [
            'module_name' => 'Profile',
            'user' => $user,
            'title' => ucfirst($logged_in_user_type) . ' Profile',
            'logged_in_user_type' => $logged_in_user_type
        ];
    }

    public function updateProfile($request)
    {
        $user = Auth::user();

        $user->name = $request->name;
        $user->email = $request->email;
        $user->phone = $request->phone;
        $user->gender = $request->gender;

        if (strlen($request->password) > 0) {
            $user->password = Hash::make($request->password);
        }

        $user->save();

        return ['status' => 1, 'msg' => 'Profile Updated'];
    }
}
