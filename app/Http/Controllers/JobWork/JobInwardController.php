<?php

namespace App\Http\Controllers\JobWork;

use App\Http\Controllers\Controller;
use App\Models\JobAssignment;
use App\Models\JobInward;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

class JobInwardController extends Controller
{
    public function index(Request $request)
    {
        $query = JobInward::with('jobAssignment')->latest();

        if ($request->has('search') && !empty($request->search)) {
            $search = $request->search;
            $query->where(function ($q) use ($search) {
                $q->where('inward_number', 'like', "%{$search}%")
                  ->orWhere('job_order_no', 'like', "%{$search}%")
                  ->orWhere('lot_number', 'like', "%{$search}%")
                  ->orWhere('job_worker_name', 'like', "%{$search}%")
                  ->orWhere('challan_no', 'like', "%{$search}%")
                  ->orWhere('style_name', 'like', "%{$search}%");
            });
        }

        if ($request->has('status') && !empty($request->status)) {
            $query->where('qc_status', $request->status);
        }

        $inwards = $query->paginate(25);

        if ($request->wantsJson() || $request->ajax()) {
            return response()->json($inwards);
        }

        $stats = [
            'totalInwardQty' => JobInward::sum('received_qty'),
            'totalDefectQty' => JobInward::sum('defect_qty'),
            'totalEntries' => JobInward::count(),
            'totalWastageReturned' => JobInward::sum('wastage_returned_meters')
        ];

        return view('jobwork.inward.index', compact('inwards', 'stats'));
    }

    public function create(Request $request)
    {
        $activeAssignments = JobAssignment::with('items')
            ->whereNotIn('status', ['Completed', 'Cancelled'])
            ->orderBy('id', 'desc')
            ->get();

        // Also allow completed assignments if query specifically asks
        $preselectedId = $request->input('job_order_id');
        $preselected = null;
        if ($preselectedId) {
            $preselected = JobAssignment::with('items')->find($preselectedId);
        }

        $count = JobInward::count() + 1;
        $nextInwardNo = 'JINW-2026-' . str_pad($count, 4, '0', STR_PAD_LEFT);
        while (JobInward::where('inward_number', $nextInwardNo)->exists()) {
            $count++;
            $nextInwardNo = 'JINW-2026-' . str_pad($count, 4, '0', STR_PAD_LEFT);
        }

        return view('jobwork.inward.create', compact('activeAssignments', 'preselected', 'nextInwardNo'));
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'job_assignment_id' => 'required|exists:job_assignments,id',
            'inward_date' => 'required|date',
            'challan_no' => 'nullable|string|max:100',
            'received_qty' => 'required|integer|min:1',
            'defect_qty' => 'nullable|integer|min:0',
            'wastage_returned_meters' => 'nullable|numeric|min:0',
            'qc_status' => 'required|string|max:100',
            'storage_location' => 'nullable|string|max:100',
            'remarks' => 'nullable|string|max:500'
        ]);

        return DB::transaction(function () use ($validated, $request) {
            $assignment = JobAssignment::lockForUpdate()->findOrFail($validated['job_assignment_id']);

            $count = JobInward::count() + 1;
            $inwardNo = 'JINW-2026-' . str_pad($count, 4, '0', STR_PAD_LEFT);
            while (JobInward::where('inward_number', $inwardNo)->exists()) {
                $count++;
                $inwardNo = 'JINW-2026-' . str_pad($count, 4, '0', STR_PAD_LEFT);
            }

            $received = (int)$validated['received_qty'];
            $defect = (int)($validated['defect_qty'] ?? 0);
            $wastage = (float)($validated['wastage_returned_meters'] ?? 0);
            $rate = (float)($assignment->rate_per_piece ?? 0);
            $totalAmount = $received * $rate;

            $inward = JobInward::create([
                'inward_number' => $inwardNo,
                'job_assignment_id' => $assignment->id,
                'job_order_no' => $assignment->job_order_no,
                'lot_number' => $assignment->lot_number,
                'job_worker_id' => $assignment->job_worker_id,
                'job_worker_name' => $assignment->job_worker_name,
                'process_name' => $assignment->process_name,
                'style_name' => $assignment->style_name,
                'inward_date' => $validated['inward_date'],
                'challan_no' => $validated['challan_no'] ?? null,
                'received_qty' => $received,
                'defect_qty' => $defect,
                'wastage_returned_meters' => $wastage,
                'rate_per_piece' => $rate,
                'total_amount' => $totalAmount,
                'qc_status' => $validated['qc_status'] ?? 'Passed QC',
                'storage_location' => $validated['storage_location'] ?? 'Finished Goods Stock',
                'remarks' => $validated['remarks'] ?? null
            ]);

            // Update parent job assignment received & rejected counts
            $assignment->received_qty = (int)($assignment->received_qty + $received);
            $assignment->rejected_qty = (int)($assignment->rejected_qty + $defect);

            $pending = $assignment->issued_qty - $assignment->received_qty - $assignment->rejected_qty;
            if ($pending <= 0) {
                $assignment->status = 'Completed';
            } else {
                $assignment->status = 'Partial Ready';
            }
            $assignment->save();

            if ($request->wantsJson() || $request->ajax()) {
                return response()->json([
                    'success' => true,
                    'message' => "Job Inward {$inwardNo} recorded successfully for Lot #{$assignment->lot_number}!",
                    'inward' => $inward
                ]);
            }

            return redirect()->route('jobwork.inward.index')
                ->with('success', "Job Inward Receipt {$inwardNo} recorded successfully! Received {$received} finished pieces from {$assignment->job_worker_name}.");
        });
    }

    public function destroy($id)
    {
        $inward = JobInward::findOrFail($id);

        DB::transaction(function () use ($inward) {
            $assignment = JobAssignment::find($inward->job_assignment_id);
            if ($assignment) {
                $assignment->received_qty = max(0, $assignment->received_qty - $inward->received_qty);
                $assignment->rejected_qty = max(0, $assignment->rejected_qty - $inward->defect_qty);

                $pending = $assignment->issued_qty - $assignment->received_qty - $assignment->rejected_qty;
                if ($pending <= 0) {
                    $assignment->status = 'Completed';
                } elseif ($assignment->received_qty > 0) {
                    $assignment->status = 'Partial Ready';
                } else {
                    $assignment->status = 'Issued';
                }
                $assignment->save();
            }

            $inward->delete();
        });

        return redirect()->route('jobwork.inward.index')
            ->with('success', "Job Inward record removed.");
    }
}
