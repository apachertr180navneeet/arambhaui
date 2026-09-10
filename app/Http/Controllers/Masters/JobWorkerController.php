<?php

namespace App\Http\Controllers\Masters;

use App\Http\Controllers\Controller;
use App\Models\JobWorker;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class JobWorkerController extends Controller
{
    public function index(Request $request)
    {
        $jobworkers = JobWorker::latest()->get();

        if ($request->wantsJson() || $request->ajax()) {
            return response()->json($jobworkers);
        }

        $stats = [
            'total' => JobWorker::count(),
            'active' => JobWorker::where('status', 'Active')->count(),
            'totalCapacity' => JobWorker::sum('daily_capacity')
        ];

        return view('masters.jobworkers.index', compact('jobworkers', 'stats'));
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'phone' => 'required|string|max:20',
            'skill_type' => 'required|string',
            'rate_per_piece' => 'required|numeric',
            'daily_capacity' => 'nullable|integer',
            'address' => 'nullable|string'
        ]);

        $count = JobWorker::count() + 1;
        $validated['code'] = 'JW-' . str_pad($count, 3, '0', STR_PAD_LEFT);
        $validated['status'] = 'Active';
        $worker = JobWorker::create($validated);

        if ($request->wantsJson() || $request->ajax()) {
            return response()->json(['success' => true, 'job_worker' => $worker]);
        }

        return redirect()->route('masters.jobworkers.index')->with('success', "Job Worker {$worker->name} registered.");
    }

    public function show(JobWorker $jobworker)
    {
        return response()->json($jobworker->load('assignments'));
    }

    public function update(Request $request, JobWorker $jobworker)
    {
        $validated = $request->validate([
            'name' => 'sometimes|required|string|max:255',
            'phone' => 'sometimes|required|string|max:20',
            'skill_type' => 'nullable|string',
            'rate_per_piece' => 'nullable|numeric',
            'daily_capacity' => 'nullable|integer',
            'address' => 'nullable|string',
            'status' => 'nullable|string'
        ]);

        $jobworker->update($validated);

        if ($request->wantsJson() || $request->ajax()) {
            return response()->json(['success' => true, 'job_worker' => $jobworker]);
        }

        return redirect()->route('masters.jobworkers.index')->with('success', "Job Worker {$jobworker->name} updated.");
    }

    public function destroy(JobWorker $jobworker)
    {
        $name = $jobworker->name;
        $jobworker->delete();

        if (request()->wantsJson() || request()->ajax()) {
            return response()->json(['success' => true, 'message' => "Job worker {$name} removed."]);
        }

        return redirect()->route('masters.jobworkers.index')->with('success', "Job worker {$name} removed.");
    }
}
