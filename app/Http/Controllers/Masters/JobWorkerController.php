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
        $query = JobWorker::withCount(['assignments']);

        if ($request->has('search') && !empty($request->search)) {
            $search = $request->search;
            $query->where(function ($q) use ($search) {
                $q->where('name', 'like', "%{$search}%")
                  ->orWhere('contact_person', 'like', "%{$search}%")
                  ->orWhere('phone', 'like', "%{$search}%")
                  ->orWhere('email', 'like', "%{$search}%")
                  ->orWhere('skill_type', 'like', "%{$search}%")
                  ->orWhere('city', 'like', "%{$search}%")
                  ->orWhere('code', 'like', "%{$search}%");
            });
        }

        if ($request->has('status') && !empty($request->status)) {
            $query->where('status', $request->status);
        }

        if ($request->has('skill_type') && !empty($request->skill_type)) {
            $query->where('skill_type', $request->skill_type);
        }

        $jobworkers = $query->latest()->get();

        if ($request->wantsJson() || $request->ajax()) {
            return response()->json($jobworkers);
        }

        $stats = $this->getStats();
        return view('masters.jobworkers.index', compact('jobworkers', 'stats'));
    }

    private function getStats()
    {
        return [
            'total' => JobWorker::count(),
            'active' => JobWorker::whereRaw('LOWER(status) = ?', ['active'])->count(),
            'totalCapacity' => (int) JobWorker::sum('daily_capacity'),
            'totalOutstanding' => (float) JobWorker::sum('outstanding'),
            'stitchingCount' => JobWorker::where('skill_type', 'like', '%stitch%')->count(),
            'cuttingCount' => JobWorker::where('skill_type', 'like', '%cut%')->count()
        ];
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'phone' => 'required|string|max:30',
            'code' => 'nullable|string|max:50',
            'skill_type' => 'required|string|max:255',
            'contact_person' => 'nullable|string|max:255',
            'email' => 'nullable|email|max:255',
            'rate_per_piece' => 'required|numeric|min:0',
            'daily_capacity' => 'nullable|integer|min:0',
            'address' => 'nullable|string',
            'city' => 'nullable|string|max:100',
            'state' => 'nullable|string|max:100',
            'pincode' => 'nullable|string|max:20',
            'outstanding' => 'nullable|numeric',
            'status' => 'nullable|string'
        ]);

        if (empty($validated['status'])) {
            $validated['status'] = 'Active';
        } else {
            $validated['status'] = ucfirst(strtolower($validated['status']));
        }

        if (empty($validated['code'])) {
            $count = JobWorker::count() + 1;
            $validated['code'] = 'JW-' . str_pad($count, 3, '0', STR_PAD_LEFT);
        }

        $validated['daily_capacity'] = isset($validated['daily_capacity']) ? intval($validated['daily_capacity']) : 250;
        $validated['rate_per_piece'] = floatval($validated['rate_per_piece']);

        $worker = JobWorker::create($validated);

        if ($request->wantsJson() || $request->ajax()) {
            return response()->json([
                'success' => true,
                'job_worker' => $worker,
                'stats' => $this->getStats(),
                'message' => "Job Worker {$worker->name} registered successfully."
            ]);
        }

        return redirect()->route('masters.jobworkers.index')->with('success', "Job Worker {$worker->name} registered successfully.");
    }

    public function show(JobWorker $jobworker)
    {
        return response()->json([
            'success' => true,
            'job_worker' => $jobworker->load('assignments')
        ]);
    }

    public function update(Request $request, JobWorker $jobworker)
    {
        $validated = $request->validate([
            'name' => 'sometimes|required|string|max:255',
            'phone' => 'sometimes|required|string|max:30',
            'code' => 'nullable|string|max:50',
            'skill_type' => 'nullable|string|max:255',
            'contact_person' => 'nullable|string|max:255',
            'email' => 'nullable|email|max:255',
            'rate_per_piece' => 'sometimes|required|numeric|min:0',
            'daily_capacity' => 'nullable|integer|min:0',
            'address' => 'nullable|string',
            'city' => 'nullable|string|max:100',
            'state' => 'nullable|string|max:100',
            'pincode' => 'nullable|string|max:20',
            'outstanding' => 'nullable|numeric',
            'status' => 'nullable|string'
        ]);

        if (isset($validated['status'])) {
            $validated['status'] = ucfirst(strtolower($validated['status']));
        }

        $jobworker->update($validated);

        if ($request->wantsJson() || $request->ajax()) {
            return response()->json([
                'success' => true,
                'job_worker' => $jobworker,
                'stats' => $this->getStats(),
                'message' => "Job Worker {$jobworker->name} updated successfully."
            ]);
        }

        return redirect()->route('masters.jobworkers.index')->with('success', "Job Worker {$jobworker->name} updated successfully.");
    }

    public function destroy(JobWorker $jobworker)
    {
        $name = $jobworker->name;
        $jobworker->delete();

        if (request()->wantsJson() || request()->ajax()) {
            return response()->json([
                'success' => true,
                'stats' => $this->getStats(),
                'message' => "Job Worker {$name} removed successfully."
            ]);
        }

        return redirect()->route('masters.jobworkers.index')->with('success', "Job Worker {$name} removed successfully.");
    }
}
