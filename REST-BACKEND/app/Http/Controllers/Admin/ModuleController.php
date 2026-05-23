<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Module;

class ModuleController extends Controller
{
    /**
     * List all modules (read-only, used by UI for role builder).
     */
    public function index()
    {
        $modules = Module::orderBy('name')->get();

        return response()->json(['modules' => $modules]);
    }
}
