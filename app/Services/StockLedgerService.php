<?php

namespace App\Services;

use App\Models\Item;
use App\Models\PurchaseOrder;
use App\Models\JobAssignment;
use App\Models\JobInward;
use App\Models\Transaction;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

class StockLedgerService
{
    /**
     * Record stock transactions for a Purchase Order (Purchase Inward)
     */
    public static function syncPurchaseOrderStock(PurchaseOrder $order): void
    {
        // 1. Remove previous transactions for this PO reference to avoid duplicates
        $refNo = $order->challan_no ?: $order->po_number;
        Transaction::where('ledger_type', 'ItemStock')
            ->where('reference_no', $refNo)
            ->where('transaction_type', 'Purchase Inward')
            ->delete();

        // If cancelled, do not add stock
        if (strtolower($order->status) === 'cancelled') {
            self::recalculateAllAffectedItems();
            return;
        }

        $order->loadMissing('items');

        foreach ($order->items as $idx => $pItem) {
            $qty = (float)($pItem->ordered_qty ?? 0) ?: ((float)($pItem->received_qty ?? 0) ?: (float)($pItem->qty ?? 0));
            if ($qty <= 0) continue;

            $item = self::resolveItem($pItem->item_id, $pItem->item_name, $pItem->item_code, $pItem->unit);
            if (!$item) continue;

            $txnNo = 'TXN-PO-' . $order->id . '-' . ($pItem->id ?: $idx) . '-' . substr(md5($order->id . '_' . $idx . '_' . $item->id), 0, 6);

            Transaction::create([
                'transaction_no' => $txnNo,
                'transaction_date' => $order->po_date ?: ($order->purchase_date ?: now()->toDateString()),
                'ledger_type' => 'ItemStock',
                'entity_id' => $item->id,
                'entity_name' => $item->name . ($item->code ? " ({$item->code})" : ''),
                'reference_no' => $refNo,
                'transaction_type' => 'Purchase Inward',
                'debit' => 0.00,
                'credit' => $qty,
                'qty_in' => $qty,
                'qty_out' => 0.00,
                'unit' => $pItem->unit ?: ($item->unit ?: 'Meters'),
                'running_balance' => 0.00,
                'created_by' => Auth::user()?->name ?? 'Admin',
                'description' => "Purchase received from {$order->vendor_name} ({$qty} " . ($pItem->unit ?: 'Meters') . ")"
            ]);

            self::recalculateItemStock($item->id);
        }
    }

    /**
     * Record stock deduction when raw materials are issued for Job Work
     */
    public static function syncJobAssignmentStock(JobAssignment $assignment): void
    {
        $refNo = $assignment->job_order_no;
        Transaction::where('ledger_type', 'ItemStock')
            ->where('reference_no', $refNo)
            ->where('transaction_type', 'Job Work Issue')
            ->delete();

        // Image 1 Requirement 3: Dummy Job Work => stock maintain nahi karna hai
        if (isset($assignment->maintain_stock) && !$assignment->maintain_stock) {
            self::recalculateAllAffectedItems();
            return;
        }

        if (strtolower($assignment->status) === 'cancelled') {
            self::recalculateAllAffectedItems();
            return;
        }

        $assignment->loadMissing('items');

        if ($assignment->items && $assignment->items->count() > 0) {
            foreach ($assignment->items as $idx => $aItem) {
                $rawQty = (float)($aItem->than_meters ?: 0);
                if ($rawQty <= 0) continue;

                $item = self::resolveItem($aItem->raw_item_id, $aItem->raw_item_name, null, 'Meters');
                if (!$item) continue;

                $txnNo = 'TXN-JA-' . $assignment->id . '-' . ($aItem->id ?: $idx) . '-' . substr(md5($assignment->id . '_' . $idx), 0, 6);

                Transaction::create([
                    'transaction_no' => $txnNo,
                    'transaction_date' => $assignment->issue_date ?: now()->toDateString(),
                    'ledger_type' => 'ItemStock',
                    'entity_id' => $item->id,
                    'entity_name' => $item->name . ($item->code ? " ({$item->code})" : ''),
                    'reference_no' => $refNo,
                    'transaction_type' => 'Job Work Issue',
                    'debit' => $rawQty,
                    'credit' => 0.00,
                    'qty_in' => 0.00,
                    'qty_out' => $rawQty,
                    'unit' => $item->unit ?: 'Meters',
                    'running_balance' => 0.00,
                    'created_by' => Auth::user()?->name ?? 'Admin',
                    'description' => "Material issued to {$assignment->job_worker_name} for Lot #{$assignment->lot_number}"
                ]);

                self::recalculateItemStock($item->id);
            }
        }
    }

    /**
     * Record stock addition when finished goods are received via Job Inward
     */
    public static function syncJobInwardStock(JobInward $inward): void
    {
        $refNo = $inward->inward_number;
        Transaction::where('ledger_type', 'ItemStock')
            ->where('reference_no', $refNo)
            ->where('transaction_type', 'Job Inward Receipt')
            ->delete();

        // Image 1 Requirement 3: Dummy Job Work check => stock maintain nahi karna hai
        $assign = JobAssignment::where('job_order_no', $inward->job_order_no)->first();
        if ($assign && isset($assign->maintain_stock) && !$assign->maintain_stock) {
            self::recalculateAllAffectedItems();
            return;
        }

        $items = $inward->items_list;
        $inwardDate = $inward->inward_date ?: now()->toDateString();

        if (!empty($items)) {
            foreach ($items as $idx => $itm) {
                $recQty = (float)($itm['received_qty'] ?? 0);
                if ($recQty <= 0) continue;

                $item = self::resolveItem($itm['item_id'] ?? null, $itm['item_name'] ?? null, $itm['item_code'] ?? null, $itm['unit'] ?? 'Pcs');
                if (!$item) continue;

                $txnNo = 'TXN-INW-' . $inward->id . '-' . $idx . '-' . substr(md5($inward->id . '_' . $idx), 0, 6);

                Transaction::create([
                    'transaction_no' => $txnNo,
                    'transaction_date' => $inwardDate,
                    'ledger_type' => 'ItemStock',
                    'entity_id' => $item->id,
                    'entity_name' => $item->name . ($item->code ? " ({$item->code})" : ''),
                    'reference_no' => $refNo,
                    'transaction_type' => 'Job Inward Receipt',
                    'debit' => 0.00,
                    'credit' => $recQty,
                    'qty_in' => $recQty,
                    'qty_out' => 0.00,
                    'unit' => $itm['unit'] ?? ($item->unit ?: 'Pcs'),
                    'running_balance' => 0.00,
                    'created_by' => Auth::user()?->name ?? 'Admin',
                    'description' => "Received {$recQty} pcs from {$inward->job_worker_name} (Challan #{$inward->challan_no})"
                ]);

                self::recalculateItemStock($item->id);
            }
        } else {
            $recQty = (float)($inward->received_qty ?? 0);
            if ($recQty > 0) {
                $item = self::resolveItem(null, $inward->style_name, null, 'Pcs');
                if ($item) {
                    $txnNo = 'TXN-INW-' . $inward->id . '-0-' . substr(md5($inward->id), 0, 6);

                    Transaction::create([
                        'transaction_no' => $txnNo,
                        'transaction_date' => $inwardDate,
                        'ledger_type' => 'ItemStock',
                        'entity_id' => $item->id,
                        'entity_name' => $item->name . ($item->code ? " ({$item->code})" : ''),
                        'reference_no' => $refNo,
                        'transaction_type' => 'Job Inward Receipt',
                        'debit' => 0.00,
                        'credit' => $recQty,
                        'qty_in' => $recQty,
                        'qty_out' => 0.00,
                        'unit' => $item->unit ?: 'Pcs',
                        'running_balance' => 0.00,
                        'created_by' => Auth::user()?->name ?? 'Admin',
                        'description' => "Received {$recQty} pcs from {$inward->job_worker_name} (Challan #{$inward->challan_no})"
                    ]);

                    self::recalculateItemStock($item->id);
                }
            }
        }
    }

    /**
     * Resolve Item model by ID, code, or name
     */
    public static function resolveItem($itemId, $itemName, $itemCode = null, $unit = 'Meters'): ?Item
    {
        if (!empty($itemId)) {
            $item = Item::find($itemId);
            if ($item) return $item;
        }

        if (!empty($itemCode)) {
            $item = Item::where('code', trim($itemCode))->first();
            if ($item) return $item;
        }

        if (!empty($itemName)) {
            $cleanName = trim($itemName);
            $item = Item::where('name', $cleanName)->first();
            if ($item) return $item;

            // Check if name contains code or exact match case-insensitive
            $item = Item::whereRaw('LOWER(name) = ?', [strtolower($cleanName)])->first();
            if ($item) return $item;

            // Create placeholder item SKU if completely new
            $code = 'ITM-' . strtoupper(substr(preg_replace('/[^A-Za-z0-9]/', '', $cleanName), 0, 4)) . '-' . rand(100, 999);
            return Item::create([
                'name' => $cleanName,
                'code' => $code,
                'category' => 'Fabric',
                'unit' => $unit ?: 'Meters',
                'current_stock' => 0.00,
                'unit_cost' => 0.00,
                'status' => 'Active'
            ]);
        }

        return null;
    }

    /**
     * Recalculate accurate chronological running stock for an item
     */
    public static function recalculateItemStock(int $itemId): float
    {
        $item = Item::find($itemId);
        if (!$item) return 0.0;

        $transactions = Transaction::where('ledger_type', 'ItemStock')
            ->where('entity_id', $itemId)
            ->orderBy('transaction_date', 'asc')
            ->orderBy('id', 'asc')
            ->get();

        $running = 0.0;

        foreach ($transactions as $txn) {
            $qtyIn = (float)$txn->qty_in;
            $qtyOut = (float)$txn->qty_out;

            $running = round($running + $qtyIn - $qtyOut, 2);

            if (abs($txn->running_balance - $running) > 0.001) {
                Transaction::where('id', $txn->id)->update(['running_balance' => $running]);
            }
        }

        $item->update(['current_stock' => $running]);
        return $running;
    }

    /**
     * Recalculate all affected items in system
     */
    public static function recalculateAllAffectedItems(): void
    {
        $itemIds = Transaction::where('ledger_type', 'ItemStock')
            ->whereNotNull('entity_id')
            ->distinct()
            ->pluck('entity_id');

        foreach ($itemIds as $id) {
            self::recalculateItemStock((int)$id);
        }
    }

    /**
     * Sync all existing historical data into Stock Ledger
     */
    public static function syncAllHistoricalStock(): void
    {
        // 1. Sync Purchase Orders
        $orders = PurchaseOrder::with('items')->whereNotIn('status', ['Cancelled'])->get();
        foreach ($orders as $po) {
            self::syncPurchaseOrderStock($po);
        }

        // 2. Sync Job Inwards
        $inwards = JobInward::all();
        foreach ($inwards as $inw) {
            self::syncJobInwardStock($inw);
        }
    }
}
