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
        if ($request->wantsJson() || $request->ajax()) {
            return response()->json(JobWorker::latest()->get());
        }

        return view('dashboard', [
            'user' => Auth::user(),
            'module' => 'masters',
            'submodule' => 'jobworkers'
        ]);
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
        $worker = JobWorker::create($validated);

        if ($request->wantsJson() || $request->ajax()) {
            return response()->json(['success' => true, 'job_worker' => $worker]);
        }

        return redirect()->route('masters.jobworkers.index')->with('success', 'Job Worker registered.');
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
            'status' => 'nullable|string'
        ]);

        $jobworker->update($validated);
        return response()->json(['success' => true, 'job_worker' => $jobworker]);
    }

    public function destroy(JobWorker $jobworker)
    {
        $jobworker->delete();
        return response()->json(['success' => true, 'message' => 'Job worker removed.']);
    }
}
