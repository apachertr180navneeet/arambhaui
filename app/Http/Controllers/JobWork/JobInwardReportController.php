<?php

namespace App\Http\Controllers\JobWork;

use App\Http\Controllers\Controller;
use App\Models\JobAssignment;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class JobInwardReportController extends Controller
{
    public function index(Request $request)
    {
        if ($request->wantsJson() || $request->ajax()) {
            return response()->json(JobAssignment::with('items')->get());
        }

        return view('dashboard', [
            'user' => Auth::user(),
            'module' => 'jobwork',
            'submodule' => 'inward-report'
        ]);
    }
}
