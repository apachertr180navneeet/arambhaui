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
                  ->orWhere('symbol', 'like', "%{$search}%")
                  ->orWhere('description', 'like', "%{$search}%");
            });
        }

        if ($request->has('status') && !empty($request->status)) {
            $query->where('status', $request->status);
        }

        if ($request->has('type') && !empty($request->type)) {
            if ($request->type === 'base') {
                $query->whereNull('parent_id');
            } elseif ($request->type === 'derived') {
                $query->whereNotNull('parent_id');
            }
        }

        $units = $query->orderByRaw('parent_id IS NOT NULL, parent_id, id')->get();

        if ($request->wantsJson() || $request->ajax()) {
            return response()->json($units);
        }

        $parentUnits = Unit::whereNull('parent_id')->get();
        $stats = $this->getStats();

        return view('masters.units.index', compact('units', 'parentUnits', 'stats'));
    }

    private function getStats()
    {
        return [
            'total' => Unit::count(),
            'baseUnits' => Unit::whereNull('parent_id')->count(),
            'derivedUnits' => Unit::whereNotNull('parent_id')->count(),
            'active' => Unit::whereRaw('LOWER(status) = ?', ['active'])->count()
        ];
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
            'status' => 'nullable|string'
        ]);

        $validated['status'] = !empty($validated['status']) ? ucfirst(strtolower($validated['status'])) : 'Active';
        $validated['decimal_places'] = isset($validated['decimal_places']) ? intval($validated['decimal_places']) : 2;
        $name = trim($validated['name']);
        $validated['code'] = !empty($validated['code']) ? strtoupper(trim($validated['code'])) : strtoupper(substr(preg_replace('/[^A-Za-z0-9]/', '', $name), 0, 8));

        if (empty($validated['parent_id'])) {
            $validated['parent_id'] = null;
            $validated['conversion_factor'] = null;
        }

        $unit = Unit::create($validated);
        $unit->load('parent');

        if ($request->wantsJson() || $request->ajax()) {
            return response()->json([
                'success' => true,
                'unit' => $unit,
                'stats' => $this->getStats(),
                'parentUnits' => Unit::whereNull('parent_id')->get(),
                'message' => "Unit {$unit->name} created successfully."
            ]);
        }

        return redirect()->route('masters.units.index')->with('success', "Unit {$unit->name} created successfully.");
    }

    public function show(Unit $unit)
    {
        return response()->json([
            'success' => true,
            'unit' => $unit->load(['parent', 'subUnits'])
        ]);
    }

    public function update(Request $request, Unit $unit)
    {
        $validated = $request->validate([
            'name' => 'sometimes|required|string|max:100',
            'code' => 'nullable|string|max:20',
            'parent_id' => 'nullable|exists:units,id|different:' . $unit->id,
            'conversion_factor' => 'nullable|numeric|min:0.0001',
            'symbol' => 'nullable|string|max:20',
            'decimal_places' => 'nullable|integer|min:0|max:4',
            'description' => 'nullable|string',
            'status' => 'nullable|string'
        ]);

        if (isset($validated['name'])) {
            $name = trim($validated['name']);
            if (empty($validated['code'])) {
                $validated['code'] = strtoupper(substr(preg_replace('/[^A-Za-z0-9]/', '', $name), 0, 8));
            } else {
                $validated['code'] = strtoupper(trim($validated['code']));
            }
        }

        if (isset($validated['status'])) {
            $validated['status'] = ucfirst(strtolower($validated['status']));
        }

        if (array_key_exists('parent_id', $validated) && empty($validated['parent_id'])) {
            $validated['parent_id'] = null;
            $validated['conversion_factor'] = null;
        }

        $unit->update($validated);
        $unit->load('parent');

        if ($request->wantsJson() || $request->ajax()) {
            return response()->json([
                'success' => true,
                'unit' => $unit,
                'stats' => $this->getStats(),
                'parentUnits' => Unit::whereNull('parent_id')->get(),
                'message' => "Unit {$unit->name} updated successfully."
            ]);
        }

        return redirect()->route('masters.units.index')->with('success', "Unit {$unit->name} updated successfully.");
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
                'stats' => $this->getStats(),
                'parentUnits' => Unit::whereNull('parent_id')->get(),
                'message' => "Unit {$name} deleted successfully."
            ]);
        }

        return redirect()->route('masters.units.index')->with('success', "Unit {$name} deleted successfully.");
    }
}
