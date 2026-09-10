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
        $assignments = JobAssignment::with('items')->latest()->get();

        if ($request->wantsJson() || $request->ajax()) {
            return response()->json($assignments);
        }

        $totalIssued = JobAssignment::sum('issued_qty');

        $stats = [
            'totalLots' => JobAssignment::count(),
            'totalIssued' => $totalIssued,
            'completedLots' => JobAssignment::where('status', 'Completed')->count()
        ];

        return view('jobwork.inward-report.index', compact('assignments', 'stats'));
    }
}
