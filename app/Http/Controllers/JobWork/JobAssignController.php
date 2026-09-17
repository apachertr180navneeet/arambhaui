<?php

namespace App\Http\Controllers\JobWork;

use App\Http\Controllers\Controller;
use App\Models\JobAssignment;
use App\Models\JobAssignmentItem;
use App\Models\JobWorker;
use App\Models\Item;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class JobAssignController extends Controller
{
    public function index(Request $request)
    {
        $assignments = JobAssignment::with(['items', 'jobWorker'])->latest()->get();

        if ($request->wantsJson() || $request->ajax()) {
            return response()->json($assignments);
        }

        $jobworkers = JobWorker::where('status', 'Active')->get();
        $items = Item::all();

        $stats = [
            'totalOrders' => JobAssignment::count(),
            'totalIssuedQty' => JobAssignment::sum('issued_qty'),
            'totalThanMeters' => JobAssignment::sum('total_than_meters'),
            'totalProcessValue' => JobAssignment::sum('total_amount'),
            'activeWorkers' => $jobworkers->count()
        ];

        return view('jobwork.assign.index', compact('assignments', 'jobworkers', 'items', 'stats'));
    }

    public static function generateNextLotNumber()
    {
        $year = date('Y');
        $latest = JobAssignment::where('lot_number', 'LIKE', "LOT-{$year}-%")
            ->orderBy('id', 'desc')
            ->value('lot_number');

        if ($latest && preg_match('/LOT-\d{4}-(\d+)/', $latest, $m)) {
            $nextNum = (int)$m[1] + 1;
        } else {
            $nextNum = JobAssignment::count() + 1;
        }

        return "LOT-{$year}-" . str_pad($nextNum, 3, '0', STR_PAD_LEFT);
    }

    public static function generateNextJobOrderNo()
    {
        $year = date('Y');
        $count = JobAssignment::count() + 1;
        return "JA-{$year}-" . str_pad($count, 3, '0', STR_PAD_LEFT);
    }

    public function create()
    {
        $jobworkers = JobWorker::where('status', 'Active')->get();
        $items = Item::all();
        $nextJobOrderNo = self::generateNextJobOrderNo();
        $nextLotNumber = self::generateNextLotNumber();

        return view('jobwork.assign.create', compact('jobworkers', 'items', 'nextJobOrderNo', 'nextLotNumber'));
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'job_worker_name' => 'required|string|max:255',
            'job_worker_id' => 'nullable|integer',
            'process_name' => 'required|string',
            'lot_number' => 'required|string|max:100',
            'style_name' => 'nullable|string|max:255',
            'issue_date' => 'required|date',
            'due_date' => 'nullable|date',
            'instructions' => 'nullable|string',
            'items' => 'required|array|min:1',
            'items.*.item_id' => 'nullable',
            'items.*.item_name' => 'required|string|max:255',
            'items.*.than_meters' => 'nullable|numeric|min:0',
            'items.*.production_pcs' => 'required|numeric|min:0',
            'items.*.wastage_meters' => 'nullable|numeric|min:0',
            'items.*.rate_per_piece' => 'required|numeric|min:0',
            'items.*.size' => 'nullable|string|max:50',
            'items.*.color' => 'nullable|string|max:50',
            'items.*.remarks' => 'nullable|string|max:255'
        ]);

        return DB::transaction(function () use ($validated, $request) {
            $jobWorker = null;
            if (!empty($validated['job_worker_id'])) {
                $jobWorker = JobWorker::find($validated['job_worker_id']);
            } elseif (!empty($validated['job_worker_name'])) {
                $jobWorker = JobWorker::where('name', $validated['job_worker_name'])->first();
            }

            $jobOrderNo = self::generateNextJobOrderNo();

            // Calculate totals across all items
            $totalPcs = 0;
            $totalThan = 0;
            $totalWastage = 0;
            $totalAmount = 0;
            $styleNames = [];

            foreach ($validated['items'] as $it) {
                $pcs = (float)($it['production_pcs'] ?? 0);
                $than = (float)($it['than_meters'] ?? 0);
                $wastage = (float)($it['wastage_meters'] ?? 0);
                $rate = (float)($it['rate_per_piece'] ?? 0);
                $lineTotal = $pcs * $rate;

                $totalPcs += $pcs;
                $totalThan += $than;
                $totalWastage += $wastage;
                $totalAmount += $lineTotal;

                if (!empty($it['item_name'])) {
                    $styleNames[] = $it['item_name'];
                }
            }

            $avgRate = $totalPcs > 0 ? ($totalAmount / $totalPcs) : 0;
            $styleNameSummary = !empty($validated['style_name'])
                ? $validated['style_name']
                : (count($styleNames) > 0 ? implode(', ', array_unique($styleNames)) : 'Garment Style');

            $jobAssignment = JobAssignment::create([
                'job_order_no' => $jobOrderNo,
                'job_worker_id' => $jobWorker ? $jobWorker->id : null,
                'job_worker_name' => $validated['job_worker_name'],
                'process_name' => $validated['process_name'],
                'lot_number' => strtoupper(trim($validated['lot_number'])),
                'style_name' => $styleNameSummary,
                'issue_date' => $validated['issue_date'],
                'due_date' => $validated['due_date'] ?? null,
                'issued_qty' => (int)$totalPcs,
                'total_than_meters' => $totalThan,
                'total_wastage_meters' => $totalWastage,
                'rate_per_piece' => $avgRate,
                'total_amount' => $totalAmount,
                'status' => 'Issued',
                'instructions' => $validated['instructions'] ?? null
            ]);

            // Save line items
            foreach ($validated['items'] as $it) {
                $pcs = (float)($it['production_pcs'] ?? 0);
                $than = (float)($it['than_meters'] ?? 0);
                $wastage = (float)($it['wastage_meters'] ?? 0);
                $rate = (float)($it['rate_per_piece'] ?? 0);
                $lineTotal = $pcs * $rate;
                $avgCons = $pcs > 0 ? max(0, ($than - $wastage) / $pcs) : 0;

                JobAssignmentItem::create([
                    'job_assignment_id' => $jobAssignment->id,
                    'item_id' => !empty($it['item_id']) ? (int)$it['item_id'] : null,
                    'item_name' => $it['item_name'],
                    'than_meters' => $than,
                    'production_pcs' => (int)$pcs,
                    'wastage_meters' => $wastage,
                    'avg_consumption' => $avgCons,
                    'rate_per_piece' => $rate,
                    'total_amount' => $lineTotal,
                    'size' => $it['size'] ?? 'All Sizes',
                    'color' => $it['color'] ?? 'Standard',
                    'qty' => (int)$pcs,
                    'remarks' => $it['remarks'] ?? null
                ]);
            }

            if ($request->wantsJson() || $request->ajax()) {
                return response()->json([
                    'success' => true,
                    'message' => "Job Work Order {$jobOrderNo} (Lot: {$jobAssignment->lot_number}) created successfully.",
                    'job_assignment' => $jobAssignment->load('items')
                ], 201);
            }

            return redirect()->route('jobwork.assign.index')->with('success', "Job Work Order {$jobOrderNo} with Lot #{$jobAssignment->lot_number} issued successfully.");
        });
    }

    public function edit(JobAssignment $assign)
    {
        $jobworkers = JobWorker::where('status', 'Active')->get();
        $items = Item::all();
        $assign->load(['items', 'jobWorker']);

        return view('jobwork.assign.edit', compact('assign', 'jobworkers', 'items'));
    }

    public function update(Request $request, JobAssignment $assign)
    {
        $validated = $request->validate([
            'job_worker_name' => 'required|string|max:255',
            'job_worker_id' => 'nullable|integer',
            'process_name' => 'required|string',
            'lot_number' => 'required|string|max:100',
            'style_name' => 'nullable|string|max:255',
            'issue_date' => 'required|date',
            'due_date' => 'nullable|date',
            'status' => 'nullable|string',
            'instructions' => 'nullable|string',
            'items' => 'required|array|min:1',
            'items.*.item_id' => 'nullable',
            'items.*.item_name' => 'required|string|max:255',
            'items.*.than_meters' => 'nullable|numeric|min:0',
            'items.*.production_pcs' => 'required|numeric|min:0',
            'items.*.wastage_meters' => 'nullable|numeric|min:0',
            'items.*.rate_per_piece' => 'required|numeric|min:0',
            'items.*.size' => 'nullable|string|max:50',
            'items.*.color' => 'nullable|string|max:50',
            'items.*.remarks' => 'nullable|string|max:255'
        ]);

        return DB::transaction(function () use ($validated, $request, $assign) {
            $jobWorker = null;
            if (!empty($validated['job_worker_id'])) {
                $jobWorker = JobWorker::find($validated['job_worker_id']);
            } elseif (!empty($validated['job_worker_name'])) {
                $jobWorker = JobWorker::where('name', $validated['job_worker_name'])->first();
            }

            // Calculate totals
            $totalPcs = 0;
            $totalThan = 0;
            $totalWastage = 0;
            $totalAmount = 0;
            $styleNames = [];

            foreach ($validated['items'] as $it) {
                $pcs = (float)($it['production_pcs'] ?? 0);
                $than = (float)($it['than_meters'] ?? 0);
                $wastage = (float)($it['wastage_meters'] ?? 0);
                $rate = (float)($it['rate_per_piece'] ?? 0);
                $lineTotal = $pcs * $rate;

                $totalPcs += $pcs;
                $totalThan += $than;
                $totalWastage += $wastage;
                $totalAmount += $lineTotal;

                if (!empty($it['item_name'])) {
                    $styleNames[] = $it['item_name'];
                }
            }

            $avgRate = $totalPcs > 0 ? ($totalAmount / $totalPcs) : 0;
            $styleNameSummary = !empty($validated['style_name'])
                ? $validated['style_name']
                : (count($styleNames) > 0 ? implode(', ', array_unique($styleNames)) : $assign->style_name);

            $assign->update([
                'job_worker_id' => $jobWorker ? $jobWorker->id : $assign->job_worker_id,
                'job_worker_name' => $validated['job_worker_name'],
                'process_name' => $validated['process_name'],
                'lot_number' => strtoupper(trim($validated['lot_number'])),
                'style_name' => $styleNameSummary,
                'issue_date' => $validated['issue_date'],
                'due_date' => $validated['due_date'] ?? null,
                'issued_qty' => (int)$totalPcs,
                'total_than_meters' => $totalThan,
                'total_wastage_meters' => $totalWastage,
                'rate_per_piece' => $avgRate,
                'total_amount' => $totalAmount,
                'status' => $validated['status'] ?? $assign->status,
                'instructions' => $validated['instructions'] ?? null
            ]);

            // Re-sync line items
            $assign->items()->delete();
            foreach ($validated['items'] as $it) {
                $pcs = (float)($it['production_pcs'] ?? 0);
                $than = (float)($it['than_meters'] ?? 0);
                $wastage = (float)($it['wastage_meters'] ?? 0);
                $rate = (float)($it['rate_per_piece'] ?? 0);
                $lineTotal = $pcs * $rate;
                $avgCons = $pcs > 0 ? max(0, ($than - $wastage) / $pcs) : 0;

                JobAssignmentItem::create([
                    'job_assignment_id' => $assign->id,
                    'item_id' => !empty($it['item_id']) ? (int)$it['item_id'] : null,
                    'item_name' => $it['item_name'],
                    'than_meters' => $than,
                    'production_pcs' => (int)$pcs,
                    'wastage_meters' => $wastage,
                    'avg_consumption' => $avgCons,
                    'rate_per_piece' => $rate,
                    'total_amount' => $lineTotal,
                    'size' => $it['size'] ?? 'All Sizes',
                    'color' => $it['color'] ?? 'Standard',
                    'qty' => (int)$pcs,
                    'remarks' => $it['remarks'] ?? null
                ]);
            }

            if ($request->wantsJson() || $request->ajax()) {
                return response()->json([
                    'success' => true,
                    'message' => "Job Work Order {$assign->job_order_no} updated successfully.",
                    'job_assignment' => $assign->load('items')
                ]);
            }

            return redirect()->route('jobwork.assign.index')->with('success', "Job Order {$assign->job_order_no} updated successfully.");
        });
    }

    public function show(JobAssignment $assign)
    {
        return response()->json($assign->load(['items', 'jobWorker']));
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
