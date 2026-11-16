<?php

namespace App\Http\Controllers\Purchase;

use App\Http\Controllers\Controller;
use App\Models\PurchaseOrder;
use App\Models\PurchaseOrderItem;
use App\Models\Vendor;
use App\Models\CompanySetting;
use Illuminate\Http\Request;
use Carbon\Carbon;

class PurchaseLedgerController extends Controller
{
    /**
     * Display Tally-Style Purchase Ledger Report with Item Details
     */
    public function index(Request $request)
    {
        $vendors = Vendor::whereRaw('LOWER(status) = ?', ['active'])->orderBy('name')->get();
        if ($vendors->isEmpty()) {
            $vendors = Vendor::orderBy('name')->get();
        }

        $items = \App\Models\Item::orderBy('name')->get();

        $selectedVendorId = $request->input('vendor_id');
        $selectedVendor = null;
        if (!empty($selectedVendorId)) {
            $selectedVendor = Vendor::find($selectedVendorId);
        }

        $selectedItemId = $request->input('item_id');
        $selectedStatus = $request->input('status');

        // Period Filtering: Financial Year default (India FY: Apr 1 - Mar 31)
        $currentYear = (int) date('Y');
        $currentMonth = (int) date('n');
        $fyStartYear = ($currentMonth >= 4) ? $currentYear : ($currentYear - 1);
        $defaultFromDate = "{$fyStartYear}-04-01";
        $defaultToDate = date('Y-m-d');

        $periodPreset = $request->input('preset', 'fy');
        if ($periodPreset === 'today') {
            $fromDate = date('Y-m-d');
            $toDate = date('Y-m-d');
        } elseif ($periodPreset === 'this_month') {
            $fromDate = date('Y-m-01');
            $toDate = date('Y-m-t');
        } elseif ($periodPreset === 'last_month') {
            $fromDate = date('Y-m-01', strtotime('-1 month'));
            $toDate = date('Y-m-t', strtotime('-1 month'));
        } elseif ($periodPreset === 'all') {
            $fromDate = null;
            $toDate = null;
        } else {
            $fromDate = $request->input('from_date', $defaultFromDate);
            $toDate = $request->input('to_date', $defaultToDate);
        }

        $query = PurchaseOrder::with(['items', 'vendor']);

        // Exclude cancelled bills unless specifically requested
        if ($selectedStatus && $selectedStatus !== 'all') {
            $query->where('status', $selectedStatus);
        } else {
            $query->where('status', '!=', 'Cancelled');
        }

        if ($selectedVendor) {
            $query->where(function ($q) use ($selectedVendor) {
                $q->where('vendor_id', $selectedVendor->id)
                  ->orWhere('vendor_name', $selectedVendor->name);
            });
        }

        if (!empty($selectedItemId)) {
            $query->whereHas('items', function ($q) use ($selectedItemId) {
                $q->where('item_id', $selectedItemId);
            });
        }

        if (!empty($fromDate)) {
            $query->whereDate('po_date', '>=', $fromDate);
        }
        if (!empty($toDate)) {
            $query->whereDate('po_date', '<=', $toDate);
        }

        $search = $request->input('search');
        if (!empty($search)) {
            $query->where(function ($q) use ($search) {
                $q->where('po_number', 'like', "%{$search}%")
                  ->orWhere('challan_no', 'like', "%{$search}%")
                  ->orWhere('vendor_name', 'like', "%{$search}%")
                  ->orWhereHas('items', function ($sub) use ($search) {
                      $sub->where('item_name', 'like', "%{$search}%")
                          ->orWhere('item_code', 'like', "%{$search}%");
                  });
            });
        }

        // Tally-style ledger is sorted chronologically
        $query->orderBy('po_date', 'asc')->orderBy('id', 'asc');

        $bills = $query->get();

        $totalAmount = (float) $bills->sum('grand_total');
        $billCount = $bills->count();

        // Calculate total items quantity
        $totalItemsQty = 0;
        foreach ($bills as $b) {
            $totalItemsQty += $b->items->sum('ordered_qty');
        }

        // CSV Export handling
        if ($request->query('export') === 'csv') {
            return $this->exportCsv($bills, $selectedVendor, $fromDate, $toDate, $totalAmount);
        }

        if ($request->wantsJson() || $request->ajax()) {
            return response()->json([
                'success' => true,
                'vendor' => $selectedVendor,
                'bills' => $bills,
                'total_amount' => $totalAmount,
                'bill_count' => $billCount,
                'total_items_qty' => $totalItemsQty,
                'from_date' => $fromDate,
                'to_date' => $toDate
            ]);
        }

        $companySettings = CompanySetting::first();

        return view('purchase.ledger', compact(
            'vendors',
            'items',
            'selectedVendor',
            'selectedVendorId',
            'selectedItemId',
            'selectedStatus',
            'bills',
            'totalAmount',
            'billCount',
            'totalItemsQty',
            'fromDate',
            'toDate',
            'periodPreset',
            'search',
            'companySettings'
        ));
    }

    /**
     * Export Tally-Style Purchase Ledger to CSV with Item Details
     */
    protected function exportCsv($bills, $vendor, $fromDate, $toDate, $totalAmount)
    {
        $vendorTitle = $vendor ? $vendor->name : 'All_Suppliers';
        $filename = "Purchase_Ledger_{$vendorTitle}_" . date('Ymd_His') . ".csv";

        $headers = [
            'Content-Type' => 'text/csv; charset=UTF-8',
            'Content-Disposition' => "attachment; filename=\"{$filename}\"",
        ];

        $callback = function () use ($bills, $vendor, $fromDate, $toDate, $totalAmount) {
            $handle = fopen('php://output', 'w');

            // Header info
            fputcsv($handle, ['PURCHASE LEDGER WITH ITEM DETAILS']);
            fputcsv($handle, ['Party / Supplier:', $vendor ? "{$vendor->name} [{$vendor->code}]" : 'All Suppliers']);
            if ($vendor && $vendor->gstin) {
                fputcsv($handle, ['Supplier GSTIN:', $vendor->gstin]);
            }
            fputcsv($handle, ['Reporting Period:', ($fromDate ?: 'Start') . ' to ' . ($toDate ?: 'End')]);
            fputcsv($handle, []);

            // Columns with item details
            fputcsv($handle, [
                'Date',
                'Bill / Challan No',
                'PO Number',
                'Supplier Name',
                'Item Name',
                'Item Code / SKU',
                'Quantity',
                'Unit',
                'Rate (Rs)',
                'Discount (Rs)',
                'Tax %',
                'Tax Amount (Rs)',
                'Item Total (Rs)',
                'Bill Total (Rs)'
            ]);

            foreach ($bills as $bill) {
                $billNo = $bill->challan_no ?: $bill->po_number;
                $dateFormatted = date('d-M-Y', strtotime($bill->po_date));

                if ($bill->items && $bill->items->count() > 0) {
                    foreach ($bill->items as $idx => $item) {
                        fputcsv($handle, [
                            $dateFormatted,
                            $billNo,
                            $bill->po_number,
                            $bill->vendor_name,
                            $item->item_name,
                            $item->item_code ?: '—',
                            format_quantity($item->ordered_qty),
                            $item->unit ?: 'MTR',
                            number_format($item->rate, 2, '.', ''),
                            '0.00',
                            number_format($item->tax_percent, 1, '.', '') . '%',
                            number_format($item->tax_amount, 2, '.', ''),
                            number_format($item->total_amount, 2, '.', ''),
                            $idx === 0 ? number_format($bill->grand_total, 2, '.', '') : ''
                        ]);
                    }
                } else {
                    fputcsv($handle, [
                        $dateFormatted,
                        $billNo,
                        $bill->po_number,
                        $bill->vendor_name,
                        'Fabric Quality',
                        '—',
                        '1',
                        'MTR',
                        number_format($bill->subtotal, 2, '.', ''),
                        '0.00',
                        '5.0%',
                        number_format($bill->tax_total, 2, '.', ''),
                        number_format($bill->grand_total, 2, '.', ''),
                        number_format($bill->grand_total, 2, '.', '')
                    ]);
                }
            }

            fputcsv($handle, []);
            fputcsv($handle, ['TOTAL PURCHASE AMOUNT', '', '', '', '', '', '', '', '', '', '', '', '', number_format($totalAmount, 2, '.', '')]);

            fclose($handle);
        };

        return response()->stream($callback, 200, $headers);
    }
}
