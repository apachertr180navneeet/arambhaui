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
     * Get available purchase tons/thans mapped by raw item id & name,
     * calculating remaining meters available after deducting meters assigned to prior Job Work Orders.
     */
    protected function getPurchasedTonsByItem($excludeAssignmentId = null)
    {
        $purchasedTons = [];
        $purchaseOrders = PurchaseOrder::with('items')->latest()->get();

        // 1. Gather all assigned purchase than meters from active Job Assignments
        $usedThanMeters = [];
        $assignments = JobAssignment::with('items')
            ->when($excludeAssignmentId, function ($q) use ($excludeAssignmentId) {
                $q->where('id', '!=', $excludeAssignmentId);
            })
            ->get();

        foreach ($assignments as $ja) {
            $hasItemThans = false;
            if ($ja->items && $ja->items->count() > 0) {
                foreach ($ja->items as $aItm) {
                    $thans = $aItm->than_list;
                    if (!empty($thans) && is_array($thans)) {
                        $hasItemThans = true;
                        foreach ($thans as $t) {
                            if (is_array($t)) {
                                $usedMtr = (float)($t['meter'] ?? $t['meters'] ?? 0);
                                if ($usedMtr <= 0) continue;

                                $canonicalKey = null;
                                if (!empty($t['unique_id'])) {
                                    $canonicalKey = $t['unique_id'];
                                } elseif (!empty($t['po_id']) && !empty($t['po_item_id']) && !empty($t['purchase_than_no'])) {
                                    $canonicalKey = "po_{$t['po_id']}_item_{$t['po_item_id']}_than_" . ($t['purchase_than_no'] - 1);
                                } elseif (!empty($t['po_id']) && !empty($t['purchase_than_no'])) {
                                    $canonicalKey = "po_{$t['po_id']}_than_{$t['purchase_than_no']}";
                                } elseif (!empty($t['challan_no']) && !empty($t['purchase_than_no'])) {
                                    $canonicalKey = "challan_{$t['challan_no']}_than_{$t['purchase_than_no']}";
                                }

                                if ($canonicalKey) {
                                    $usedThanMeters[$canonicalKey] = ($usedThanMeters[$canonicalKey] ?? 0) + $usedMtr;
                                }
                            }
                        }
                    }
                }
            }

            // Fallback for legacy assignments if line items didn't store than_details
            if (!$hasItemThans) {
                $thans = $ja->than_list;
                if (!empty($thans) && is_array($thans)) {
                    foreach ($thans as $t) {
                        if (is_array($t)) {
                            $usedMtr = (float)($t['meter'] ?? $t['meters'] ?? 0);
                            if ($usedMtr <= 0) continue;

                            $canonicalKey = null;
                            if (!empty($t['unique_id'])) {
                                $canonicalKey = $t['unique_id'];
                            } elseif (!empty($t['po_id']) && !empty($t['po_item_id']) && !empty($t['purchase_than_no'])) {
                                $canonicalKey = "po_{$t['po_id']}_item_{$t['po_item_id']}_than_" . ($t['purchase_than_no'] - 1);
                            } elseif (!empty($t['po_id']) && !empty($t['purchase_than_no'])) {
                                $canonicalKey = "po_{$t['po_id']}_than_{$t['purchase_than_no']}";
                            } elseif (!empty($t['challan_no']) && !empty($t['purchase_than_no'])) {
                                $canonicalKey = "challan_{$t['challan_no']}_than_{$t['purchase_than_no']}";
                            }

                            if ($canonicalKey) {
                                $usedThanMeters[$canonicalKey] = ($usedThanMeters[$canonicalKey] ?? 0) + $usedMtr;
                            }
                        }
                    }
                }
            }
        }

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
                        $thanNo = $tIdx + 1;
                        $origMtr = (float)$tMtr;
                        if ($origMtr <= 0) continue;

                        $uniqueKey = "po_{$po->id}_item_{$poItem->id}_than_{$tIdx}";
                        $altKey1 = "po_{$po->id}_than_{$thanNo}";
                        $altKey2 = "challan_{$challanNo}_than_{$thanNo}";

                        // Compute used meters from prior assignments
                        $usedMtr = ($usedThanMeters[$uniqueKey] ?? 0)
                                 + ($usedThanMeters[$altKey1] ?? 0)
                                 + ($usedThanMeters[$altKey2] ?? 0);

                        $remainMtr = round(max(0, $origMtr - $usedMtr), 2);

                        // If fully consumed, do not show in available stock chips
                        if ($remainMtr <= 0.001) {
                            continue;
                        }

                        $isPartiallyUsed = ($usedMtr > 0.001 && $remainMtr < $origMtr);

                        $label = $isPartiallyUsed
                            ? "Challan #{$challanNo} - Than #{$thanNo} (" . number_format($remainMtr, 2) . " " . ($poItem->unit ?: 'Mtr') . " rem / " . number_format($origMtr, 2) . ")"
                            : "Challan #{$challanNo} - Than #{$thanNo} (" . number_format($remainMtr, 2) . " " . ($poItem->unit ?: 'Mtr') . ")";

                        $tonObj = [
                            'unique_id' => $uniqueKey,
                            'po_id' => $po->id,
                            'po_item_id' => $poItem->id,
                            'po_number' => $po->po_number,
                            'challan_no' => $challanNo,
                            'date' => $po->po_date,
                            'than_no' => $thanNo,
                            'meter' => (float)$remainMtr,
                            'original_meter' => (float)$origMtr,
                            'used_meter' => round($usedMtr, 2),
                            'unit' => $poItem->unit ?: 'Mtr',
                            'label' => $label
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
            'items.*.thans.*' => 'nullable',
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
                $rate = (float)($it['rate_per_piece'] ?? 0);
                $cons = !empty($it['avg_consumption']) && (float)$it['avg_consumption'] > 0 ? (float)$it['avg_consumption'] : 1.5;

                // Process thans breakdown independently per Than
                $thans = [];
                $itemThanMeters = 0;
                $itemWastage = 0;
                $itemPcs = 0;

                if (!empty($it['thans']) && is_array($it['thans'])) {
                    foreach ($it['thans'] as $t) {
                        if (is_array($t)) {
                            $m = (float)($t['meter'] ?? $t['meters'] ?? 0);
                            $w = isset($t['wastage']) ? (float)$t['wastage'] : (isset($t['wastage_meters']) ? (float)$t['wastage_meters'] : null);
                            if ($w !== null && $w > 0) {
                                $avail = max(0, $m - $w);
                                $p = isset($t['pieces']) ? (int)$t['pieces'] : ($cons > 0 ? (int)floor($avail / $cons) : 0);
                                $u = isset($t['usable']) ? (float)$t['usable'] : ($p * $cons);
                                $w = max(0, $m - $u);
                            } else {
                                $p = isset($t['pieces']) ? (int)$t['pieces'] : ($cons > 0 ? (int)floor($m / $cons) : 0);
                                $u = isset($t['usable']) ? (float)$t['usable'] : ($p * $cons);
                                $w = max(0, $m - $u);
                            }
                            if ($m > 0 || $w > 0 || $p > 0) {
                                $thans[] = [
                                    'than_no' => count($thans) + 1,
                                    'meter' => round($m, 2),
                                    'wastage' => round($w, 2),
                                    'usable' => round($u, 2),
                                    'pieces' => $p,
                                    'unique_id' => $t['unique_id'] ?? null,
                                    'po_id' => $t['po_id'] ?? null,
                                    'po_item_id' => $t['po_item_id'] ?? null,
                                    'po_number' => $t['po_number'] ?? null,
                                    'challan_no' => $t['challan_no'] ?? null,
                                    'purchase_than_no' => $t['purchase_than_no'] ?? ($t['than_no'] ?? null),
                                ];
                                $itemThanMeters += $m;
                                $itemWastage += $w;
                                $itemPcs += $p;
                            }
                        } elseif (is_numeric($t)) {
                            $m = (float)$t;
                            if ($m > 0) {
                                $p = $cons > 0 ? (int)floor($m / $cons) : 0;
                                $u = $p * $cons;
                                $w = max(0, $m - $u);
                                $thans[] = [
                                    'than_no' => count($thans) + 1,
                                    'meter' => round($m, 2),
                                    'wastage' => round($w, 2),
                                    'usable' => round($u, 2),
                                    'pieces' => $p
                                ];
                                $itemThanMeters += $m;
                                $itemWastage += $w;
                                $itemPcs += $p;
                            }
                        }
                    }
                }

                if (empty($thans)) {
                    $itemThanMeters = (float)($it['than_meters'] ?? 0);
                    $itemWastage = (float)($it['wastage_meters'] ?? 0);
                    $itemPcs = (int)($it['production_pcs'] ?? 0);
                    if ($itemThanMeters > 0 || $itemPcs > 0) {
                        $u = max(0, $itemThanMeters - $itemWastage);
                        $thans = [[
                            'than_no' => 1,
                            'meter' => round($itemThanMeters, 2),
                            'wastage' => round($itemWastage, 2),
                            'usable' => round($u, 2),
                            'pieces' => $itemPcs > 0 ? $itemPcs : ($cons > 0 ? (int)floor($u / $cons) : 0)
                        ]];
                    }
                }

                $thanCount = count($thans);
                $pcs = !empty($thans) ? $itemPcs : (int)($it['production_pcs'] ?? 0);
                $wastage = !empty($thans) ? $itemWastage : (float)($it['wastage_meters'] ?? 0);
                $thanMeters = !empty($thans) ? $itemThanMeters : (float)($it['than_meters'] ?? 0);
                $lineTotal = round($pcs * $rate, 2);

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
                    'avg_consumption' => round($cons, 4),
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

                // Reduce Raw Item current stock in items table
                $rItemId = $pItem['raw_item_id'] ?: $pItem['item_id'];
                $rawItem = null;
                if ($rItemId) {
                    $rawItem = Item::find($rItemId);
                }
                if (!$rawItem && !empty($pItem['raw_item_name'])) {
                    $rawItem = Item::where('name', $pItem['raw_item_name'])->first();
                }
                if ($rawItem) {
                    $deductMtr = (float)($pItem['than_meters'] > 0 ? $pItem['than_meters'] : $pItem['qty']);
                    $rawItem->current_stock = max(0, (float)$rawItem->current_stock - $deductMtr);
                    $rawItem->save();
                }
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
        $purchasedTonsByItem = $this->getPurchasedTonsByItem($assign->id);
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
            'items.*.thans.*' => 'nullable',
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
                $rate = (float)($it['rate_per_piece'] ?? 0);
                $cons = !empty($it['avg_consumption']) && (float)$it['avg_consumption'] > 0 ? (float)$it['avg_consumption'] : 1.5;

                // Process thans breakdown independently per Than
                $thans = [];
                $itemThanMeters = 0;
                $itemWastage = 0;
                $itemPcs = 0;

                if (!empty($it['thans']) && is_array($it['thans'])) {
                    foreach ($it['thans'] as $t) {
                        if (is_array($t)) {
                            $m = (float)($t['meter'] ?? $t['meters'] ?? 0);
                            $w = isset($t['wastage']) ? (float)$t['wastage'] : (isset($t['wastage_meters']) ? (float)$t['wastage_meters'] : null);
                            if ($w !== null && $w > 0) {
                                $avail = max(0, $m - $w);
                                $p = isset($t['pieces']) ? (int)$t['pieces'] : ($cons > 0 ? (int)floor($avail / $cons) : 0);
                                $u = isset($t['usable']) ? (float)$t['usable'] : ($p * $cons);
                                $w = max(0, $m - $u);
                            } else {
                                $p = isset($t['pieces']) ? (int)$t['pieces'] : ($cons > 0 ? (int)floor($m / $cons) : 0);
                                $u = isset($t['usable']) ? (float)$t['usable'] : ($p * $cons);
                                $w = max(0, $m - $u);
                            }
                            if ($m > 0 || $w > 0 || $p > 0) {
                                $thans[] = [
                                    'than_no' => count($thans) + 1,
                                    'meter' => round($m, 2),
                                    'wastage' => round($w, 2),
                                    'usable' => round($u, 2),
                                    'pieces' => $p,
                                    'unique_id' => $t['unique_id'] ?? null,
                                    'po_id' => $t['po_id'] ?? null,
                                    'po_item_id' => $t['po_item_id'] ?? null,
                                    'po_number' => $t['po_number'] ?? null,
                                    'challan_no' => $t['challan_no'] ?? null,
                                    'purchase_than_no' => $t['purchase_than_no'] ?? ($t['than_no'] ?? null),
                                ];
                                $itemThanMeters += $m;
                                $itemWastage += $w;
                                $itemPcs += $p;
                            }
                        } elseif (is_numeric($t)) {
                            $m = (float)$t;
                            if ($m > 0) {
                                $p = $cons > 0 ? (int)floor($m / $cons) : 0;
                                $u = $p * $cons;
                                $w = max(0, $m - $u);
                                $thans[] = [
                                    'than_no' => count($thans) + 1,
                                    'meter' => round($m, 2),
                                    'wastage' => round($w, 2),
                                    'usable' => round($u, 2),
                                    'pieces' => $p
                                ];
                                $itemThanMeters += $m;
                                $itemWastage += $w;
                                $itemPcs += $p;
                            }
                        }
                    }
                }

                if (empty($thans)) {
                    $itemThanMeters = (float)($it['than_meters'] ?? 0);
                    $itemWastage = (float)($it['wastage_meters'] ?? 0);
                    $itemPcs = (int)($it['production_pcs'] ?? 0);
                    if ($itemThanMeters > 0 || $itemPcs > 0) {
                        $u = max(0, $itemThanMeters - $itemWastage);
                        $thans = [[
                            'than_no' => 1,
                            'meter' => round($itemThanMeters, 2),
                            'wastage' => round($itemWastage, 2),
                            'usable' => round($u, 2),
                            'pieces' => $itemPcs > 0 ? $itemPcs : ($cons > 0 ? (int)floor($u / $cons) : 0)
                        ]];
                    }
                }

                $thanCount = count($thans);
                $pcs = !empty($thans) ? $itemPcs : (int)($it['production_pcs'] ?? 0);
                $wastage = !empty($thans) ? $itemWastage : (float)($it['wastage_meters'] ?? 0);
                $thanMeters = !empty($thans) ? $itemThanMeters : (float)($it['than_meters'] ?? 0);
                $lineTotal = round($pcs * $rate, 2);

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
                    'avg_consumption' => round($cons, 4),
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

            // Restore previous raw item stock
            foreach ($assign->items as $prevItem) {
                $prevRId = $prevItem->raw_item_id ?: $prevItem->item_id;
                $prevRawItem = null;
                if ($prevRId) {
                    $prevRawItem = Item::find($prevRId);
                }
                if (!$prevRawItem && !empty($prevItem->raw_item_name)) {
                    $prevRawItem = Item::where('name', $prevItem->raw_item_name)->first();
                }
                if ($prevRawItem) {
                    $restoreMtr = (float)($prevItem->than_meters > 0 ? $prevItem->than_meters : $prevItem->qty);
                    $prevRawItem->current_stock = (float)$prevRawItem->current_stock + $restoreMtr;
                    $prevRawItem->save();
                }
            }

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

                // Reduce Raw Item current stock
                $rItemId = $pItem['raw_item_id'] ?: $pItem['item_id'];
                $rawItem = null;
                if ($rItemId) {
                    $rawItem = Item::find($rItemId);
                }
                if (!$rawItem && !empty($pItem['raw_item_name'])) {
                    $rawItem = Item::where('name', $pItem['raw_item_name'])->first();
                }
                if ($rawItem) {
                    $deductMtr = (float)($pItem['than_meters'] > 0 ? $pItem['than_meters'] : $pItem['qty']);
                    $rawItem->current_stock = max(0, (float)$rawItem->current_stock - $deductMtr);
                    $rawItem->save();
                }
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

        // Restore raw item stock
        foreach ($assign->items as $prevItem) {
            $prevRId = $prevItem->raw_item_id ?: $prevItem->item_id;
            $prevRawItem = null;
            if ($prevRId) {
                $prevRawItem = Item::find($prevRId);
            }
            if (!$prevRawItem && !empty($prevItem->raw_item_name)) {
                $prevRawItem = Item::where('name', $prevItem->raw_item_name)->first();
            }
            if ($prevRawItem) {
                $restoreMtr = (float)($prevItem->than_meters > 0 ? $prevItem->than_meters : $prevItem->qty);
                $prevRawItem->current_stock = (float)$prevRawItem->current_stock + $restoreMtr;
                $prevRawItem->save();
            }
        }

        $assign->items()->delete();
        $assign->delete();

        if (request()->wantsJson() || request()->ajax()) {
            return response()->json(['success' => true, 'message' => "Job order {$no} removed."]);
        }

        return redirect()->route('jobwork.assign.index')->with('success', "Job order {$no} removed.");
    }
}
