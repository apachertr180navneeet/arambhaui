<?php

namespace App\Http\Controllers\Production;

use App\Http\Controllers\Controller;
use App\Models\QualityCheck;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class QualityCheckController extends Controller
{
    public function index(Request $request)
    {
        if ($request->wantsJson() || $request->ajax()) {
            return response()->json(QualityCheck::latest()->get());
        }

        return view('dashboard', [
            'user' => Auth::user(),
            'module' => 'production',
            'submodule' => 'qc'
        ]);
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'order_no' => 'required|string',
            'lot_number' => 'required|string',
            'style_name' => 'required|string',
            'inspection_date' => 'required|date',
            'inspector_name' => 'required|string',
            'total_inspected' => 'required|integer|min:1',
            'passed_qty' => 'required|integer|min:0',
            'minor_defects' => 'nullable|integer',
            'major_defects' => 'nullable|integer',
            'remarks' => 'nullable|string'
        ]);

        $count = QualityCheck::count() + 1;
        $qcBatchNo = 'QC-2026-' . str_pad($count, 3, '0', STR_PAD_LEFT);

        $totalDefects = ($validated['minor_defects'] ?? 0) + ($validated['major_defects'] ?? 0);
        $defectPct = ($totalDefects / $validated['total_inspected']) * 100;

        $validated['qc_batch_no'] = $qcBatchNo;
        $validated['defect_percent'] = round($defectPct, 2);
        $validated['status'] = $defectPct > 5.0 ? 'Rework Required' : 'Passed';

        $qc = QualityCheck::create($validated);

        if ($request->wantsJson() || $request->ajax()) {
            return response()->json(['success' => true, 'qc' => $qc]);
        }

        return redirect()->route('production.qc.index')->with('success', "QC Record {$qcBatchNo} logged.");
    }
}
