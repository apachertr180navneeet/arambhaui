<?php

namespace App\Http\Controllers\Reports;

use App\Http\Controllers\Controller;
use App\Models\Item;
use App\Models\PurchaseOrder;
use App\Models\DispatchChallan;
use App\Models\JobAssignment;
use App\Models\JobAssignmentItem;
use App\Models\JobWorker;
use App\Models\Transaction;
use App\Models\CompanySetting;
use App\Services\StockLedgerService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class ReportsController extends Controller
{
    /**
     * Item Stock Movement & Transaction Ledger
     */
    public function itemLedger(Request $request)
    {
        // Auto-sync historical purchases and job works if transactions are empty or missing PO stock
        $hasPoUnsynced = PurchaseOrder::whereNotIn('status', ['Cancelled'])
            ->whereDoesntHave('transactions')
            ->exists();
            
        if ($hasPoUnsynced || Transaction::where('ledger_type', 'ItemStock')->count() === 0) {
            StockLedgerService::syncAllHistoricalStock();
        }

        $items = Item::orderBy('name')->get();
        $selectedItemId = $request->input('item_id');
        $selectedItem = $selectedItemId ? Item::find($selectedItemId) : null;

        $fromDate = $request->input('from_date');
        $toDate = $request->input('to_date');
        $search = $request->input('search');

        $query = Transaction::where('ledger_type', 'ItemStock');

        if ($selectedItemId) {
            $query->where('entity_id', $selectedItemId);
        }

        if ($fromDate) {
            $query->whereDate('transaction_date', '>=', $fromDate);
        }

        if ($toDate) {
            $query->whereDate('transaction_date', '<=', $toDate);
        }

        if ($search) {
            $query->where(function ($q) use ($search) {
                $q->where('entity_name', 'like', "%{$search}%")
                  ->orWhere('reference_no', 'like', "%{$search}%")
                  ->orWhere('transaction_type', 'like', "%{$search}%")
                  ->orWhere('description', 'like', "%{$search}%");
            });
        }

        // Chronological order for stock ledger
        $query->orderBy('transaction_date', 'asc')->orderBy('id', 'asc');

        $transactions = $query->get();

        // Calculate accurate cumulative running balance
        $running = 0.0;
        $totalIn = 0.0;
        $totalOut = 0.0;

        foreach ($transactions as $t) {
            $totalIn += $t->qty_in;
            $totalOut += $t->qty_out;
            $running = round($running + $t->qty_in - $t->qty_out, 2);
            $t->running_balance = $running;
        }

        // CSV Export
        if ($request->query('export') === 'csv') {
            return $this->exportStockLedgerCsv($transactions, $selectedItem, $fromDate, $toDate, $totalIn, $totalOut, $running);
        }

        if ($request->wantsJson() || $request->ajax()) {
            return response()->json([
                'success' => true,
                'transactions' => $transactions,
                'total_in' => $totalIn,
                'total_out' => $totalOut,
                'closing_stock' => $running
            ]);
        }

        $companySettings = CompanySetting::first();
        $companyName = $companySettings?->company_name ?? 'aarambh';

        return view('reports.ledger', compact(
            'transactions',
            'items',
            'selectedItem',
            'selectedItemId',
            'fromDate',
            'toDate',
            'search',
            'totalIn',
            'totalOut',
            'running',
            'companySettings',
            'companyName'
        ));
    }

    /**
     * Export Stock Ledger to CSV
     */
    protected function exportStockLedgerCsv($transactions, $item, $fromDate, $toDate, $totalIn, $totalOut, $closing)
    {
        $itemTitle = $item ? $item->name : 'All_Items';
        $filename = "Stock_Ledger_{$itemTitle}_" . date('Ymd_His') . ".csv";

        $headers = [
            'Content-Type' => 'text/csv; charset=UTF-8',
            'Content-Disposition' => "attachment; filename=\"{$filename}\"",
        ];

        $callback = function () use ($transactions, $item, $fromDate, $toDate, $totalIn, $totalOut, $closing) {
            $handle = fopen('php://output', 'w');
            fputcsv($handle, ['ITEM STOCK MOVEMENT LEDGER']);
            fputcsv($handle, ['Item / SKU:', $item ? "{$item->name} [{$item->code}]" : 'All Materials & Goods']);
            fputcsv($handle, ['Period:', ($fromDate ?: 'Start') . ' to ' . ($toDate ?: 'End')]);
            fputcsv($handle, []);
            fputcsv($handle, ['Date & Time', 'Item / Material', 'Transaction Type', 'Reference Doc #', 'Quantity In (+)', 'Quantity Out (-)', 'Running Stock', 'Unit', 'Notes']);

            foreach ($transactions as $t) {
                fputcsv($handle, [
                    date('d-M-Y H:i', strtotime($t->created_at)),
                    $t->item_name,
                    $t->type,
                    $t->reference_no,
                    $t->qty_in ? '+' . format_quantity($t->qty_in) : '',
                    $t->qty_out ? '-' . format_quantity($t->qty_out) : '',
                    format_quantity($t->running_balance),
                    $t->unit ?: 'MTR',
                    $t->description
                ]);
            }

            fputcsv($handle, []);
            fputcsv($handle, ['TOTALS', '', '', '', '+' . format_quantity($totalIn), '-' . format_quantity($totalOut), format_quantity($closing)]);
            fclose($handle);
        };

        return response()->stream($callback, 200, $headers);
    }

    /**
     * Job Work Register Report
     * Columns:
     * - Job Order Number, Job Order Date, Job Worker / Contractor Name, Item Name, Item Code,
     *   Issued Quantity, Received Quantity, Remaining Quantity, Unit, Contractor Rate, Total Job Work Amount, Job Work Status
     */
    public function jobWorkRegister(Request $request)
    {
        $jobworkers = JobWorker::orderBy('name')->get();
        $items = Item::orderBy('name')->get();

        $workerId = $request->input('worker_id');
        $itemId = $request->input('item_id');
        $jobOrderNo = $request->input('job_order_no');
        $status = $request->input('status'); // Pending, Partial, Completed
        $fromDate = $request->input('from_date');
        $toDate = $request->input('to_date');
        $search = $request->input('search');
        $preset = $request->input('preset');

        if ($preset === 'today') {
            $fromDate = date('Y-m-d');
            $toDate = date('Y-m-d');
        } elseif ($preset === 'this_month') {
            $fromDate = date('Y-m-01');
            $toDate = date('Y-m-t');
        } elseif ($preset === 'last_month') {
            $fromDate = date('Y-m-01', strtotime('-1 month'));
            $toDate = date('Y-m-t', strtotime('-1 month'));
        } elseif ($preset === 'fy') {
            $currentYear = (int) date('Y');
            $currentMonth = (int) date('n');
            $fyStartYear = ($currentMonth >= 4) ? $currentYear : ($currentYear - 1);
            $fromDate = "{$fyStartYear}-04-01";
            $toDate = date('Y-m-d');
        } elseif ($preset === 'all') {
            $fromDate = null;
            $toDate = null;
        }

        $query = JobAssignment::with(['items.rawItem', 'items.finishedItem', 'jobWorker', 'inwards'])
            ->whereNotIn('status', ['Cancelled']);

        if ($workerId) {
            $query->where('job_worker_id', $workerId);
        }

        if ($jobOrderNo) {
            $query->where('job_order_no', 'like', "%{$jobOrderNo}%");
        }

        if ($fromDate) {
            $query->whereDate('issue_date', '>=', $fromDate);
        }

        if ($toDate) {
            $query->whereDate('issue_date', '<=', $toDate);
        }

        if ($search) {
            $query->where(function ($q) use ($search) {
                $q->where('job_order_no', 'like', "%{$search}%")
                  ->orWhere('lot_number', 'like', "%{$search}%")
                  ->orWhere('job_worker_name', 'like', "%{$search}%")
                  ->orWhere('style_name', 'like', "%{$search}%")
                  ->orWhere('process_name', 'like', "%{$search}%");
            });
        }

        // Apply Status Filter
        if ($status && $status !== 'all') {
            if ($status === 'Pending') {
                $query->where(function ($q) {
                    $q->where('status', 'Issued')
                      ->orWhereRaw('received_qty = 0');
                });
            } elseif ($status === 'Partial') {
                $query->where(function ($q) {
                    $q->where('status', 'Partial Ready')
                      ->orWhere(function ($sub) {
                          $sub->where('received_qty', '>', 0)
                              ->whereRaw('received_qty < issued_qty');
                      });
                });
            } elseif ($status === 'Completed') {
                $query->where(function ($q) {
                    $q->where('status', 'Completed')
                      ->orWhereRaw('received_qty >= issued_qty');
                });
            }
        }

        // Sorting
        $sortCol = $request->input('sort', 'issue_date');
        $sortDir = $request->input('direction', 'desc');
        $allowedSorts = ['job_order_no', 'issue_date', 'job_worker_name', 'issued_qty', 'received_qty', 'total_amount', 'status'];
        if (in_array($sortCol, $allowedSorts)) {
            $query->orderBy($sortCol, $sortDir === 'asc' ? 'asc' : 'desc');
        } else {
            $query->orderBy('issue_date', 'desc')->orderBy('id', 'desc');
        }

        $allOrders = $query->get();

        // Calculate line-item register rows
        $registerRows = [];
        $totalIssuedAll = 0;
        $totalReceivedAll = 0;
        $totalRemainingAll = 0;
        $totalAmountAll = 0;

        foreach ($allOrders as $order) {
            // Check cumulative confirmed inward quantities
            $actualReceived = (int)$order->inwards()->sum('received_qty');
            if ($actualReceived != $order->received_qty) {
                $order->received_qty = $actualReceived;
            }

            $orderIssued = (int)$order->issued_qty;
            $orderRemaining = max(0, $orderIssued - $actualReceived);

            // Determine status
            $orderStatus = 'Pending';
            if ($orderRemaining <= 0 && $orderIssued > 0) {
                $orderStatus = 'Completed';
            } elseif ($actualReceived > 0) {
                $orderStatus = 'Partial';
            }

            // If item filter was chosen, check if order contains that item
            if ($itemId) {
                $hasItem = false;
                foreach ($order->items as $oItm) {
                    if ($oItm->raw_item_id == $itemId || $oItm->finished_item_id == $itemId || $oItm->item_id == $itemId) {
                        $hasItem = true;
                        break;
                    }
                }
                if (!$hasItem) continue;
            }

            // Build item-wise rows if items exist, or order-level summary
            if ($order->items && $order->items->count() > 0) {
                $itemCount = $order->items->count();
                $allocatedRec = $actualReceived;

                foreach ($order->items as $idx => $itm) {
                    $itemIssued = (int)($itm->production_pcs ?: ($itm->qty ?: ($orderIssued / $itemCount)));
                    // Calculate item received
                    $itemReceived = min($itemIssued, max(0, $allocatedRec));
                    $allocatedRec = max(0, $allocatedRec - $itemReceived);
                    $itemRemaining = max(0, $itemIssued - $itemReceived);

                    $itemName = $itm->finished_item_name ?: ($itm->item_name ?: ($itm->raw_item_name ?: $order->style_name));
                    $itemCode = $itm->finishedItem?->code ?: ($itm->item?->code ?: ($itm->rawItem?->code ?: '—'));
                    $itemRate = (float)($itm->rate_per_piece ?: ($order->rate_per_piece ?: 0));
                    $itemAmount = round($itemIssued * $itemRate, 2);

                    $itemStatus = 'Pending';
                    if ($itemRemaining <= 0 && $itemIssued > 0) {
                        $itemStatus = 'Completed';
                    } elseif ($itemReceived > 0) {
                        $itemStatus = 'Partial';
                    }

                    $totalIssuedAll += $itemIssued;
                    $totalReceivedAll += $itemReceived;
                    $totalRemainingAll += $itemRemaining;
                    $totalAmountAll += $itemAmount;

                    $registerRows[] = [
                        'order_id' => $order->id,
                        'job_order_no' => $order->job_order_no,
                        'lot_number' => $order->lot_number,
                        'job_order_date' => $order->issue_date ? $order->issue_date->format('d-M-Y') : '—',
                        'raw_date' => $order->issue_date,
                        'job_worker_name' => $order->job_worker_name,
                        'worker_code' => $order->jobWorker?->code,
                        'item_name' => $itemName,
                        'item_code' => $itemCode,
                        'issued_qty' => $itemIssued,
                        'received_qty' => $itemReceived,
                        'remaining_qty' => $itemRemaining,
                        'unit' => 'PCS',
                        'contractor_rate' => $itemRate,
                        'total_amount' => $itemAmount,
                        'status' => $itemStatus,
                        'process_name' => $order->process_name,
                        'due_date' => $order->due_date ? $order->due_date->format('d-M-Y') : null,
                    ];
                }
            } else {
                $rate = (float)($order->rate_per_piece ?: 0);
                $amt = round($orderIssued * $rate, 2);

                $totalIssuedAll += $orderIssued;
                $totalReceivedAll += $actualReceived;
                $totalRemainingAll += $orderRemaining;
                $totalAmountAll += $amt;

                $registerRows[] = [
                    'order_id' => $order->id,
                    'job_order_no' => $order->job_order_no,
                    'lot_number' => $order->lot_number,
                    'job_order_date' => $order->issue_date ? $order->issue_date->format('d-M-Y') : '—',
                    'raw_date' => $order->issue_date,
                    'job_worker_name' => $order->job_worker_name,
                    'worker_code' => $order->jobWorker?->code,
                    'item_name' => $order->style_name ?: 'Finished Garment',
                    'item_code' => '—',
                    'issued_qty' => $orderIssued,
                    'received_qty' => $actualReceived,
                    'remaining_qty' => $orderRemaining,
                    'unit' => 'PCS',
                    'contractor_rate' => $rate,
                    'total_amount' => $amt,
                    'status' => $orderStatus,
                    'process_name' => $order->process_name,
                    'due_date' => $order->due_date ? $order->due_date->format('d-M-Y') : null,
                ];
            }
        }

        // CSV / Excel Export
        if ($request->query('export') === 'csv' || $request->query('export') === 'excel') {
            return $this->exportJobWorkRegisterCsv($registerRows, $totalIssuedAll, $totalReceivedAll, $totalRemainingAll, $totalAmountAll, $fromDate, $toDate);
        }

        // PDF Export
        if ($request->query('export') === 'pdf') {
            return $this->exportJobWorkRegisterPdf($registerRows, $totalIssuedAll, $totalReceivedAll, $totalRemainingAll, $totalAmountAll, $fromDate, $toDate);
        }

        if ($request->wantsJson() || $request->ajax()) {
            return response()->json([
                'success' => true,
                'rows' => $registerRows,
                'total_issued' => $totalIssuedAll,
                'total_received' => $totalReceivedAll,
                'total_remaining' => $totalRemainingAll,
                'total_amount' => $totalAmountAll
            ]);
        }

        $companySettings = CompanySetting::first();
        $companyName = $companySettings?->company_name ?? 'aarambh';

        return view('reports.job-work-register', compact(
            'registerRows',
            'jobworkers',
            'items',
            'workerId',
            'itemId',
            'jobOrderNo',
            'status',
            'preset',
            'fromDate',
            'toDate',
            'search',
            'sortCol',
            'sortDir',
            'totalIssuedAll',
            'totalReceivedAll',
            'totalRemainingAll',
            'totalAmountAll',
            'companySettings',
            'companyName'
        ));
    }

    /**
     * Export Job Work Register to CSV / Excel
     */
    protected function exportJobWorkRegisterCsv($rows, $totIssued, $totRec, $totRem, $totAmt, $fromDate, $toDate)
    {
        $filename = "Job_Work_Register_" . date('Ymd_His') . ".csv";
        $headers = [
            'Content-Type' => 'text/csv; charset=UTF-8',
            'Content-Disposition' => "attachment; filename=\"{$filename}\"",
        ];

        $callback = function () use ($rows, $totIssued, $totRec, $totRem, $totAmt, $fromDate, $toDate) {
            $handle = fopen('php://output', 'w');
            fputcsv($handle, ['JOB WORK REGISTER REPORT']);
            fputcsv($handle, ['Period:', ($fromDate ?: 'Start') . ' to ' . ($toDate ?: 'End')]);
            fputcsv($handle, ['Generated:', now()->format('d-M-Y H:i')]);
            fputcsv($handle, []);
            fputcsv($handle, [
                'Job Order #',
                'Job Order Date',
                'Job Worker / Contractor',
                'Item Name',
                'Item Code',
                'Issued Qty',
                'Received Qty',
                'Remaining Qty',
                'Unit',
                'Contractor Rate (Rs)',
                'Total Amount (Rs)',
                'Status'
            ]);

            foreach ($rows as $r) {
                fputcsv($handle, [
                    $r['job_order_no'],
                    $r['job_order_date'],
                    $r['job_worker_name'],
                    $r['item_name'],
                    $r['item_code'],
                    $r['issued_qty'],
                    $r['received_qty'],
                    $r['remaining_qty'],
                    $r['unit'],
                    number_format($r['contractor_rate'], 2, '.', ''),
                    number_format($r['total_amount'], 2, '.', ''),
                    $r['status']
                ]);
            }

            fputcsv($handle, []);
            fputcsv($handle, [
                'TOTALS',
                '',
                '',
                '',
                '',
                $totIssued,
                $totRec,
                $totRem,
                'PCS',
                '',
                number_format($totAmt, 2, '.', ''),
                ''
            ]);

            fclose($handle);
        };

        return response()->stream($callback, 200, $headers);
    }

    /**
     * Export Job Work Register to PDF
     */
    protected function exportJobWorkRegisterPdf($rows, $totIssued, $totRec, $totRem, $totAmt, $fromDate, $toDate)
    {
        $companySettings = CompanySetting::first();
        $companyName = $companySettings?->company_name ?? 'aarambh';
        $filename = "Job_Work_Register_" . date('Ymd_His') . ".pdf";

        if (class_exists(\Barryvdh\DomPDF\Facade\Pdf::class)) {
            try {
                $pdf = \Barryvdh\DomPDF\Facade\Pdf::loadView('reports.job-work-register-pdf', compact(
                    'rows',
                    'totIssued',
                    'totRec',
                    'totRem',
                    'totAmt',
                    'fromDate',
                    'toDate',
                    'companySettings',
                    'companyName'
                ));
                $pdf->setPaper('a4', 'landscape');
                return $pdf->download($filename);
            } catch (\Throwable $e) {
                \Illuminate\Support\Facades\Log::warning('DomPDF failed: ' . $e->getMessage());
            }
        }

        // Fallback to print view
        return response()->view('reports.job-work-register-pdf', compact(
            'rows',
            'totIssued',
            'totRec',
            'totRem',
            'totAmt',
            'fromDate',
            'toDate',
            'companySettings',
            'companyName'
        ));
    }

    public function stockReport(Request $request)
    {
        $items = Item::latest()->get();

        if ($request->wantsJson() || $request->ajax()) {
            return response()->json($items);
        }

        $totalValuation = $items->sum(function($itm) {
            return $itm->current_stock * $itm->unit_cost;
        });

        $stats = [
            'totalItems' => $items->count(),
            'totalValuation' => $totalValuation,
            'lowStockCount' => $items->where('current_stock', '<=', 'min_stock')->count()
        ];

        return view('reports.stock', compact('items', 'stats'));
    }

    public function lotPurchase(Request $request)
    {
        $orders = PurchaseOrder::with(['items', 'vendor'])->latest()->get();

        if ($request->wantsJson() || $request->ajax()) {
            return response()->json($orders);
        }

        return view('reports.lot-purchase', compact('orders'));
    }

    public function lotSales(Request $request)
    {
        $customers = \App\Models\Customer::orderBy('name')->get();
        $items = Item::orderBy('name')->get();

        $customerId = $request->input('customer_id');
        $itemId = $request->input('item_id');
        $status = $request->input('status');
        $fromDate = $request->input('from_date');
        $toDate = $request->input('to_date');
        $search = $request->input('search');
        $preset = $request->input('preset');

        if ($preset === 'today') {
            $fromDate = date('Y-m-d');
            $toDate = date('Y-m-d');
        } elseif ($preset === 'this_month') {
            $fromDate = date('Y-m-01');
            $toDate = date('Y-m-t');
        } elseif ($preset === 'last_month') {
            $fromDate = date('Y-m-01', strtotime('-1 month'));
            $toDate = date('Y-m-t', strtotime('-1 month'));
        } elseif ($preset === 'fy') {
            $currentYear = (int) date('Y');
            $currentMonth = (int) date('n');
            $fyStartYear = ($currentMonth >= 4) ? $currentYear : ($currentYear - 1);
            $fromDate = "{$fyStartYear}-04-01";
            $toDate = date('Y-m-d');
        } elseif ($preset === 'all') {
            $fromDate = null;
            $toDate = null;
        }

        $query = DispatchChallan::with(['items.item', 'customer'])->latest('dispatch_date')->latest('id');

        if ($customerId) {
            $query->where('customer_id', $customerId);
        }

        if ($itemId) {
            $query->whereHas('items', function ($q) use ($itemId) {
                $q->where('item_id', $itemId);
            });
        }

        if ($status && $status !== 'all') {
            $query->where('status', $status);
        }

        if ($fromDate) {
            $query->whereDate('dispatch_date', '>=', $fromDate);
        }

        if ($toDate) {
            $query->whereDate('dispatch_date', '<=', $toDate);
        }

        if ($search) {
            $query->where(function ($q) use ($search) {
                $q->where('challan_no', 'like', "%{$search}%")
                  ->orWhere('customer_name', 'like', "%{$search}%")
                  ->orWhere('transporter_name', 'like', "%{$search}%")
                  ->orWhere('lr_number', 'like', "%{$search}%")
                  ->orWhere('vehicle_number', 'like', "%{$search}%")
                  ->orWhere('remarks', 'like', "%{$search}%");
            });
        }

        $challans = $query->get();

        $totalQty = 0;
        $totalThans = 0;
        $totalAmount = 0;
        $totalCartons = 0;

        foreach ($challans as $dc) {
            $totalQty += (float)$dc->total_qty;
            $totalThans += (int)($dc->total_thans ?: $dc->items->sum('than_count'));
            $totalAmount += (float)$dc->total_amount;
            $totalCartons += (int)$dc->total_cartons;
        }

        if ($request->query('export') === 'csv') {
            return $this->exportDispatchCsv($challans, $totalQty, $totalThans, $totalAmount);
        }

        if ($request->wantsJson() || $request->ajax()) {
            return response()->json([
                'challans' => $challans,
                'total_qty' => $totalQty,
                'total_thans' => $totalThans,
                'total_amount' => $totalAmount,
                'total_cartons' => $totalCartons,
                'total_count' => $challans->count()
            ]);
        }

        $companySettings = CompanySetting::first();
        $companyName = $companySettings?->company_name ?? 'aarambh';

        return view('reports.lot-sales', compact(
            'challans',
            'customers',
            'items',
            'customerId',
            'itemId',
            'status',
            'fromDate',
            'toDate',
            'preset',
            'search',
            'totalQty',
            'totalThans',
            'totalAmount',
            'totalCartons',
            'companySettings',
            'companyName'
        ));
    }

    protected function exportDispatchCsv($challans, $totalQty, $totalThans, $totalAmount)
    {
        $filename = "Dispatch_Register_" . date('Ymd_His') . ".csv";
        $headers = [
            'Content-Type' => 'text/csv; charset=UTF-8',
            'Content-Disposition' => "attachment; filename=\"{$filename}\"",
            'Pragma' => 'no-cache',
            'Cache-Control' => 'must-revalidate, post-check=0, pre-check=0',
            'Expires' => '0'
        ];

        $callback = function () use ($challans, $totalQty, $totalThans, $totalAmount) {
            $handle = fopen('php://output', 'w');
            fputcsv($handle, ['DISPATCH & SALES REGISTER REPORT']);
            fputcsv($handle, ['Generated on', date('d-M-Y H:i:s')]);
            fputcsv($handle, []);
            fputcsv($handle, ['S.No.', 'Challan #', 'Dispatch Date', 'Customer', 'Transporter', 'LR / Bilty #', 'Cartons', 'Thans', 'Dispatched Qty', 'Total Amount (INR)', 'Status']);

            foreach ($challans as $idx => $dc) {
                fputcsv($handle, [
                    $idx + 1,
                    $dc->challan_no,
                    date('d-M-Y', strtotime($dc->dispatch_date)),
                    $dc->customer_name,
                    $dc->transporter_name ?: '—',
                    $dc->bilty_no ?: ($dc->lr_number ?: '—'),
                    $dc->total_cartons,
                    $dc->total_thans ?: $dc->items->sum('than_count'),
                    $dc->total_qty,
                    $dc->total_amount,
                    $dc->status ?: 'Dispatched'
                ]);
            }

            fputcsv($handle, []);
            fputcsv($handle, ['TOTALS', '', '', '', '', '', '', $totalThans, $totalQty, $totalAmount, '']);
            fclose($handle);
        };

        return response()->stream($callback, 200, $headers);
    }
}
