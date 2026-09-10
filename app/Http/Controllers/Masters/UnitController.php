<?php

namespace App\Http\Controllers\Masters;

use App\Http\Controllers\Controller;
use App\Models\Unit;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class UnitController extends Controller
{
    /**
     * Display a listing of Units.
     */
    public function index(Request $request)
    {
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

        $units = $query->orderByRaw('parent_id IS NOT NULL, parent_id, id')->get();

        if ($request->wantsJson() || $request->ajax()) {
            return response()->json($units);
        }

        $parentUnits = Unit::whereNull('parent_id')->get();
        $stats = [
            'total' => Unit::count(),
            'baseUnits' => Unit::whereNull('parent_id')->count(),
            'derivedUnits' => Unit::whereNotNull('parent_id')->count()
        ];

        return view('masters.units.index', compact('units', 'parentUnits', 'stats'));
    }

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

        if (empty($validated['parent_id'])) {
            $validated['parent_id'] = null;
            $validated['conversion_factor'] = null;
        }

        $unit = Unit::create($validated);
        $unit->load('parent');

        if ($request->wantsJson() || $request->ajax()) {
            return response()->json([
                'success' => true,
                'message' => 'Unit created successfully!',
                'unit' => $unit
            ]);
        }

        return redirect()->route('masters.units.index')->with('success', "Unit {$unit->name} created.");
    }

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

        if ($request->wantsJson() || $request->ajax()) {
            return response()->json([
                'success' => true,
                'message' => 'Unit updated successfully!',
                'unit' => $unit
            ]);
        }

        return redirect()->route('masters.units.index')->with('success', "Unit {$unit->name} updated.");
    }

    public function destroy(Unit $unit)
    {
        $name = $unit->name;
        if ($unit->subUnits()->count() > 0) {
            $unit->subUnits()->update(['parent_id' => null, 'conversion_factor' => null]);
        }

        $unit->delete();

        if (request()->wantsJson() || request()->ajax()) {
            return response()->json([
                'success' => true,
                'message' => 'Unit deleted successfully!'
            ]);
        }

        return redirect()->route('masters.units.index')->with('success', "Unit {$name} deleted.");
    }
}
