<?php

namespace App\Http\Controllers\JobWork;

use App\Http\Controllers\Controller;
use App\Models\JobInward;
use App\Models\JobAssignment;
use App\Models\JobWorker;
use App\Models\Item;
use App\Models\CompanySetting;
use Illuminate\Http\Request;

class JobInwardReportController extends Controller
{
    public function index(Request $request)
    {
        $jobworkers = JobWorker::orderBy('name')->get();
        $items = Item::orderBy('name')->get();

        $workerId = $request->input('worker_id');
        $qcStatus = $request->input('qc_status');
        $fromDate = $request->input('from_date');
        $toDate = $request->input('to_date');
        $search = $request->input('search');
        $preset = $request->input('preset');

        // Preset handling
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

        $query = JobInward::with(['jobAssignment', 'jobWorker'])->latest('inward_date')->latest('id');

        if ($workerId) {
            $query->where('job_worker_id', $workerId);
        }

        if ($qcStatus && $qcStatus !== 'all') {
            $query->where('qc_status', $qcStatus);
        }

        if ($fromDate) {
            $query->whereDate('inward_date', '>=', $fromDate);
        }

        if ($toDate) {
            $query->whereDate('inward_date', '<=', $toDate);
        }

        if ($search) {
            $query->where(function ($q) use ($search) {
                $q->where('inward_number', 'like', "%{$search}%")
                  ->orWhere('job_order_no', 'like', "%{$search}%")
                  ->orWhere('challan_no', 'like', "%{$search}%")
                  ->orWhere('job_worker_name', 'like', "%{$search}%")
                  ->orWhere('lot_number', 'like', "%{$search}%")
                  ->orWhere('style_name', 'like', "%{$search}%");
            });
        }

        $inwards = $query->get();

        // Calculate KPI aggregations
        $totalReceived = 0;
        $totalDefect = 0;
        $totalWastage = 0;
        $totalThans = 0;

        foreach ($inwards as $inw) {
            $totalReceived += (float)$inw->received_qty;
            $totalDefect += (float)$inw->defect_qty;
            $totalWastage += (float)$inw->wastage_returned_meters;
            
            if (!empty($inw->items_list) && is_array($inw->items_list)) {
                foreach ($inw->items_list as $it) {
                    $totalThans += (int)($it['than_count'] ?? (count($it['assigned_thans'] ?? []) ?: 1));
                }
            } else {
                $totalThans += (int)$inw->total_thans ?: 1;
            }
        }

        if ($request->query('export') === 'csv') {
            return $this->exportCsv($inwards, $totalReceived, $totalDefect, $totalWastage);
        }

        if ($request->wantsJson() || $request->ajax()) {
            return response()->json([
                'inwards' => $inwards,
                'total_received' => $totalReceived,
                'total_defect' => $totalDefect,
                'total_wastage' => $totalWastage,
                'total_thans' => $totalThans,
                'total_count' => $inwards->count()
            ]);
        }

        $companySettings = CompanySetting::first();
        $companyName = $companySettings?->company_name ?? 'aarambh';

        return view('jobwork.inward-report.index', compact(
            'inwards',
            'jobworkers',
            'items',
            'workerId',
            'qcStatus',
            'fromDate',
            'toDate',
            'preset',
            'search',
            'totalReceived',
            'totalDefect',
            'totalWastage',
            'totalThans',
            'companySettings',
            'companyName'
        ));
    }

    protected function exportCsv($inwards, $totRec, $totDef, $totWaste)
    {
        $filename = "Job_Inward_Register_" . date('Ymd_His') . ".csv";
        $headers = [
            'Content-Type' => 'text/csv; charset=UTF-8',
            'Content-Disposition' => "attachment; filename=\"{$filename}\"",
            'Pragma' => 'no-cache',
            'Cache-Control' => 'must-revalidate, post-check=0, pre-check=0',
            'Expires' => '0'
        ];

        $callback = function () use ($inwards, $totRec, $totDef, $totWaste) {
            $handle = fopen('php://output', 'w');
            fputcsv($handle, ['JOB WORK INWARD REGISTER REPORT']);
            fputcsv($handle, ['Generated on', date('d-M-Y H:i:s')]);
            fputcsv($handle, []);
            fputcsv($handle, ['S.No.', 'Inward #', 'Receipt Date', 'Job Order #', 'Job Worker', 'Challan #', 'Product', 'Received Qty', 'Defect Qty', 'Wastage (Mtr)', 'QC Status']);

            foreach ($inwards as $idx => $inw) {
                fputcsv($handle, [
                    $idx + 1,
                    $inw->inward_number,
                    date('d-M-Y', strtotime($inw->inward_date)),
                    $inw->job_order_no,
                    $inw->job_worker_name,
                    $inw->challan_no ?: '—',
                    $inw->style_name ?: 'Garment Product',
                    $inw->received_qty,
                    $inw->defect_qty,
                    $inw->wastage_returned_meters,
                    $inw->qc_status ?: 'Passed'
                ]);
            }

            fputcsv($handle, []);
            fputcsv($handle, ['TOTALS', '', '', '', '', '', '', $totRec, $totDef, $totWaste, '']);
            fclose($handle);
        };

        return response()->stream($callback, 200, $headers);
    }
}
