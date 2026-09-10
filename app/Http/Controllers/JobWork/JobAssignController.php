<?php

namespace App\Http\Controllers\JobWork;

use App\Http\Controllers\Controller;
use App\Models\JobAssignment;
use App\Models\JobAssignmentItem;
use App\Models\JobWorker;
use App\Models\Item;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

class JobAssignController extends Controller
{
    public function index(Request $request)
    {
        $assignments = JobAssignment::with('items')->latest()->get();

        if ($request->wantsJson() || $request->ajax()) {
            return response()->json($assignments);
        }

        $jobworkers = JobWorker::where('status', 'Active')->get();
        $items = Item::all();

        $stats = [
            'totalOrders' => JobAssignment::count(),
            'totalIssuedQty' => JobAssignment::sum('issued_qty'),
            'totalProcessValue' => JobAssignment::sum('total_amount'),
            'activeWorkers' => $jobworkers->count()
        ];

        return view('jobwork.assign.index', compact('assignments', 'jobworkers', 'items', 'stats'));
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'job_worker_name' => 'required|string|max:255',
            'process_name' => 'required|string',
            'lot_number' => 'required|string',
            'style_name' => 'required|string',
            'issue_date' => 'required|date',
            'due_date' => 'nullable|date',
            'issued_qty' => 'required|integer|min:1',
            'rate_per_piece' => 'required|numeric|min:0',
            'instructions' => 'nullable|string'
        ]);

        return DB::transaction(function () use ($validated, $request) {
            $count = JobAssignment::count() + 1;
            $jobOrderNo = 'JA-2026-' . str_pad($count, 3, '0', STR_PAD_LEFT);

            $totalAmount = $validated['issued_qty'] * $validated['rate_per_piece'];

            $jobAssignment = JobAssignment::create([
                'job_order_no' => $jobOrderNo,
                'job_worker_name' => $validated['job_worker_name'],
                'process_name' => $validated['process_name'],
                'lot_number' => $validated['lot_number'],
                'style_name' => $validated['style_name'],
                'issue_date' => $validated['issue_date'],
                'due_date' => $validated['due_date'] ?? null,
                'issued_qty' => $validated['issued_qty'],
                'rate_per_piece' => $validated['rate_per_piece'],
                'total_amount' => $totalAmount,
                'status' => 'Issued',
                'instructions' => $validated['instructions'] ?? null
            ]);

            // Default distribution
            $sizes = ['M', 'L', 'XL'];
            $perSize = (int)($validated['issued_qty'] / count($sizes));
            foreach ($sizes as $sz) {
                JobAssignmentItem::create([
                    'job_assignment_id' => $jobAssignment->id,
                    'size' => $sz,
                    'color' => 'Navy Blue',
                    'qty' => $perSize
                ]);
            }

            if ($request->wantsJson() || $request->ajax()) {
                return response()->json(['success' => true, 'job_assignment' => $jobAssignment]);
            }

            return redirect()->route('jobwork.assign.index')->with('success', "Job Order {$jobOrderNo} created.");
        });
    }

    public function show(JobAssignment $assign)
    {
        return response()->json($assign->load('items'));
    }

    public function destroy(JobAssignment $assign)
    {
        $no = $assign->job_order_no;
        $assign->items()->delete();
        $assign->delete();

        if (request()->wantsJson() || request()->ajax()) {
            return response()->json(['success' => true, 'message' => "Job order {$no} removed."]);
        }

        return redirect()->route('jobwork.assign.index')->with('success', "Job order {$no} removed.");
    }
}
