<?php

namespace App\Http\Controllers\Masters;

use App\Http\Controllers\Controller;
use App\Models\Unit;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class UnitController extends Controller
{
    /**
     * Display a listing of Units (or return JSON).
     */
    public function index(Request $request)
    {
        if ($request->wantsJson() || $request->ajax()) {
            $query = Unit::with('parent');

            if ($request->has('search') && !empty($request->search)) {
                $search = $request->search;
                $query->where(function ($q) use ($search) {
                    $q->where('name', 'like', "%{$search}%")
                      ->orWhere('code', 'like', "%{$search}%")
                      ->orWhere('symbol', 'like', "%{$search}%");
                });
            }

            if ($request->has('status') && !empty($request->status)) {
                $query->where('status', $request->status);
            }

            return response()->json($query->orderByRaw('parent_id IS NOT NULL, parent_id, id')->get());
        }

        return view('dashboard', [
            'user' => Auth::user(),
            'module' => 'masters',
            'submodule' => 'units'
        ]);
    }

    /**
     * Store a newly created Unit.
     */
    public function store(Request $request)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:100',
            'code' => 'nullable|string|max:20',
            'parent_id' => 'nullable|exists:units,id',
            'conversion_factor' => 'nullable|numeric|min:0.0001',
            'symbol' => 'nullable|string|max:20',
            'decimal_places' => 'nullable|integer|min:0|max:4',
            'description' => 'nullable|string',
            'status' => 'nullable|in:Active,Inactive'
        ]);

        $validated['status'] = $validated['status'] ?? 'Active';
        $validated['decimal_places'] = $validated['decimal_places'] ?? 2;
        $name = trim($validated['name']);
        $validated['code'] = !empty($validated['code']) ? strtoupper(trim($validated['code'])) : strtoupper(substr($name, 0, 10));

        // If parent_id is empty/0, set to null
        if (empty($validated['parent_id'])) {
            $validated['parent_id'] = null;
            $validated['conversion_factor'] = null;
        }

        $unit = Unit::create($validated);
        $unit->load('parent');

        return response()->json([
            'success' => true,
            'message' => 'Unit created successfully!',
            'unit' => $unit
        ]);
    }

    /**
     * Update the specified Unit.
     */
    public function update(Request $request, Unit $unit)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:100',
            'code' => 'nullable|string|max:20',
            'parent_id' => 'nullable|exists:units,id|different:' . $unit->id,
            'conversion_factor' => 'nullable|numeric|min:0.0001',
            'symbol' => 'nullable|string|max:20',
            'decimal_places' => 'nullable|integer|min:0|max:4',
            'description' => 'nullable|string',
            'status' => 'required|in:Active,Inactive'
        ]);

        $name = trim($validated['name']);
        $validated['code'] = !empty($validated['code']) ? strtoupper(trim($validated['code'])) : strtoupper(substr($name, 0, 10));

        if (empty($validated['parent_id'])) {
            $validated['parent_id'] = null;
            $validated['conversion_factor'] = null;
        }

        $unit->update($validated);
        $unit->load('parent');

        return response()->json([
            'success' => true,
            'message' => 'Unit updated successfully!',
            'unit' => $unit
        ]);
    }

    /**
     * Remove the specified Unit.
     */
    public function destroy(Unit $unit)
    {
        // Check if there are sub-units
        if ($unit->subUnits()->count() > 0) {
            $unit->subUnits()->update(['parent_id' => null, 'conversion_factor' => null]);
        }

        $unit->delete();

        return response()->json([
            'success' => true,
            'message' => 'Unit deleted successfully!'
        ]);
    }
}
