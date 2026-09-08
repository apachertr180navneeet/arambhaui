<?php

namespace App\Http\Controllers\Masters;

use App\Http\Controllers\Controller;
use App\Models\Color;
use App\Models\Size;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class SizeColorController extends Controller
{
    public function index(Request $request)
    {
        if ($request->wantsJson() || $request->ajax()) {
            return response()->json([
                'sizes' => Size::orderBy('sort_order')->get(),
                'colors' => Color::all()
            ]);
        }

        return view('dashboard', [
            'user' => Auth::user(),
            'module' => 'masters',
            'submodule' => 'sizes'
        ]);
    }

    public function storeSize(Request $request)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:50',
            'sort_order' => 'nullable|integer'
        ]);

        $size = Size::create($validated);
        return response()->json(['success' => true, 'size' => $size]);
    }

    public function storeColor(Request $request)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:50',
            'hex_code' => 'required|string|max:10'
        ]);

        $color = Color::create($validated);
        return response()->json(['success' => true, 'color' => $color]);
    }
}
