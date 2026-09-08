<?php

namespace App\Http\Controllers\Masters;

use App\Http\Controllers\Controller;
use App\Models\Item;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class ItemController extends Controller
{
    public function index(Request $request)
    {
        if ($request->wantsJson() || $request->ajax()) {
            return response()->json(Item::latest()->get());
        }

        return view('dashboard', [
            'user' => Auth::user(),
            'module' => 'masters',
            'submodule' => 'items'
        ]);
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'category' => 'required|string',
            'unit' => 'required|string',
            'unit_cost' => 'required|numeric',
            'current_stock' => 'nullable|numeric',
            'min_stock' => 'nullable|numeric',
            'hsn_code' => 'nullable|string',
            'location' => 'nullable|string'
        ]);

        $prefix = strtoupper(substr($validated['category'], 0, 3));
        $count = Item::where('category', $validated['category'])->count() + 1;
        $validated['code'] = $prefix . '-' . str_pad($count, 3, '0', STR_PAD_LEFT);
        $item = Item::create($validated);

        if ($request->wantsJson() || $request->ajax()) {
            return response()->json(['success' => true, 'item' => $item]);
        }

        return redirect()->route('masters.items.index')->with('success', 'Item master registered.');
    }

    public function show(Item $item)
    {
        return response()->json($item);
    }

    public function update(Request $request, Item $item)
    {
        $validated = $request->validate([
            'name' => 'sometimes|required|string|max:255',
            'unit_cost' => 'sometimes|numeric',
            'current_stock' => 'sometimes|numeric',
            'min_stock' => 'sometimes|numeric',
            'location' => 'nullable|string',
            'status' => 'nullable|string'
        ]);

        $item->update($validated);
        return response()->json(['success' => true, 'item' => $item]);
    }

    public function destroy(Item $item)
    {
        $item->delete();
        return response()->json(['success' => true, 'message' => 'Item deleted.']);
    }
}
