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

        $items = \App\Models\Item::whereRaw('LOWER(status) = ?', ['active'])->orderBy('name')->get();
        if ($items->isEmpty()) {
            $items = \App\Models\Item::orderBy('name')->get();
        }

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

        $nextChallanNo = 'JDC-' . date('Y') . '-' . str_pad($count, 4, '0', STR_PAD_LEFT);

        return view('jobwork.inward.create', compact('activeAssignments', 'preselected', 'nextInwardNo', 'nextChallanNo', 'items'));
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

            $count = JobInward::count() + 1;
            $inwardNo = 'JINW-2026-' . str_pad($count, 4, '0', STR_PAD_LEFT);
            while (JobInward::where('inward_number', $inwardNo)->exists()) {
                $count++;
                $inwardNo = 'JINW-2026-' . str_pad($count, 4, '0', STR_PAD_LEFT);
            }

            $processedItems = $this->normalizeInwardItemsFromRequest($request, $assignment->rate_per_piece);

            $overallReceivedQty = 0;
            $overallDefectQty = 0;
            $overallWastageMeters = 0;
            $overallTotalThans = 0;
            $overallTotalMeters = 0;
            $allThansCombined = [];
            $totalAmount = 0;

            if (!empty($processedItems)) {
                foreach ($processedItems as $pItem) {
                    $overallReceivedQty += $pItem['received_qty'];
                    $overallDefectQty += $pItem['defect_qty'];
                    $overallWastageMeters += $pItem['wastage_returned_meters'];
                    $overallTotalThans += $pItem['than_count'];
                    $overallTotalMeters += $pItem['total_meters'];
                    $totalAmount += $pItem['line_total'];
                    if (!empty($pItem['thans'])) {
                        $allThansCombined = array_merge($allThansCombined, $pItem['thans']);
                    }
                }
            } else {
                $overallReceivedQty = (int)($validated['received_qty'] ?? 0);
                $overallDefectQty = (int)($validated['defect_qty'] ?? 0);
                $overallWastageMeters = (float)($validated['wastage_returned_meters'] ?? 0);
                $rate = (float)($request->input('rate_per_piece', $assignment->rate_per_piece) ?: 0);
                $totalAmount = $overallReceivedQty * $rate;
            }

            if ($overallReceivedQty <= 0 && $overallTotalMeters <= 0) {
                $overallReceivedQty = (int)($validated['received_qty'] ?? 0);
            }

            $rate = (float)($request->input('rate_per_piece', $assignment->rate_per_piece) ?: 0);
            if ($totalAmount <= 0) {
                $totalAmount = $overallReceivedQty * $rate;
            }

            $challanNo = trim((string)$request->input('challan_no', ''));
            if (empty($challanNo)) {
                $cCount = JobInward::count() + 1;
                $challanNo = 'JDC-' . date('Y') . '-' . str_pad($cCount, 4, '0', STR_PAD_LEFT);
                while (JobInward::where('challan_no', $challanNo)->exists()) {
                    $cCount++;
                    $challanNo = 'JDC-' . date('Y') . '-' . str_pad($cCount, 4, '0', STR_PAD_LEFT);
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

            // Update parent job assignment received & rejected counts
            $effectiveReceived = $overallReceivedQty > 0 ? $overallReceivedQty : (int)ceil($overallTotalMeters);
            $assignment->received_qty = (int)($assignment->received_qty + $effectiveReceived);
            $assignment->rejected_qty = (int)($assignment->rejected_qty + $overallDefectQty);

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
                ->with('success', "Job Inward Receipt {$inwardNo} recorded successfully! Received {$overallReceivedQty} pieces / {$overallTotalThans} thans ({$overallTotalMeters} Mtr) from {$assignment->job_worker_name}.");
        });
    }

    private function normalizeInwardItemsFromRequest(Request $request, $defaultRate = 0): array
    {
        $items = $request->input('items', []);
        $processed = [];

        if (is_array($items) && count($items) > 0) {
            foreach ($items as $itm) {
                if (empty($itm) || !is_array($itm)) continue;

                $itemName = trim($itm['item_name'] ?? '');
                if (empty($itemName)) continue;

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
                $receivedQty = (int)($itm['received_qty'] ?? 0);
                $defectQty = (int)($itm['defect_qty'] ?? 0);
                $wastageMeters = (float)($itm['wastage_returned_meters'] ?? 0);
                $rate = isset($itm['rate']) && $itm['rate'] !== '' ? (float)$itm['rate'] : (float)$defaultRate;
                
                $calcQty = $receivedQty > 0 ? $receivedQty : $totalMeters;
                $lineTotal = round($calcQty * $rate, 2);

                $processed[] = [
                    'item_id' => $itm['item_id'] ?? null,
                    'item_name' => $itemName,
                    'item_code' => $itm['item_code'] ?? null,
                    'unit' => $itm['unit'] ?? 'Pcs',
                    'received_qty' => $receivedQty,
                    'defect_qty' => $defectQty,
                    'than_count' => $thanCount,
                    'total_meters' => $totalMeters,
                    'thans' => $cleanThans,
                    'wastage_returned_meters' => $wastageMeters,
                    'rate' => $rate,
                    'line_total' => $lineTotal
                ];
            }
        }

        return $processed;
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
