<?php

namespace App\Http\Controllers\Production;

use App\Http\Controllers\Controller;
use App\Models\LotTracking;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class LotTrackingController extends Controller
{
    public function index(Request $request, $lot = null)
    {
        if ($request->wantsJson() || $request->ajax()) {
            if ($lot) {
                return response()->json(LotTracking::where('lot_number', $lot)->first());
            }
            return response()->json(LotTracking::latest()->get());
        }

        return view('dashboard', [
            'user' => Auth::user(),
            'module' => 'production',
            'submodule' => 'tracking'
        ]);
    }
}
