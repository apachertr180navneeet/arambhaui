<?php

namespace App\Http\Controllers\JobWork;

use App\Http\Controllers\Controller;
use App\Models\JobAssignment;
use App\Models\JobAssignmentItem;
use App\Models\JobWorker;
use App\Models\Item;
use App\Models\PurchaseOrder;
use App\Models\PurchaseOrderItem;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

class JobAssignController extends Controller
{
    public function index(Request $request)
    {
        $assignments = JobAssignment::with(['items.rawItem', 'items.finishedItem', 'jobWorker'])->latest()->get();

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

    /**
     * Get available purchase tons/thans mapped by raw item id & name
     */
    protected function getPurchasedTonsByItem()
    {
        $purchasedTons = [];
        $purchaseOrders = PurchaseOrder::with('items')->latest()->get();

        foreach ($purchaseOrders as $po) {
            $challanNo = $po->challan_no ?: $po->po_number;
            foreach ($po->items as $poItem) {
                $thans = $poItem->than_list;
                if (!empty($thans)) {
                    $keys = [];
                    if (!empty($poItem->item_id)) {
                        $keys[] = (string)$poItem->item_id;
                    }
                    if (!empty($poItem->item_name)) {
                        $keys[] = strtolower(trim($poItem->item_name));
                    }

                    foreach ($thans as $tIdx => $tMtr) {
                        $tonObj = [
                            'po_id' => $po->id,
                            'po_number' => $po->po_number,
                            'challan_no' => $challanNo,
                            'date' => $po->po_date,
                            'than_no' => $tIdx + 1,
                            'meter' => (float)$tMtr,
                            'unit' => $poItem->unit ?: 'Mtr',
                            'label' => "Challan #{$challanNo} - Roll/Ton #" . ($tIdx + 1) . " (" . number_format($tMtr, 2) . " " . ($poItem->unit ?: 'Mtr') . ")"
                        ];

                        foreach ($keys as $k) {
                            if (!isset($purchasedTons[$k])) {
                                $purchasedTons[$k] = [];
                            }
                            $purchasedTons[$k][] = $tonObj;
                        }
                    }
                }
            }
        }

        return $purchasedTons;
    }

    public function create()
    {
        $jobworkers = JobWorker::where('status', 'Active')->get();
        $items = Item::all();
        $nextJobOrderNo = self::generateNextJobOrderNo();
        $nextLotNumber = self::generateNextLotNumber();
        $purchasedTonsByItem = $this->getPurchasedTonsByItem();

        return view('jobwork.assign.create', compact('jobworkers', 'items', 'nextJobOrderNo', 'nextLotNumber', 'purchasedTonsByItem'));
    }

    public function store(Request $request)
    {
        // Clean up empty rows before validation
        $rawItems = $request->input('items', []);
        $cleanedItems = [];
        if (is_array($rawItems)) {
            foreach ($rawItems as $it) {
                $hasRaw = !empty($it['raw_item_id']) || !empty($it['raw_item_name']) || !empty($it['item_id']) || !empty($it['item_name']);
                $hasQty = (!empty($it['production_pcs']) && (float)$it['production_pcs'] > 0) || (!empty($it['than_meters']) && (float)$it['than_meters'] > 0) || !empty($it['thans']);
                if ($hasRaw || $hasQty) {
                    $cleanedItems[] = $it;
                }
            }
        }
        $request->merge(['items' => $cleanedItems]);

        $validated = $request->validate([
            'job_worker_name' => 'required|string|max:255',
            'job_worker_id' => 'nullable|integer',
            'process_name' => 'required|string',
            'lot_number' => 'required|string|max:100',
            'style_name' => 'nullable|string|max:255',
            'issue_date' => 'required|date',
            'due_date' => 'nullable|date',
            'instructions' => 'nullable|string',
            'items' => 'nullable|array',
            'items.*.raw_item_id' => 'nullable',
            'items.*.raw_item_name' => 'nullable|string|max:255',
            'items.*.finished_item_id' => 'nullable',
            'items.*.finished_item_name' => 'nullable|string|max:255',
            'items.*.item_id' => 'nullable',
            'items.*.item_name' => 'nullable|string|max:255',
            'items.*.item_code' => 'nullable|string|max:100',
            'items.*.than_meters' => 'nullable|numeric|min:0',
            'items.*.thans' => 'nullable|array',
            'items.*.thans.*' => 'nullable|numeric|min:0',
            'items.*.production_pcs' => 'nullable|numeric|min:0',
            'items.*.wastage_meters' => 'nullable|numeric|min:0',
            'items.*.rate_per_piece' => 'nullable|numeric|min:0',
            'items.*.avg_consumption' => 'nullable|numeric|min:0',
            'items.*.size' => 'nullable|string|max:50',
            'items.*.color' => 'nullable|string|max:50',
            'items.*.remarks' => 'nullable|string|max:255'
        ]);

        return DB::transaction(function () use ($validated, $request, $cleanedItems) {
            $jobWorker = null;
            if (!empty($validated['job_worker_id'])) {
                $jobWorker = JobWorker::find($validated['job_worker_id']);
            } elseif (!empty($validated['job_worker_name'])) {
                $jobWorker = JobWorker::where('name', $validated['job_worker_name'])->first();
            }

            $jobOrderNo = self::generateNextJobOrderNo();

            // Calculate totals across all valid items
            $totalPcs = 0;
            $totalThanMeters = 0;
            $totalThansCount = 0;
            $totalWastage = 0;
            $totalAmount = 0;
            $styleNames = [];
            $allThanDetails = [];
            $processedItems = [];

            foreach ($cleanedItems as $it) {
                $pcs = (float)($it['production_pcs'] ?? 0);
                $wastage = (float)($it['wastage_meters'] ?? 0);
                $rate = (float)($it['rate_per_piece'] ?? 0);

                // Process thans breakdown
                $thans = [];
                if (!empty($it['thans']) && is_array($it['thans'])) {
                    foreach ($it['thans'] as $t) {
                        $f = (float)$t;
                        if ($f > 0) {
                            $thans[] = round($f, 2);
                        }
                    }
                }

                $thanMeters = !empty($thans) ? array_sum($thans) : (float)($it['than_meters'] ?? 0);
                if (empty($thans) && $thanMeters > 0) {
                    $thans = [round($thanMeters, 2)];
                }

                $thanCount = count($thans);
                $lineTotal = round($pcs * $rate, 2);
                $netFabric = max(0, $thanMeters - $wastage);
                $avgCons = !empty($it['avg_consumption']) && (float)$it['avg_consumption'] > 0
                    ? (float)$it['avg_consumption']
                    : ($pcs > 0 ? ($netFabric / $pcs) : 0);

                $totalPcs += $pcs;
                $totalThanMeters += $thanMeters;
                $totalThansCount += $thanCount;
                $totalWastage += $wastage;
                $totalAmount += $lineTotal;

                if (!empty($thans)) {
                    $allThanDetails = array_merge($allThanDetails, $thans);
                }

                // Resolve Raw Item and Finished Item
                $rawItemId = !empty($it['raw_item_id']) ? (int)$it['raw_item_id'] : (!empty($it['item_id']) ? (int)$it['item_id'] : null);
                $rawItemName = !empty($it['raw_item_name']) ? $it['raw_item_name'] : ($rawItemId ? (Item::find($rawItemId)?->name ?? 'Raw Material') : ($it['item_name'] ?? 'Raw Material'));

                $finishedItemId = !empty($it['finished_item_id']) ? (int)$it['finished_item_id'] : null;
                $finishedItemName = !empty($it['finished_item_name']) ? $it['finished_item_name'] : ($finishedItemId ? (Item::find($finishedItemId)?->name ?? 'Finished Product') : ($it['item_name'] ?? 'Finished Product'));

                $primaryItemName = $finishedItemName . ($rawItemName ? " (from {$rawItemName})" : '');
                $styleNames[] = $finishedItemName ?: $rawItemName;

                $processedItems[] = [
                    'raw_item_id' => $rawItemId,
                    'raw_item_name' => $rawItemName,
                    'finished_item_id' => $finishedItemId,
                    'finished_item_name' => $finishedItemName,
                    'item_id' => $finishedItemId ?: $rawItemId,
                    'item_name' => $primaryItemName,
                    'than_meters' => $thanMeters,
                    'than_count' => $thanCount,
                    'than_details' => !empty($thans) ? json_encode($thans) : null,
                    'production_pcs' => (int)$pcs,
                    'wastage_meters' => $wastage,
                    'avg_consumption' => round($avgCons, 4),
                    'rate_per_piece' => $rate,
                    'total_amount' => $lineTotal,
                    'size' => $it['size'] ?? 'All Sizes',
                    'color' => $it['color'] ?? 'Standard',
                    'qty' => (int)$pcs,
                    'remarks' => $it['remarks'] ?? null
                ];
            }

            $avgRate = $totalPcs > 0 ? ($totalAmount / $totalPcs) : 0;
            $styleNameSummary = !empty($validated['style_name'])
                ? $validated['style_name']
                : (count($styleNames) > 0 ? implode(', ', array_unique($styleNames)) : 'Garment Lot');

            $assignmentData = [
                'job_order_no' => $jobOrderNo,
                'job_worker_id' => $jobWorker ? $jobWorker->id : null,
                'job_worker_name' => $validated['job_worker_name'],
                'process_name' => $validated['process_name'],
                'lot_number' => strtoupper(trim($validated['lot_number'])),
                'style_name' => $styleNameSummary,
                'issue_date' => $validated['issue_date'],
                'due_date' => $validated['due_date'] ?? null,
                'issued_qty' => (int)$totalPcs,
                'total_than_meters' => $totalThanMeters,
                'total_wastage_meters' => $totalWastage,
                'rate_per_piece' => round($avgRate, 2),
                'total_amount' => round($totalAmount, 2),
                'status' => 'Issued',
                'instructions' => $validated['instructions'] ?? null
            ];

            if (Schema::hasColumn('job_assignments', 'total_thans')) {
                $assignmentData['total_thans'] = $totalThansCount;
            }
            if (Schema::hasColumn('job_assignments', 'than_details')) {
                $assignmentData['than_details'] = !empty($allThanDetails) ? json_encode($allThanDetails) : null;
            }

            $jobAssignment = JobAssignment::create($assignmentData);

            // Save line items
            $hasRawId = Schema::hasColumn('job_assignment_items', 'raw_item_id');
            $hasRawName = Schema::hasColumn('job_assignment_items', 'raw_item_name');
            $hasFinId = Schema::hasColumn('job_assignment_items', 'finished_item_id');
            $hasFinName = Schema::hasColumn('job_assignment_items', 'finished_item_name');
            $hasItemThanCount = Schema::hasColumn('job_assignment_items', 'than_count');
            $hasItemThanDetails = Schema::hasColumn('job_assignment_items', 'than_details');

            foreach ($processedItems as $pItem) {
                $itemPayload = [
                    'job_assignment_id' => $jobAssignment->id,
                    'item_id' => $pItem['item_id'],
                    'item_name' => $pItem['item_name'],
                    'than_meters' => $pItem['than_meters'],
                    'production_pcs' => $pItem['production_pcs'],
                    'wastage_meters' => $pItem['wastage_meters'],
                    'avg_consumption' => $pItem['avg_consumption'],
                    'rate_per_piece' => $pItem['rate_per_piece'],
                    'total_amount' => $pItem['total_amount'],
                    'size' => $pItem['size'],
                    'color' => $pItem['color'],
                    'qty' => $pItem['qty'],
                    'remarks' => $pItem['remarks']
                ];

                if ($hasRawId) $itemPayload['raw_item_id'] = $pItem['raw_item_id'];
                if ($hasRawName) $itemPayload['raw_item_name'] = $pItem['raw_item_name'];
                if ($hasFinId) $itemPayload['finished_item_id'] = $pItem['finished_item_id'];
                if ($hasFinName) $itemPayload['finished_item_name'] = $pItem['finished_item_name'];
                if ($hasItemThanCount) $itemPayload['than_count'] = $pItem['than_count'];
                if ($hasItemThanDetails) $itemPayload['than_details'] = $pItem['than_details'];

                JobAssignmentItem::create($itemPayload);
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
        $purchasedTonsByItem = $this->getPurchasedTonsByItem();
        $assign->load(['items.rawItem', 'items.finishedItem', 'jobWorker']);

        return view('jobwork.assign.edit', compact('assign', 'jobworkers', 'items', 'purchasedTonsByItem'));
    }

    public function update(Request $request, JobAssignment $assign)
    {
        // Clean up empty rows before validation
        $rawItems = $request->input('items', []);
        $cleanedItems = [];
        if (is_array($rawItems)) {
            foreach ($rawItems as $it) {
                $hasRaw = !empty($it['raw_item_id']) || !empty($it['raw_item_name']) || !empty($it['item_id']) || !empty($it['item_name']);
                $hasQty = (!empty($it['production_pcs']) && (float)$it['production_pcs'] > 0) || (!empty($it['than_meters']) && (float)$it['than_meters'] > 0) || !empty($it['thans']);
                if ($hasRaw || $hasQty) {
                    $cleanedItems[] = $it;
                }
            }
        }
        $request->merge(['items' => $cleanedItems]);

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
            'items' => 'nullable|array',
            'items.*.raw_item_id' => 'nullable',
            'items.*.raw_item_name' => 'nullable|string|max:255',
            'items.*.finished_item_id' => 'nullable',
            'items.*.finished_item_name' => 'nullable|string|max:255',
            'items.*.item_id' => 'nullable',
            'items.*.item_name' => 'nullable|string|max:255',
            'items.*.item_code' => 'nullable|string|max:100',
            'items.*.than_meters' => 'nullable|numeric|min:0',
            'items.*.thans' => 'nullable|array',
            'items.*.thans.*' => 'nullable|numeric|min:0',
            'items.*.production_pcs' => 'nullable|numeric|min:0',
            'items.*.wastage_meters' => 'nullable|numeric|min:0',
            'items.*.rate_per_piece' => 'nullable|numeric|min:0',
            'items.*.avg_consumption' => 'nullable|numeric|min:0',
            'items.*.size' => 'nullable|string|max:50',
            'items.*.color' => 'nullable|string|max:50',
            'items.*.remarks' => 'nullable|string|max:255'
        ]);

        return DB::transaction(function () use ($validated, $request, $assign, $cleanedItems) {
            $jobWorker = null;
            if (!empty($validated['job_worker_id'])) {
                $jobWorker = JobWorker::find($validated['job_worker_id']);
            } elseif (!empty($validated['job_worker_name'])) {
                $jobWorker = JobWorker::where('name', $validated['job_worker_name'])->first();
            }

            // Calculate totals
            $totalPcs = 0;
            $totalThanMeters = 0;
            $totalThansCount = 0;
            $totalWastage = 0;
            $totalAmount = 0;
            $styleNames = [];
            $allThanDetails = [];
            $processedItems = [];

            foreach ($cleanedItems as $it) {
                $pcs = (float)($it['production_pcs'] ?? 0);
                $wastage = (float)($it['wastage_meters'] ?? 0);
                $rate = (float)($it['rate_per_piece'] ?? 0);

                // Process thans breakdown
                $thans = [];
                if (!empty($it['thans']) && is_array($it['thans'])) {
                    foreach ($it['thans'] as $t) {
                        $f = (float)$t;
                        if ($f > 0) {
                            $thans[] = round($f, 2);
                        }
                    }
                }

                $thanMeters = !empty($thans) ? array_sum($thans) : (float)($it['than_meters'] ?? 0);
                if (empty($thans) && $thanMeters > 0) {
                    $thans = [round($thanMeters, 2)];
                }

                $thanCount = count($thans);
                $lineTotal = round($pcs * $rate, 2);
                $netFabric = max(0, $thanMeters - $wastage);
                $avgCons = !empty($it['avg_consumption']) && (float)$it['avg_consumption'] > 0
                    ? (float)$it['avg_consumption']
                    : ($pcs > 0 ? ($netFabric / $pcs) : 0);

                $totalPcs += $pcs;
                $totalThanMeters += $thanMeters;
                $totalThansCount += $thanCount;
                $totalWastage += $wastage;
                $totalAmount += $lineTotal;

                if (!empty($thans)) {
                    $allThanDetails = array_merge($allThanDetails, $thans);
                }

                // Resolve Raw Item and Finished Item
                $rawItemId = !empty($it['raw_item_id']) ? (int)$it['raw_item_id'] : (!empty($it['item_id']) ? (int)$it['item_id'] : null);
                $rawItemName = !empty($it['raw_item_name']) ? $it['raw_item_name'] : ($rawItemId ? (Item::find($rawItemId)?->name ?? 'Raw Material') : ($it['item_name'] ?? 'Raw Material'));

                $finishedItemId = !empty($it['finished_item_id']) ? (int)$it['finished_item_id'] : null;
                $finishedItemName = !empty($it['finished_item_name']) ? $it['finished_item_name'] : ($finishedItemId ? (Item::find($finishedItemId)?->name ?? 'Finished Product') : ($it['item_name'] ?? 'Finished Product'));

                $primaryItemName = $finishedItemName . ($rawItemName ? " (from {$rawItemName})" : '');
                $styleNames[] = $finishedItemName ?: $rawItemName;

                $processedItems[] = [
                    'raw_item_id' => $rawItemId,
                    'raw_item_name' => $rawItemName,
                    'finished_item_id' => $finishedItemId,
                    'finished_item_name' => $finishedItemName,
                    'item_id' => $finishedItemId ?: $rawItemId,
                    'item_name' => $primaryItemName,
                    'than_meters' => $thanMeters,
                    'than_count' => $thanCount,
                    'than_details' => !empty($thans) ? json_encode($thans) : null,
                    'production_pcs' => (int)$pcs,
                    'wastage_meters' => $wastage,
                    'avg_consumption' => round($avgCons, 4),
                    'rate_per_piece' => $rate,
                    'total_amount' => $lineTotal,
                    'size' => $it['size'] ?? 'All Sizes',
                    'color' => $it['color'] ?? 'Standard',
                    'qty' => (int)$pcs,
                    'remarks' => $it['remarks'] ?? null
                ];
            }

            $avgRate = $totalPcs > 0 ? ($totalAmount / $totalPcs) : 0;
            $styleNameSummary = !empty($validated['style_name'])
                ? $validated['style_name']
                : (count($styleNames) > 0 ? implode(', ', array_unique($styleNames)) : $assign->style_name);

            $assignmentData = [
                'job_worker_id' => $jobWorker ? $jobWorker->id : $assign->job_worker_id,
                'job_worker_name' => $validated['job_worker_name'],
                'process_name' => $validated['process_name'],
                'lot_number' => strtoupper(trim($validated['lot_number'])),
                'style_name' => $styleNameSummary,
                'issue_date' => $validated['issue_date'],
                'due_date' => $validated['due_date'] ?? null,
                'issued_qty' => (int)$totalPcs,
                'total_than_meters' => $totalThanMeters,
                'total_wastage_meters' => $totalWastage,
                'rate_per_piece' => round($avgRate, 2),
                'total_amount' => round($totalAmount, 2),
                'status' => $validated['status'] ?? $assign->status,
                'instructions' => $validated['instructions'] ?? null
            ];

            if (Schema::hasColumn('job_assignments', 'total_thans')) {
                $assignmentData['total_thans'] = $totalThansCount;
            }
            if (Schema::hasColumn('job_assignments', 'than_details')) {
                $assignmentData['than_details'] = !empty($allThanDetails) ? json_encode($allThanDetails) : null;
            }

            $assign->update($assignmentData);

            // Sync line items
            $assign->items()->delete();

            $hasRawId = Schema::hasColumn('job_assignment_items', 'raw_item_id');
            $hasRawName = Schema::hasColumn('job_assignment_items', 'raw_item_name');
            $hasFinId = Schema::hasColumn('job_assignment_items', 'finished_item_id');
            $hasFinName = Schema::hasColumn('job_assignment_items', 'finished_item_name');
            $hasItemThanCount = Schema::hasColumn('job_assignment_items', 'than_count');
            $hasItemThanDetails = Schema::hasColumn('job_assignment_items', 'than_details');

            foreach ($processedItems as $pItem) {
                $itemPayload = [
                    'job_assignment_id' => $assign->id,
                    'item_id' => $pItem['item_id'],
                    'item_name' => $pItem['item_name'],
                    'than_meters' => $pItem['than_meters'],
                    'production_pcs' => $pItem['production_pcs'],
                    'wastage_meters' => $pItem['wastage_meters'],
                    'avg_consumption' => $pItem['avg_consumption'],
                    'rate_per_piece' => $pItem['rate_per_piece'],
                    'total_amount' => $pItem['total_amount'],
                    'size' => $pItem['size'],
                    'color' => $pItem['color'],
                    'qty' => $pItem['qty'],
                    'remarks' => $pItem['remarks']
                ];

                if ($hasRawId) $itemPayload['raw_item_id'] = $pItem['raw_item_id'];
                if ($hasRawName) $itemPayload['raw_item_name'] = $pItem['raw_item_name'];
                if ($hasFinId) $itemPayload['finished_item_id'] = $pItem['finished_item_id'];
                if ($hasFinName) $itemPayload['finished_item_name'] = $pItem['finished_item_name'];
                if ($hasItemThanCount) $itemPayload['than_count'] = $pItem['than_count'];
                if ($hasItemThanDetails) $itemPayload['than_details'] = $pItem['than_details'];

                JobAssignmentItem::create($itemPayload);
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
        return response()->json($assign->load(['items.rawItem', 'items.finishedItem', 'jobWorker']));
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
