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
        $items = Item::latest()->get();

        if ($request->wantsJson() || $request->ajax()) {
            return response()->json($items);
        }

        $stats = [
            'total' => Item::count(),
            'fabricCount' => Item::where('category', 'Fabric')->count(),
            'trimsCount' => Item::where('category', 'Trims')->count(),
            'lowStockCount' => Item::whereRaw('current_stock <= min_stock')->count()
        ];

        return view('masters.items.index', compact('items', 'stats'));
    }

    public function store(Request $request)
    {
        $data = $request->all();
        if (!isset($data['unit_cost']) && isset($data['rate'])) {
            $data['unit_cost'] = $data['rate'];
        }
        if (!isset($data['current_stock']) && isset($data['openingStock'])) {
            $data['current_stock'] = $data['openingStock'];
        }
        if (!isset($data['min_stock']) && isset($data['reorderLevel'])) {
            $data['min_stock'] = $data['reorderLevel'];
        }
        if (!isset($data['hsn_code']) && isset($data['hsn'])) {
            $data['hsn_code'] = $data['hsn'];
        }

        $validated = validator($data, [
            'code' => 'nullable|string|max:100',
            'name' => 'required|string|max:255',
            'type' => 'nullable|string|max:100',
            'category' => 'nullable|string|max:100',
            'brand' => 'nullable|string|max:100',
            'fabric' => 'nullable|string|max:150',
            'color' => 'nullable|string|max:100',
            'size' => 'nullable|string|max:50',
            'unit' => 'nullable|string|max:50',
            'unit_cost' => 'nullable|numeric',
            'current_stock' => 'nullable|numeric',
            'min_stock' => 'nullable|numeric',
            'hsn_code' => 'nullable|string|max:50',
            'location' => 'nullable|string|max:150',
            'status' => 'nullable|string|max:50'
        ])->validate();

        $category = !empty($validated['category']) ? $validated['category'] : 'Fabric';
        $validated['category'] = $category;
        $validated['unit'] = !empty($validated['unit']) ? $validated['unit'] : 'Pieces';
        $validated['unit_cost'] = isset($validated['unit_cost']) ? floatval($validated['unit_cost']) : 0.00;
        $validated['current_stock'] = isset($validated['current_stock']) ? floatval($validated['current_stock']) : 0.00;
        $validated['min_stock'] = isset($validated['min_stock']) ? floatval($validated['min_stock']) : 100.00;
        $validated['status'] = !empty($validated['status']) ? $validated['status'] : 'Active';

        if (empty($validated['code'])) {
            $prefix = strtoupper(substr(preg_replace('/[^A-Za-z0-9]/', '', $category), 0, 3));
            if (empty($prefix)) $prefix = 'ITM';
            $count = Item::count() + 1;
            $code = $prefix . '-' . str_pad($count, 3, '0', STR_PAD_LEFT);
            while (Item::where('code', $code)->exists()) {
                $count++;
                $code = $prefix . '-' . str_pad($count, 3, '0', STR_PAD_LEFT);
            }
            $validated['code'] = $code;
        }

        $item = Item::create($validated);

        if ($request->wantsJson() || $request->ajax()) {
            return response()->json(['success' => true, 'item' => $item]);
        }

        return redirect()->route('masters.items.index')->with('success', "Item {$item->name} registered.");
    }

    public function show(Item $item)
    {
        return response()->json($item);
    }

    public function update(Request $request, Item $item)
    {
        $data = $request->all();
        if (!isset($data['unit_cost']) && isset($data['rate'])) {
            $data['unit_cost'] = $data['rate'];
        }
        if (!isset($data['current_stock']) && isset($data['openingStock'])) {
            $data['current_stock'] = $data['openingStock'];
        }
        if (!isset($data['min_stock']) && isset($data['reorderLevel'])) {
            $data['min_stock'] = $data['reorderLevel'];
        }
        if (!isset($data['hsn_code']) && isset($data['hsn'])) {
            $data['hsn_code'] = $data['hsn'];
        }

        $validated = validator($data, [
            'code' => 'sometimes|nullable|string|max:100',
            'name' => 'sometimes|required|string|max:255',
            'type' => 'nullable|string|max:100',
            'category' => 'sometimes|nullable|string|max:100',
            'brand' => 'nullable|string|max:100',
            'fabric' => 'nullable|string|max:150',
            'color' => 'nullable|string|max:100',
            'size' => 'nullable|string|max:50',
            'unit' => 'sometimes|nullable|string|max:50',
            'unit_cost' => 'sometimes|nullable|numeric',
            'current_stock' => 'sometimes|nullable|numeric',
            'min_stock' => 'sometimes|nullable|numeric',
            'hsn_code' => 'nullable|string|max:50',
            'location' => 'nullable|string|max:150',
            'status' => 'nullable|string|max:50'
        ])->validate();

        $item->update($validated);

        if ($request->wantsJson() || $request->ajax()) {
            return response()->json(['success' => true, 'item' => $item]);
        }

        return redirect()->route('masters.items.index')->with('success', "Item {$item->name} updated.");
    }

    public function destroy(Item $item)
    {
        $name = $item->name;
        $item->delete();

        if (request()->wantsJson() || request()->ajax()) {
            return response()->json(['success' => true, 'message' => "Item {$name} deleted."]);
        }

        return redirect()->route('masters.items.index')->with('success', "Item {$name} deleted.");
    }
}
