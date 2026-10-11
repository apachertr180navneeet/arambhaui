<?php

namespace App\Http\Controllers\Purchase;

use App\Http\Controllers\Controller;
use App\Models\PurchaseOrder;
use App\Models\Vendor;
use App\Models\CompanySetting;
use Illuminate\Http\Request;
use Carbon\Carbon;

class PurchaseLedgerController extends Controller
{
    /**
     * Display Tally-Style Purchase Ledger Report
     */
    public function index(Request $request)
    {
        $vendors = Vendor::whereRaw('LOWER(status) = ?', ['active'])->orderBy('name')->get();
        if ($vendors->isEmpty()) {
            $vendors = Vendor::orderBy('name')->get();
        }

        $selectedVendorId = $request->input('vendor_id');
        $selectedVendor = null;
        if (!empty($selectedVendorId)) {
            $selectedVendor = Vendor::find($selectedVendorId);
        }

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

        $query = PurchaseOrder::query();

        // Exclude cancelled bills
        $query->where('status', '!=', 'Cancelled');

        if ($selectedVendor) {
            $query->where(function ($q) use ($selectedVendor) {
                $q->where('vendor_id', $selectedVendor->id)
                  ->orWhere('vendor_name', $selectedVendor->name);
            });
        }

        if (!empty($fromDate)) {
            $query->whereDate('po_date', '>=', $fromDate);
        }
        if (!empty($toDate)) {
            $query->whereDate('po_date', '<=', $toDate);
        }

        // Tally-style ledger is sorted chronologically
        $query->orderBy('po_date', 'asc')->orderBy('id', 'asc');

        $bills = $query->get();

        $totalAmount = (float) $bills->sum('grand_total');
        $billCount = $bills->count();

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
                'from_date' => $fromDate,
                'to_date' => $toDate
            ]);
        }

        $companySettings = CompanySetting::first();

        return view('purchase.ledger', compact(
            'vendors',
            'selectedVendor',
            'selectedVendorId',
            'bills',
            'totalAmount',
            'billCount',
            'fromDate',
            'toDate',
            'periodPreset',
            'companySettings'
        ));
    }

    /**
     * Export Tally-Style Purchase Ledger to CSV
     */
    protected function exportCsv($bills, $vendor, $fromDate, $toDate, $totalAmount)
    {
        $vendorTitle = $vendor ? $vendor->name : 'All_Suppliers';
        $filename = "Purchase_Ledger_{$vendorTitle}_" . date('Ymd_His') . ".csv";

        $headers = [
            'Content-Type' => 'text/csv',
            'Content-Disposition' => "attachment; filename=\"{$filename}\"",
        ];

        $callback = function () use ($bills, $vendor, $fromDate, $toDate, $totalAmount) {
            $handle = fopen('php://output', 'w');

            // Header info
            fputcsv($handle, ['PURCHASE LEDGER (Tally-Style Accounting Report)']);
            fputcsv($handle, ['Party / Supplier:', $vendor ? "{$vendor->name} [{$vendor->code}]" : 'All Suppliers']);
            if ($vendor && $vendor->gstin) {
                fputcsv($handle, ['Supplier GSTIN:', $vendor->gstin]);
            }
            fputcsv($handle, ['Reporting Period:', ($fromDate ?: 'Start') . ' to ' . ($toDate ?: 'End')]);
            fputcsv($handle, []);

            // 3 Standard Tally Columns
            fputcsv($handle, ['Date', 'Bill Number', 'Amount']);

            foreach ($bills as $bill) {
                $billNo = $bill->challan_no ?: $bill->po_number;
                $dateFormatted = date('d-M-Y', strtotime($bill->po_date));
                fputcsv($handle, [
                    $dateFormatted,
                    $billNo,
                    number_format($bill->grand_total, 2, '.', '')
                ]);
            }

            fputcsv($handle, []);
            fputcsv($handle, ['TOTAL PURCHASE AMOUNT', '', number_format($totalAmount, 2, '.', '')]);

            fclose($handle);
        };

        return response()->stream($callback, 200, $headers);
    }
}
