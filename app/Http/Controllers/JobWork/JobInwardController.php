<?php

namespace App\Http\Controllers\JobWork;

use App\Http\Controllers\Controller;
use App\Models\JobAssignment;
use App\Models\JobInward;
use App\Models\JobWorker;
use App\Models\Item;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

class JobInwardController extends Controller
{
    public function index(Request $request)
    {
        $query = JobInward::with(['jobAssignment', 'jobWorker'])->latest();

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

    /**
     * Compute item-wise and Than-wise assigned, previously received, and pending quantities for a Job Assignment.
     */
    public function formatAssignmentTracking(JobAssignment $ja, $excludeInwardId = null): array
    {
        $query = JobInward::where('job_assignment_id', $ja->id);
        if ($excludeInwardId) {
            $query->where('id', '!=', $excludeInwardId);
        }
        $previousInwards = $query->get();

        $prevReceivedByJaiId = [];
        $prevReceivedByItemId = [];
        $prevReceivedByName = [];
        $prevDefectByJaiId = [];
        $prevDefectByItemId = [];
        $prevDefectByName = [];
        $prevReceivedByThanKey = [];
        $prevWastageByThanKey = [];
        $prevWastageByJaiId = [];

        foreach ($previousInwards as $inw) {
            $inwItems = $inw->items_list;
            if (!empty($inwItems)) {
                foreach ($inwItems as $inwItm) {
                    $qty = (int)($inwItm['received_qty'] ?? 0);
                    $def = (int)($inwItm['defect_qty'] ?? 0);
                    $wMtr = (float)($inwItm['wastage_returned_meters'] ?? 0);
                    
                    if (!empty($inwItm['job_assignment_item_id'])) {
                        $k = (string)$inwItm['job_assignment_item_id'];
                        $prevReceivedByJaiId[$k] = ($prevReceivedByJaiId[$k] ?? 0) + $qty;
                        $prevDefectByJaiId[$k] = ($prevDefectByJaiId[$k] ?? 0) + $def;
                        $prevWastageByJaiId[$k] = ($prevWastageByJaiId[$k] ?? 0) + $wMtr;
                    }
                    if (!empty($inwItm['item_id'])) {
                        $k = (string)$inwItm['item_id'];
                        $prevReceivedByItemId[$k] = ($prevReceivedByItemId[$k] ?? 0) + $qty;
                        $prevDefectByItemId[$k] = ($prevDefectByItemId[$k] ?? 0) + $def;
                    }
                    if (!empty($inwItm['item_name'])) {
                        $k = strtolower(trim($inwItm['item_name']));
                        $prevReceivedByName[$k] = ($prevReceivedByName[$k] ?? 0) + $qty;
                        $prevDefectByName[$k] = ($prevDefectByName[$k] ?? 0) + $def;
                    }

                    // Track than-wise received quantities and returned wastage from previous inwards
                    if (!empty($inwItm['assigned_thans']) && is_array($inwItm['assigned_thans'])) {
                        foreach ($inwItm['assigned_thans'] as $t) {
                            $thRecPcs = (int)($t['received_pcs'] ?? ($t['received_qty'] ?? 0));
                            $thWastage = (float)($t['wastage_returned'] ?? ($t['wastage_returned_meters'] ?? 0));

                            if (!empty($t['unique_id'])) {
                                $prevReceivedByThanKey[(string)$t['unique_id']] = ($prevReceivedByThanKey[(string)$t['unique_id']] ?? 0) + $thRecPcs;
                                $prevWastageByThanKey[(string)$t['unique_id']] = ($prevWastageByThanKey[(string)$t['unique_id']] ?? 0) + $thWastage;
                            }
                            if (!empty($t['than_key'])) {
                                $prevReceivedByThanKey[(string)$t['than_key']] = ($prevReceivedByThanKey[(string)$t['than_key']] ?? 0) + $thRecPcs;
                                $prevWastageByThanKey[(string)$t['than_key']] = ($prevWastageByThanKey[(string)$t['than_key']] ?? 0) + $thWastage;
                            }
                            if (!empty($inwItm['job_assignment_item_id']) && isset($t['than_no'])) {
                                $tk = $inwItm['job_assignment_item_id'] . '_than_' . $t['than_no'];
                                $prevReceivedByThanKey[$tk] = ($prevReceivedByThanKey[$tk] ?? 0) + $thRecPcs;
                                $prevWastageByThanKey[$tk] = ($prevWastageByThanKey[$tk] ?? 0) + $thWastage;
                            }
                        }
                    }
                }
            } else {
                $qty = (int)$inw->received_qty;
                $def = (int)$inw->defect_qty;
                $wMtr = (float)($inw->wastage_returned_meters ?? 0);
                if (!empty($inw->style_name)) {
                    $k = strtolower(trim($inw->style_name));
                    $prevReceivedByName[$k] = ($prevReceivedByName[$k] ?? 0) + $qty;
                    $prevDefectByName[$k] = ($prevDefectByName[$k] ?? 0) + $def;
                }
            }
        }

        $items = [];
        $totalAssigned = 0;
        $totalPrevReceived = 0;
        $totalRemaining = 0;

        if ($ja->items && $ja->items->count() > 0) {
            foreach ($ja->items as $aItm) {
                $nameKey = strtolower(trim($aItm->item_name ?: ($aItm->finished_item_name ?: ($aItm->raw_item_name ?: ''))));
                $prevQty = $prevReceivedByJaiId[(string)$aItm->id] ?? ($prevReceivedByItemId[(string)$aItm->finished_item_id] ?? ($prevReceivedByItemId[(string)$aItm->item_id] ?? ($prevReceivedByName[$nameKey] ?? 0)));
                $prevDef = $prevDefectByJaiId[(string)$aItm->id] ?? ($prevDefectByItemId[(string)$aItm->finished_item_id] ?? ($prevDefectByItemId[(string)$aItm->item_id] ?? ($prevDefectByName[$nameKey] ?? 0)));

                // Build Than-wise breakdown
                $rawThans = $aItm->than_list;
                if (empty($rawThans) && !empty($ja->than_details)) {
                    $rawThans = $ja->than_list;
                }

                $assignedThans = [];
                $cons = (float)($aItm->avg_consumption > 0 ? $aItm->avg_consumption : 1.5);
                $allocatedPrevPool = $prevQty;

                if (!empty($rawThans) && is_array($rawThans)) {
                    foreach ($rawThans as $tIdx => $t) {
                        if (is_array($t)) {
                            $thanNo = $t['than_no'] ?? ($tIdx + 1);
                            $meter = (float)($t['meter'] ?? $t['meters'] ?? ($t['than_meters'] ?? 0));
                            $wastage = (float)($t['wastage'] ?? 0);
                            $usable = (float)($t['usable'] ?? max(0, $meter - $wastage));
                            $uniqueId = $t['unique_id'] ?? null;
                            $challanNo = $t['challan_no'] ?? null;
                            $poNumber = $t['po_number'] ?? null;
                            $purchaseThanNo = $t['purchase_than_no'] ?? null;

                            // Expected pieces calculation
                            $pcs = isset($t['pieces']) && (int)$t['pieces'] > 0 ? (int)$t['pieces'] : 0;
                            if ($pcs <= 0) {
                                if ($cons > 0 && $usable > 0) {
                                    $pcs = (int)floor($usable / $cons);
                                } elseif ($aItm->production_pcs > 0 && count($rawThans) === 1) {
                                    $pcs = (int)$aItm->production_pcs;
                                } elseif ($meter > 0) {
                                    $pcs = (int)ceil($meter);
                                }
                            }
                            if ($pcs <= 0) {
                                $pcs = (int)$aItm->production_pcs > 0 ? (int)$aItm->production_pcs : 1;
                            }

                            $thanKey = $uniqueId ?: ($aItm->id . '_than_' . $thanNo);
                            
                            // Determine previously received pieces and returned wastage for this Than
                            if (isset($prevReceivedByThanKey[$thanKey])) {
                                $thPrevRec = (int)$prevReceivedByThanKey[$thanKey];
                            } elseif ($uniqueId && isset($prevReceivedByThanKey[$uniqueId])) {
                                $thPrevRec = (int)$prevReceivedByThanKey[$uniqueId];
                            } else {
                                $thPrevRec = min($pcs, max(0, $allocatedPrevPool));
                                $allocatedPrevPool = max(0, $allocatedPrevPool - $thPrevRec);
                            }

                            $thPrevWastage = isset($prevWastageByThanKey[$thanKey]) ? (float)$prevWastageByThanKey[$thanKey] : ($uniqueId && isset($prevWastageByThanKey[$uniqueId]) ? (float)$prevWastageByThanKey[$uniqueId] : 0);
                            $thRemWastage = max(0, $wastage - $thPrevWastage);

                            $thRemPcs = max(0, $pcs - $thPrevRec);
                            $thStatus = 'Pending';
                            if ($thRemPcs <= 0 && $thPrevRec > 0) {
                                $thStatus = 'Completed';
                            } elseif ($thPrevRec > 0) {
                                $thStatus = 'Partially Received';
                            }

                            $assignedThans[] = [
                                'than_key' => $thanKey,
                                'than_no' => $thanNo,
                                'unique_id' => $uniqueId,
                                'challan_no' => $challanNo,
                                'po_number' => $poNumber,
                                'purchase_than_no' => $purchaseThanNo,
                                'meter' => round($meter, 2),
                                'usable' => round($usable, 2),
                                'wastage' => round($wastage, 2),
                                'previously_returned_wastage' => round($thPrevWastage, 2),
                                'remaining_wastage' => round($thRemWastage, 2),
                                'assigned_pcs' => $pcs,
                                'previously_received_pcs' => $thPrevRec,
                                'remaining_pcs' => $thRemPcs,
                                'status' => $thStatus
                            ];
                        } elseif (is_numeric($t)) {
                            $meter = (float)$t;
                            $pcs = $cons > 0 ? (int)floor($meter / $cons) : (int)ceil($meter);
                            $thanKey = $aItm->id . '_than_' . ($tIdx + 1);
                            $thPrevRec = min($pcs, max(0, $allocatedPrevPool));
                            $allocatedPrevPool = max(0, $allocatedPrevPool - $thPrevRec);
                            $thRemPcs = max(0, $pcs - $thPrevRec);

                            $assignedThans[] = [
                                'than_key' => $thanKey,
                                'than_no' => $tIdx + 1,
                                'unique_id' => null,
                                'challan_no' => null,
                                'po_number' => null,
                                'purchase_than_no' => null,
                                'meter' => round($meter, 2),
                                'usable' => round($meter, 2),
                                'wastage' => 0,
                                'previously_returned_wastage' => 0,
                                'remaining_wastage' => 0,
                                'assigned_pcs' => $pcs,
                                'previously_received_pcs' => $thPrevRec,
                                'remaining_pcs' => $thRemPcs,
                                'status' => $thRemPcs <= 0 && $thPrevRec > 0 ? 'Completed' : ($thPrevRec > 0 ? 'Partially Received' : 'Pending')
                            ];
                        }
                    }
                }

                // If no thans defined, create standard single than
                if (empty($assignedThans)) {
                    $basePcs = $aItm->production_pcs > 0 ? (int)$aItm->production_pcs : ($aItm->qty > 0 ? (int)$aItm->qty : (int)$ja->issued_qty);
                    if ($basePcs <= 0) $basePcs = 1;
                    $thRemPcs = max(0, $basePcs - $prevQty);
                    $wMtr = (float)$aItm->wastage_meters;

                    $assignedThans[] = [
                        'than_key' => $aItm->id . '_than_1',
                        'than_no' => 1,
                        'unique_id' => null,
                        'challan_no' => null,
                        'po_number' => null,
                        'purchase_than_no' => null,
                        'meter' => (float)$aItm->than_meters,
                        'usable' => (float)$aItm->than_meters,
                        'wastage' => $wMtr,
                        'previously_returned_wastage' => 0,
                        'remaining_wastage' => $wMtr,
                        'assigned_pcs' => $basePcs,
                        'previously_received_pcs' => $prevQty,
                        'remaining_pcs' => $thRemPcs,
                        'status' => $thRemPcs <= 0 && $prevQty > 0 ? 'Completed' : ($prevQty > 0 ? 'Partially Received' : 'Pending')
                    ];
                }

                $assignedQty = array_sum(array_column($assignedThans, 'assigned_pcs'));
                if ($assignedQty <= 0) {
                    $assignedQty = $aItm->production_pcs > 0 ? (int)$aItm->production_pcs : (int)$ja->issued_qty;
                }
                $prevQty = array_sum(array_column($assignedThans, 'previously_received_pcs'));
                $remQty = max(0, $assignedQty - $prevQty);

                $status = 'Pending';
                if ($remQty <= 0 && $prevQty > 0) {
                    $status = 'Completed';
                } elseif ($prevQty > 0) {
                    $status = 'Partially Received';
                }

                $totalAssigned += $assignedQty;
                $totalPrevReceived += $prevQty;
                $totalRemaining += $remQty;

                $totalThanWastage = array_sum(array_column($assignedThans, 'wastage'));

                $items[] = [
                    'job_assignment_item_id' => $aItm->id,
                    'raw_item_id' => $aItm->raw_item_id,
                    'raw_item_name' => $aItm->raw_item_name,
                    'finished_item_id' => $aItm->finished_item_id,
                    'finished_item_name' => $aItm->finished_item_name,
                    'item_id' => $aItm->finished_item_id ?: ($aItm->item_id ?: $aItm->raw_item_id),
                    'item_name' => $aItm->item_name ?: ($aItm->finished_item_name ?: ($aItm->raw_item_name ?: 'Garment Product')),
                    'item_code' => $aItm->finishedItem?->code ?? ($aItm->item?->code ?? ''),
                    'unit' => 'Pcs',
                    'fabric_unit' => 'Meter',
                    'rate' => (float)($aItm->rate_per_piece ?: ($ja->rate_per_piece ?: 0)),
                    'assigned_qty' => $assignedQty,
                    'previously_received_qty' => $prevQty,
                    'previously_defect_qty' => $prevDef,
                    'remaining_qty' => $remQty,
                    'status' => $status,
                    'than_meters' => (float)$aItm->than_meters,
                    'than_count' => count($assignedThans),
                    'wastage_meters' => $totalThanWastage > 0 ? $totalThanWastage : (float)$aItm->wastage_meters,
                    'assigned_thans' => $assignedThans
                ];
            }
        } else {
            $assignedQty = (int)$ja->issued_qty;
            if ($assignedQty <= 0) $assignedQty = 1;
            $nameKey = strtolower(trim($ja->style_name ?: ''));
            $prevQty = $prevReceivedByName[$nameKey] ?? (int)$ja->received_qty;
            $prevDef = $prevDefectByName[$nameKey] ?? (int)$ja->rejected_qty;
            $remQty = max(0, $assignedQty - $prevQty);

            $status = 'Pending';
            if ($remQty <= 0 && $prevQty > 0) {
                $status = 'Completed';
            } elseif ($prevQty > 0) {
                $status = 'Partially Received';
            }

            $totalAssigned += $assignedQty;
            $totalPrevReceived += $prevQty;
            $totalRemaining += $remQty;

            $singleThan = [
                'than_key' => 'ja_' . $ja->id . '_than_1',
                'than_no' => 1,
                'unique_id' => null,
                'challan_no' => null,
                'po_number' => null,
                'purchase_than_no' => null,
                'meter' => (float)$ja->total_than_meters,
                'usable' => (float)$ja->total_than_meters,
                'wastage' => (float)$ja->total_wastage_meters,
                'previously_returned_wastage' => 0,
                'remaining_wastage' => (float)$ja->total_wastage_meters,
                'assigned_pcs' => $assignedQty,
                'previously_received_pcs' => $prevQty,
                'remaining_pcs' => $remQty,
                'status' => $status
            ];

            $items[] = [
                'job_assignment_item_id' => null,
                'raw_item_id' => null,
                'raw_item_name' => null,
                'finished_item_id' => null,
                'finished_item_name' => $ja->style_name,
                'item_id' => null,
                'item_name' => $ja->style_name ?: 'Garment Product',
                'item_code' => '',
                'unit' => 'Pcs',
                'fabric_unit' => 'Meter',
                'rate' => (float)($ja->rate_per_piece ?: 0),
                'assigned_qty' => $assignedQty,
                'previously_received_qty' => $prevQty,
                'previously_defect_qty' => $prevDef,
                'remaining_qty' => $remQty,
                'status' => $status,
                'than_meters' => (float)$ja->total_than_meters,
                'than_count' => (int)$ja->total_thans ?: 1,
                'wastage_meters' => (float)$ja->total_wastage_meters,
                'assigned_thans' => [$singleThan]
            ];
        }

        return [
            'id' => $ja->id,
            'job_order_no' => $ja->job_order_no,
            'lot_number' => $ja->lot_number,
            'job_worker_id' => $ja->job_worker_id,
            'job_worker_name' => $ja->job_worker_name,
            'process_name' => $ja->process_name,
            'style_name' => $ja->style_name,
            'issue_date' => $ja->issue_date,
            'rate_per_piece' => (float)$ja->rate_per_piece,
            'total_assigned_qty' => $totalAssigned,
            'total_previously_received_qty' => $totalPrevReceived,
            'total_remaining_qty' => $totalRemaining,
            'status' => $ja->status,
            'items' => $items
        ];
    }

    public function create(Request $request)
    {
        $jobworkers = JobWorker::where('status', 'Active')->orderBy('name')->get();
        $assignments = JobAssignment::with(['items.finishedItem', 'items.rawItem', 'items.item', 'jobWorker'])
            ->whereNotIn('status', ['Cancelled'])
            ->orderBy('id', 'desc')
            ->get();

        $items = Item::whereRaw('LOWER(status) = ?', ['active'])->orderBy('name')->get();
        if ($items->isEmpty()) {
            $items = Item::orderBy('name')->get();
        }

        $preselectedId = $request->input('job_order_id');
        $preselectedWorkerId = $request->input('job_worker_id');

        $formattedAssignments = [];
        $assignmentsByWorker = [];

        foreach ($assignments as $ja) {
            $tracking = $this->formatAssignmentTracking($ja);
            
            // Include if remaining balance > 0 OR if specifically preselected
            if ($tracking['total_remaining_qty'] > 0 || ($preselectedId && $preselectedId == $ja->id)) {
                $formattedAssignments[$ja->id] = $tracking;
                
                $wKey = $ja->job_worker_id ? (string)$ja->job_worker_id : 'name_' . md5($ja->job_worker_name);
                if (!isset($assignmentsByWorker[$wKey])) {
                    $assignmentsByWorker[$wKey] = [];
                }
                $assignmentsByWorker[$wKey][] = $tracking;
            }
        }

        $year = date('Y');
        $count = JobInward::withTrashed()->count() + 1;
        $nextInwardNo = 'JINW-' . $year . '-' . str_pad($count, 4, '0', STR_PAD_LEFT);
        while (JobInward::withTrashed()->where('inward_number', $nextInwardNo)->exists()) {
            $count++;
            $nextInwardNo = 'JINW-' . $year . '-' . str_pad($count, 4, '0', STR_PAD_LEFT);
        }

        $nextChallanNo = 'JDC-' . $year . '-' . str_pad($count, 4, '0', STR_PAD_LEFT);

        return view('jobwork.inward.create', compact(
            'jobworkers',
            'formattedAssignments',
            'assignmentsByWorker',
            'preselectedId',
            'preselectedWorkerId',
            'nextInwardNo',
            'nextChallanNo',
            'items'
        ));
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'job_assignment_id' => 'required|exists:job_assignments,id',
            'inward_date' => 'required|date',
            'challan_no' => 'nullable|string|max:100',
            'received_qty' => 'nullable|integer|min:0',
            'defect_qty' => 'nullable|integer|min:0',
            'wastage_returned_meters' => 'nullable|numeric|min:0',
            'rate_per_piece' => 'nullable|numeric|min:0',
            'qc_status' => 'required|string|max:100',
            'storage_location' => 'nullable|string|max:100',
            'remarks' => 'nullable|string',
            'items' => 'nullable|array'
        ]);

        return DB::transaction(function () use ($validated, $request) {
            $assignment = JobAssignment::lockForUpdate()->findOrFail($validated['job_assignment_id']);
            $currentTracking = $this->formatAssignmentTracking($assignment);

            $year = date('Y');
            $count = JobInward::withTrashed()->count() + 1;
            $inwardNo = 'JINW-' . $year . '-' . str_pad($count, 4, '0', STR_PAD_LEFT);
            while (JobInward::withTrashed()->where('inward_number', $inwardNo)->exists()) {
                $count++;
                $inwardNo = 'JINW-' . $year . '-' . str_pad($count, 4, '0', STR_PAD_LEFT);
            }

            $rawItems = $request->input('items', []);
            $processedItems = [];
            $overallReceivedQty = 0;
            $overallDefectQty = 0;
            $overallWastageMeters = 0;
            $overallTotalThans = 0;
            $overallTotalMeters = 0;
            $allThansCombined = [];
            $totalAmount = 0;

            // Map tracking items for remaining validation
            $trackingItemsMap = [];
            foreach ($currentTracking['items'] as $ti) {
                if (!empty($ti['job_assignment_item_id'])) {
                    $trackingItemsMap['jai_' . $ti['job_assignment_item_id']] = $ti;
                }
                if (!empty($ti['item_id'])) {
                    $trackingItemsMap['itm_' . $ti['item_id']] = $ti;
                }
                if (!empty($ti['item_name'])) {
                    $trackingItemsMap['name_' . strtolower(trim($ti['item_name']))] = $ti;
                }
            }

            if (is_array($rawItems) && count($rawItems) > 0) {
                foreach ($rawItems as $itm) {
                    if (empty($itm) || !is_array($itm)) continue;

                    $itemName = trim($itm['item_name'] ?? '');
                    if (empty($itemName)) continue;

                    $jaiId = !empty($itm['job_assignment_item_id']) ? (int)$itm['job_assignment_item_id'] : null;
                    $itemId = !empty($itm['item_id']) ? (int)$itm['item_id'] : null;
                    
                    // Match tracking data
                    $matchedTracking = null;
                    if ($jaiId && isset($trackingItemsMap['jai_' . $jaiId])) {
                        $matchedTracking = $trackingItemsMap['jai_' . $jaiId];
                    } elseif ($itemId && isset($trackingItemsMap['itm_' . $itemId])) {
                        $matchedTracking = $trackingItemsMap['itm_' . $itemId];
                    } elseif (isset($trackingItemsMap['name_' . strtolower(trim($itemName))])) {
                        $matchedTracking = $trackingItemsMap['name_' . strtolower(trim($itemName))];
                    }

                    $assignedQty = $matchedTracking ? (int)$matchedTracking['assigned_qty'] : (int)($itm['assigned_qty'] ?? 0);
                    $prevReceivedQty = $matchedTracking ? (int)$matchedTracking['previously_received_qty'] : (int)($itm['previously_received_qty'] ?? 0);
                    $maxAllowed = $matchedTracking ? (int)$matchedTracking['remaining_qty'] : $assignedQty;

                    $receivedQty = (int)($itm['received_qty'] ?? 0);
                    $defectQty = (int)($itm['defect_qty'] ?? 0);
                    $wastageMeters = (float)($itm['wastage_returned_meters'] ?? 0);
                    $rate = isset($itm['rate']) && $itm['rate'] !== '' ? (float)$itm['rate'] : (float)$assignment->rate_per_piece;

                    // Process Than-wise inward entries if present
                    $rawAssignedThans = $itm['assigned_thans'] ?? [];
                    $processedAssignedThans = [];
                    $sumThanReceivedPcs = 0;
                    $sumThanDefectPcs = 0;
                    $sumThanWastage = 0;

                    if (is_array($rawAssignedThans) && count($rawAssignedThans) > 0) {
                        foreach ($rawAssignedThans as $at) {
                            $thAssignedPcs = (int)($at['assigned_pcs'] ?? 0);
                            $thPrevRecPcs = (int)($at['previously_received_pcs'] ?? 0);
                            $thMaxAllowed = max(0, $thAssignedPcs - $thPrevRecPcs);
                            $thRecPcs = (int)($at['received_pcs'] ?? 0);
                            $thDefPcs = (int)($at['defect_pcs'] ?? 0);
                            $thWastage = (float)($at['wastage'] ?? 0);
                            $thPrevWastage = (float)($at['previously_returned_wastage'] ?? 0);
                            $thRetWastage = isset($at['wastage_returned']) ? (float)$at['wastage_returned'] : (float)($at['wastage_returned_meters'] ?? 0);

                            if ($thMaxAllowed > 0 && $thRecPcs > $thMaxAllowed) {
                                $thRecPcs = $thMaxAllowed;
                            }

                            $thRemPcs = max(0, $thAssignedPcs - ($thPrevRecPcs + $thRecPcs));
                            $thRemWastage = max(0, $thWastage - ($thPrevWastage + $thRetWastage));

                            $sumThanReceivedPcs += $thRecPcs;
                            $sumThanDefectPcs += $thDefPcs;
                            $sumThanWastage += $thRetWastage;

                            $processedAssignedThans[] = [
                                'than_key' => $at['than_key'] ?? null,
                                'than_no' => $at['than_no'] ?? null,
                                'unique_id' => $at['unique_id'] ?? null,
                                'challan_no' => $at['challan_no'] ?? null,
                                'po_number' => $at['po_number'] ?? null,
                                'purchase_than_no' => $at['purchase_than_no'] ?? null,
                                'meter' => (float)($at['meter'] ?? 0),
                                'usable' => (float)($at['usable'] ?? 0),
                                'wastage' => $thWastage,
                                'previously_returned_wastage' => $thPrevWastage,
                                'wastage_returned' => $thRetWastage,
                                'remaining_wastage' => $thRemWastage,
                                'assigned_pcs' => $thAssignedPcs,
                                'previously_received_pcs' => $thPrevRecPcs,
                                'received_pcs' => $thRecPcs,
                                'defect_pcs' => $thDefPcs,
                                'remaining_pcs' => $thRemPcs,
                                'status' => ($thRemPcs <= 0 && ($thPrevRecPcs + $thRecPcs) > 0) ? 'Completed' : ((($thPrevRecPcs + $thRecPcs) > 0) ? 'Partially Received' : 'Pending')
                            ];
                        }
                    }

                    if (!empty($processedAssignedThans) && $sumThanReceivedPcs > 0) {
                        $receivedQty = $sumThanReceivedPcs;
                    }
                    if (!empty($processedAssignedThans) && $sumThanDefectPcs > 0 && $defectQty <= 0) {
                        $defectQty = $sumThanDefectPcs;
                    }
                    if (!empty($processedAssignedThans) && $sumThanWastage > 0) {
                        $wastageMeters = $sumThanWastage;
                    }

                    // Enforce remaining limit on item overall
                    if ($maxAllowed > 0 && $receivedQty > $maxAllowed) {
                        $receivedQty = $maxAllowed;
                    }

                    $thansRaw = $itm['thans'] ?? [];
                    $cleanThans = [];
                    if (is_string($thansRaw)) {
                        $thansRaw = explode(',', $thansRaw);
                    }
                    if (is_array($thansRaw)) {
                        foreach ($thansRaw as $t) {
                            $val = (float)$t;
                            if ($val > 0) {
                                $cleanThans[] = round($val, 2);
                            }
                        }
                    }

                    $totalMeters = round(array_sum($cleanThans), 2);
                    $thanCount = count($cleanThans);
                    $calcQty = $receivedQty > 0 ? $receivedQty : $totalMeters;
                    $lineTotal = round($calcQty * $rate, 2);
                    $newRemaining = max(0, $assignedQty - ($prevReceivedQty + $receivedQty));

                    $processedItems[] = [
                        'job_assignment_item_id' => $jaiId,
                        'item_id' => $itemId,
                        'item_name' => $itemName,
                        'item_code' => $itm['item_code'] ?? null,
                        'unit' => 'Pcs',
                        'assigned_qty' => $assignedQty,
                        'previously_received_qty' => $prevReceivedQty,
                        'received_qty' => $receivedQty,
                        'defect_qty' => $defectQty,
                        'remaining_qty' => $newRemaining,
                        'than_count' => count($processedAssignedThans) > 0 ? count($processedAssignedThans) : $thanCount,
                        'total_meters' => $totalMeters,
                        'thans' => $cleanThans,
                        'assigned_thans' => $processedAssignedThans,
                        'wastage_returned_meters' => $wastageMeters,
                        'rate' => $rate,
                        'line_total' => $lineTotal
                    ];

                    $overallReceivedQty += $receivedQty;
                    $overallDefectQty += $defectQty;
                    $overallWastageMeters += $wastageMeters;
                    $overallTotalThans += $thanCount;
                    $overallTotalMeters += $totalMeters;
                    $totalAmount += $lineTotal;

                    if (!empty($cleanThans)) {
                        $allThansCombined = array_merge($allThansCombined, $cleanThans);
                    }

                    // Increment finished item stock in items table
                    $stockItemId = $itemId ?: ($matchedTracking['finished_item_id'] ?? ($matchedTracking['item_id'] ?? null));
                    if ($stockItemId && $receivedQty > 0) {
                        $stockItem = Item::find($stockItemId);
                        if ($stockItem) {
                            $stockItem->current_stock = (float)$stockItem->current_stock + (float)$receivedQty;
                            $stockItem->save();
                        }
                    }
                }
            }

            if (empty($processedItems)) {
                $overallReceivedQty = (int)($validated['received_qty'] ?? 0);
                $overallDefectQty = (int)($validated['defect_qty'] ?? 0);
                $overallWastageMeters = (float)($validated['wastage_returned_meters'] ?? 0);
                $rate = (float)($request->input('rate_per_piece', $assignment->rate_per_piece) ?: 0);
                $totalAmount = $overallReceivedQty * $rate;
            }

            $rate = (float)($request->input('rate_per_piece', $assignment->rate_per_piece) ?: 0);
            if ($totalAmount <= 0) {
                $totalAmount = $overallReceivedQty * $rate;
            }

            $challanNo = trim((string)$request->input('challan_no', ''));
            if (empty($challanNo)) {
                $year = date('Y');
                $cCount = JobInward::withTrashed()->count() + 1;
                $challanNo = 'JDC-' . $year . '-' . str_pad($cCount, 4, '0', STR_PAD_LEFT);
                while (JobInward::withTrashed()->where('challan_no', $challanNo)->exists()) {
                    $cCount++;
                    $challanNo = 'JDC-' . $year . '-' . str_pad($cCount, 4, '0', STR_PAD_LEFT);
                }
            }

            $notesPayload = [
                'user_remarks' => $request->input('remarks'),
                'total_items' => count($processedItems),
                'total_thans' => $overallTotalThans,
                'total_meters' => round($overallTotalMeters, 2),
                'thans' => $allThansCombined,
                'items' => $processedItems
            ];

            $inwardData = [
                'inward_number' => $inwardNo,
                'job_assignment_id' => $assignment->id,
                'job_order_no' => $assignment->job_order_no,
                'lot_number' => $assignment->lot_number,
                'job_worker_id' => $assignment->job_worker_id,
                'job_worker_name' => $assignment->job_worker_name,
                'process_name' => $assignment->process_name,
                'style_name' => $assignment->style_name,
                'inward_date' => $validated['inward_date'],
                'challan_no' => $challanNo,
                'received_qty' => $overallReceivedQty,
                'defect_qty' => $overallDefectQty,
                'wastage_returned_meters' => round($overallWastageMeters, 2),
                'rate_per_piece' => $rate,
                'total_amount' => round($totalAmount, 2),
                'qc_status' => $validated['qc_status'] ?? 'Passed QC',
                'storage_location' => $validated['storage_location'] ?? 'Finished Goods Stock',
                'remarks' => json_encode($notesPayload)
            ];

            if (Schema::hasColumn('job_inwards', 'total_thans')) {
                $inwardData['total_thans'] = $overallTotalThans;
            }
            if (Schema::hasColumn('job_inwards', 'total_meters')) {
                $inwardData['total_meters'] = round($overallTotalMeters, 2);
            }
            if (Schema::hasColumn('job_inwards', 'than_details')) {
                $inwardData['than_details'] = !empty($allThansCombined) ? json_encode($allThansCombined) : null;
            }
            if (Schema::hasColumn('job_inwards', 'items_data')) {
                $inwardData['items_data'] = !empty($processedItems) ? json_encode($processedItems) : null;
            }

            $inward = JobInward::create($inwardData);

            // Recompute total received on parent Job Assignment across all inwards
            $allAssignmentInwards = JobInward::where('job_assignment_id', $assignment->id)->get();
            $totReceived = $allAssignmentInwards->sum('received_qty');
            $totDefect = $allAssignmentInwards->sum('defect_qty');

            $assignment->received_qty = (int)$totReceived;
            $assignment->rejected_qty = (int)$totDefect;

            $totalIssued = (int)$assignment->issued_qty;
            $pending = max(0, $totalIssued - $totReceived);

            if ($pending <= 0) {
                $assignment->status = 'Completed';
            } elseif ($totReceived > 0) {
                $assignment->status = 'Partial Ready';
            } else {
                $assignment->status = 'Issued';
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
                ->with('success', "Job Inward Receipt {$inwardNo} recorded successfully! Received {$overallReceivedQty} pieces / {$overallTotalThans} thans from {$assignment->job_worker_name}.");
        });
    }

    public function edit($id)
    {
        $inward = JobInward::with(['jobAssignment.items.finishedItem', 'jobAssignment.items.rawItem', 'jobAssignment.items.item', 'jobWorker'])->findOrFail($id);
        $jobworkers = JobWorker::where('status', 'Active')->orderBy('name')->get();
        
        $ja = $inward->jobAssignment;
        $items = Item::whereRaw('LOWER(status) = ?', ['active'])->orderBy('name')->get();
        if ($items->isEmpty()) {
            $items = Item::orderBy('name')->get();
        }

        // Format tracking for this assignment, excluding this inward itself from previous inward sums
        $tracking = $ja ? $this->formatAssignmentTracking($ja, $inward->id) : null;
        
        // Also get all assignments for worker selection if needed
        $assignments = JobAssignment::with(['items.finishedItem', 'items.rawItem', 'items.item', 'jobWorker'])
            ->whereNotIn('status', ['Cancelled'])
            ->orderBy('id', 'desc')
            ->get();

        $formattedAssignments = [];
        $assignmentsByWorker = [];

        foreach ($assignments as $a) {
            $t = ($ja && $a->id == $ja->id) ? $tracking : $this->formatAssignmentTracking($a);
            $formattedAssignments[$a->id] = $t;

            $wKey = $a->job_worker_id ? (string)$a->job_worker_id : 'name_' . md5($a->job_worker_name);
            if (!isset($assignmentsByWorker[$wKey])) {
                $assignmentsByWorker[$wKey] = [];
            }
            $assignmentsByWorker[$wKey][] = $t;
        }

        return view('jobwork.inward.edit', compact(
            'inward',
            'jobworkers',
            'formattedAssignments',
            'assignmentsByWorker',
            'items'
        ));
    }

    public function update(Request $request, $id)
    {
        $inward = JobInward::findOrFail($id);

        $validated = $request->validate([
            'job_assignment_id' => 'required|exists:job_assignments,id',
            'inward_date' => 'required|date',
            'challan_no' => 'nullable|string|max:100',
            'received_qty' => 'nullable|integer|min:0',
            'defect_qty' => 'nullable|integer|min:0',
            'rate_per_piece' => 'nullable|numeric|min:0',
            'qc_status' => 'required|string|max:100',
            'storage_location' => 'nullable|string|max:100',
            'remarks' => 'nullable|string',
            'items' => 'nullable|array'
        ]);

        return DB::transaction(function () use ($validated, $request, $inward) {
            // 1. Revert previous stock changes for this inward
            $oldInwItems = $inward->items_list;
            if (!empty($oldInwItems)) {
                foreach ($oldInwItems as $it) {
                    $oldItemId = $it['item_id'] ?? null;
                    $oldQty = (int)($it['received_qty'] ?? 0);
                    if ($oldItemId && $oldQty > 0) {
                        $stockItem = Item::find($oldItemId);
                        if ($stockItem) {
                            $stockItem->current_stock = max(0, (float)$stockItem->current_stock - $oldQty);
                            $stockItem->save();
                        }
                    }
                }
            }

            $assignment = JobAssignment::lockForUpdate()->findOrFail($validated['job_assignment_id']);
            $currentTracking = $this->formatAssignmentTracking($assignment, $inward->id);

            $rawItems = $request->input('items', []);
            $processedItems = [];
            $overallReceivedQty = 0;
            $overallDefectQty = 0;
            $overallTotalThans = 0;
            $overallTotalMeters = 0;
            $allThansCombined = [];
            $totalAmount = 0;

            // Map tracking items for remaining validation
            $trackingItemsMap = [];
            foreach ($currentTracking['items'] as $ti) {
                if (!empty($ti['job_assignment_item_id'])) {
                    $trackingItemsMap['jai_' . $ti['job_assignment_item_id']] = $ti;
                }
                if (!empty($ti['item_id'])) {
                    $trackingItemsMap['itm_' . $ti['item_id']] = $ti;
                }
                if (!empty($ti['item_name'])) {
                    $trackingItemsMap['name_' . strtolower(trim($ti['item_name']))] = $ti;
                }
            }

            if (is_array($rawItems) && count($rawItems) > 0) {
                foreach ($rawItems as $itm) {
                    if (empty($itm) || !is_array($itm)) continue;

                    $itemName = trim($itm['item_name'] ?? '');
                    if (empty($itemName)) continue;

                    $jaiId = !empty($itm['job_assignment_item_id']) ? (int)$itm['job_assignment_item_id'] : null;
                    $itemId = !empty($itm['item_id']) ? (int)$itm['item_id'] : null;

                    // Match tracking data
                    $matchedTracking = null;
                    if ($jaiId && isset($trackingItemsMap['jai_' . $jaiId])) {
                        $matchedTracking = $trackingItemsMap['jai_' . $jaiId];
                    } elseif ($itemId && isset($trackingItemsMap['itm_' . $itemId])) {
                        $matchedTracking = $trackingItemsMap['itm_' . $itemId];
                    } elseif (isset($trackingItemsMap['name_' . strtolower(trim($itemName))])) {
                        $matchedTracking = $trackingItemsMap['name_' . strtolower(trim($itemName))];
                    }

                    $assignedQty = $matchedTracking ? (int)$matchedTracking['assigned_qty'] : (int)($itm['assigned_qty'] ?? 0);
                    $prevReceivedQty = $matchedTracking ? (int)$matchedTracking['previously_received_qty'] : (int)($itm['previously_received_qty'] ?? 0);
                    $maxAllowed = $matchedTracking ? (int)$matchedTracking['remaining_qty'] : $assignedQty;

                    $receivedQty = (int)($itm['received_qty'] ?? 0);
                    $defectQty = (int)($itm['defect_qty'] ?? 0);
                    $rate = isset($itm['rate']) && $itm['rate'] !== '' ? (float)$itm['rate'] : (float)$assignment->rate_per_piece;

                    // Process Than-wise inward entries if present
                    $rawAssignedThans = $itm['assigned_thans'] ?? [];
                    $processedAssignedThans = [];
                    $sumThanReceivedPcs = 0;
                    $sumThanDefectPcs = 0;

                    if (is_array($rawAssignedThans) && count($rawAssignedThans) > 0) {
                        foreach ($rawAssignedThans as $at) {
                            $thAssignedPcs = (int)($at['assigned_pcs'] ?? 0);
                            $thPrevRecPcs = (int)($at['previously_received_pcs'] ?? 0);
                            $thMaxAllowed = max(0, $thAssignedPcs - $thPrevRecPcs);
                            $thRecPcs = (int)($at['received_pcs'] ?? 0);
                            $thDefPcs = (int)($at['defect_pcs'] ?? 0);

                            if ($thMaxAllowed > 0 && $thRecPcs > $thMaxAllowed) {
                                $thRecPcs = $thMaxAllowed;
                            }

                            $thRemPcs = max(0, $thAssignedPcs - ($thPrevRecPcs + $thRecPcs));

                            $sumThanReceivedPcs += $thRecPcs;
                            $sumThanDefectPcs += $thDefPcs;

                            $processedAssignedThans[] = [
                                'than_key' => $at['than_key'] ?? null,
                                'than_no' => $at['than_no'] ?? null,
                                'unique_id' => $at['unique_id'] ?? null,
                                'challan_no' => $at['challan_no'] ?? null,
                                'po_number' => $at['po_number'] ?? null,
                                'purchase_than_no' => $at['purchase_than_no'] ?? null,
                                'meter' => (float)($at['meter'] ?? 0),
                                'usable' => (float)($at['usable'] ?? 0),
                                'assigned_pcs' => $thAssignedPcs,
                                'previously_received_pcs' => $thPrevRecPcs,
                                'received_pcs' => $thRecPcs,
                                'defect_pcs' => $thDefPcs,
                                'remaining_pcs' => $thRemPcs,
                                'status' => ($thRemPcs <= 0 && ($thPrevRecPcs + $thRecPcs) > 0) ? 'Completed' : ((($thPrevRecPcs + $thRecPcs) > 0) ? 'Partial Ready' : 'Pending')
                            ];
                        }
                    }

                    if (!empty($processedAssignedThans) && $sumThanReceivedPcs > 0) {
                        $receivedQty = $sumThanReceivedPcs;
                    }
                    if (!empty($processedAssignedThans) && $sumThanDefectPcs > 0 && $defectQty <= 0) {
                        $defectQty = $sumThanDefectPcs;
                    }

                    // Enforce remaining limit on item overall
                    if ($maxAllowed > 0 && $receivedQty > $maxAllowed) {
                        $receivedQty = $maxAllowed;
                    }

                    $thansRaw = $itm['thans'] ?? [];
                    $cleanThans = [];
                    if (is_string($thansRaw)) {
                        $thansRaw = explode(',', $thansRaw);
                    }
                    if (is_array($thansRaw)) {
                        foreach ($thansRaw as $t) {
                            $t = (float)trim((string)$t);
                            if ($t > 0) {
                                $cleanThans[] = $t;
                                $overallTotalMeters += $t;
                                $overallTotalThans++;
                                $allThansCombined[] = [
                                    'than_no' => count($cleanThans),
                                    'item_name' => $itemName,
                                    'meter' => $t
                                ];
                            }
                        }
                    }

                    if (empty($cleanThans) && !empty($processedAssignedThans)) {
                        foreach ($processedAssignedThans as $pat) {
                            if (($pat['received_pcs'] ?? 0) > 0) {
                                $m = (float)($pat['meter'] ?? 0);
                                $cleanThans[] = $m;
                                $overallTotalMeters += $m;
                                $overallTotalThans++;
                                $allThansCombined[] = [
                                    'than_no' => $pat['than_no'] ?? count($cleanThans),
                                    'item_name' => $itemName,
                                    'meter' => $m,
                                    'received_pcs' => $pat['received_pcs'],
                                    'challan_no' => $pat['challan_no'] ?? ''
                                ];
                            }
                        }
                    }

                    $itemAmount = round($receivedQty * $rate, 2);
                    $totalAmount += $itemAmount;

                    $overallReceivedQty += $receivedQty;
                    $overallDefectQty += $defectQty;

                    $processedItems[] = [
                        'job_assignment_item_id' => $jaiId,
                        'item_id' => $itemId,
                        'item_name' => $itemName,
                        'item_code' => $itm['item_code'] ?? '',
                        'unit' => $itm['unit'] ?? 'Pcs',
                        'assigned_qty' => $assignedQty,
                        'previously_received_qty' => $prevReceivedQty,
                        'received_qty' => $receivedQty,
                        'defect_qty' => $defectQty,
                        'rate' => $rate,
                        'total_amount' => $itemAmount,
                        'remaining_qty' => max(0, $assignedQty - ($prevReceivedQty + $receivedQty)),
                        'status' => (max(0, $assignedQty - ($prevReceivedQty + $receivedQty)) <= 0 && ($prevReceivedQty + $receivedQty) > 0) ? 'Completed' : ((($prevReceivedQty + $receivedQty) > 0) ? 'Partial Ready' : 'Pending'),
                        'thans' => $cleanThans,
                        'assigned_thans' => $processedAssignedThans
                    ];

                    // Increment finished item stock in inventory
                    if ($itemId && $receivedQty > 0) {
                        $stockItem = Item::find($itemId);
                        if ($stockItem) {
                            $stockItem->current_stock = (float)$stockItem->current_stock + $receivedQty;
                            $stockItem->save();
                        }
                    }
                }
            }

            if ($overallReceivedQty <= 0) {
                $overallReceivedQty = (int)($validated['received_qty'] ?? 0);
            }
            if ($overallDefectQty <= 0) {
                $overallDefectQty = (int)($validated['defect_qty'] ?? 0);
            }

            $rate = isset($validated['rate_per_piece']) && $validated['rate_per_piece'] !== '' ? (float)$validated['rate_per_piece'] : (float)$assignment->rate_per_piece;
            if ($totalAmount <= 0) {
                $totalAmount = $overallReceivedQty * $rate;
            }

            $challanNo = !empty($validated['challan_no']) ? trim($validated['challan_no']) : $inward->challan_no;

            $notesPayload = [
                'user_remarks' => $request->input('remarks'),
                'total_items' => count($processedItems),
                'total_thans' => $overallTotalThans,
                'total_meters' => round($overallTotalMeters, 2),
                'thans' => $allThansCombined,
                'items' => $processedItems
            ];

            $updateData = [
                'job_assignment_id' => $assignment->id,
                'job_order_no' => $assignment->job_order_no,
                'lot_number' => $assignment->lot_number,
                'job_worker_id' => $assignment->job_worker_id,
                'job_worker_name' => $assignment->job_worker_name,
                'process_name' => $assignment->process_name,
                'style_name' => $assignment->style_name,
                'inward_date' => $validated['inward_date'],
                'challan_no' => $challanNo,
                'received_qty' => $overallReceivedQty,
                'defect_qty' => $overallDefectQty,
                'rate_per_piece' => $rate,
                'total_amount' => round($totalAmount, 2),
                'qc_status' => $validated['qc_status'] ?? 'Passed QC',
                'storage_location' => $validated['storage_location'] ?? 'Finished Goods Stock',
                'remarks' => json_encode($notesPayload)
            ];

            if (Schema::hasColumn('job_inwards', 'total_thans')) {
                $updateData['total_thans'] = $overallTotalThans;
            }
            if (Schema::hasColumn('job_inwards', 'total_meters')) {
                $updateData['total_meters'] = round($overallTotalMeters, 2);
            }
            if (Schema::hasColumn('job_inwards', 'than_details')) {
                $updateData['than_details'] = !empty($allThansCombined) ? json_encode($allThansCombined) : null;
            }
            if (Schema::hasColumn('job_inwards', 'items_data')) {
                $updateData['items_data'] = !empty($processedItems) ? json_encode($processedItems) : null;
            }

            $inward->update($updateData);

            // Recompute total received on parent Job Assignment across all inwards
            $allAssignmentInwards = JobInward::where('job_assignment_id', $assignment->id)->get();
            $totReceived = $allAssignmentInwards->sum('received_qty');
            $totDefect = $allAssignmentInwards->sum('defect_qty');

            $assignment->received_qty = (int)$totReceived;
            $assignment->rejected_qty = (int)$totDefect;

            $totalIssued = (int)$assignment->issued_qty;
            $pending = max(0, $totalIssued - $totReceived);

            if ($pending <= 0) {
                $assignment->status = 'Completed';
            } elseif ($totReceived > 0) {
                $assignment->status = 'Partial Ready';
            } else {
                $assignment->status = 'Issued';
            }
            $assignment->save();

            if ($request->wantsJson() || $request->ajax()) {
                return response()->json([
                    'success' => true,
                    'message' => "Job Inward {$inward->inward_number} updated successfully!",
                    'inward' => $inward
                ]);
            }

            return redirect()->route('jobwork.inward.index')
                ->with('success', "Job Inward Receipt {$inward->inward_number} updated successfully! Received {$overallReceivedQty} pieces / {$overallTotalThans} thans from {$assignment->job_worker_name}.");
        });
    }

    public function destroy($id)
    {
        $inward = JobInward::findOrFail($id);

        DB::transaction(function () use ($inward) {
            // Revert finished item stocks
            $inwItems = $inward->items_list;
            if (!empty($inwItems)) {
                foreach ($inwItems as $it) {
                    $itemId = $it['item_id'] ?? null;
                    $qty = (int)($it['received_qty'] ?? 0);
                    if ($itemId && $qty > 0) {
                        $stockItem = Item::find($itemId);
                        if ($stockItem) {
                            $stockItem->current_stock = max(0, (float)$stockItem->current_stock - $qty);
                            $stockItem->save();
                        }
                    }
                }
            }

            $assignId = $inward->job_assignment_id;
            $inward->delete();

            $assignment = JobAssignment::find($assignId);
            if ($assignment) {
                $remainingInwards = JobInward::where('job_assignment_id', $assignment->id)->get();
                $totReceived = $remainingInwards->sum('received_qty');
                $totDefect = $remainingInwards->sum('defect_qty');

                $assignment->received_qty = (int)$totReceived;
                $assignment->rejected_qty = (int)$totDefect;

                $totalIssued = (int)$assignment->issued_qty;
                $pending = max(0, $totalIssued - $totReceived);

                if ($pending <= 0 && $totalIssued > 0) {
                    $assignment->status = 'Completed';
                } elseif ($totReceived > 0) {
                    $assignment->status = 'Partial Ready';
                } else {
                    $assignment->status = 'Issued';
                }
                $assignment->save();
            }
        });

        return redirect()->route('jobwork.inward.index')
            ->with('success', "Job Inward record removed.");
    }
}
