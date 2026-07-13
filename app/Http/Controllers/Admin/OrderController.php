<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Traits\NormalizesRawRows;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class OrderController extends Controller
{
    use NormalizesRawRows;

    public function index()
    {
        // JOIN — all orders with customer name and item count
        $rows = DB::select(
            'SELECT o."ORDER_ID", o."ORDER_DATE", o."TOTAL_AMOUNT", o."STATUS",
                    o."SHIPPING_ADDRESS", u."FULL_NAME",
                    COUNT(oi."ITEM_ID") AS "ITEM_COUNT"
             FROM "ORDERS" o
             JOIN "USERS" u ON o."USER_ID" = u."USER_ID"
             LEFT JOIN "ORDER_ITEMS" oi ON o."ORDER_ID" = oi."ORDER_ID"
             GROUP BY o."ORDER_ID", o."ORDER_DATE", o."TOTAL_AMOUNT",
                      o."STATUS", o."SHIPPING_ADDRESS", u."FULL_NAME"
             ORDER BY o."ORDER_DATE" DESC'
        );

        $orders = $this->normalizeRows($rows);
        return view('admin.orders.index', compact('orders'));
    }

    public function show($orderId)
    {
        $order = $this->normalizeRow(
            DB::selectOne(
                'SELECT o.*, u."FULL_NAME", u."EMAIL", u."PHONE"
                 FROM "ORDERS" o
                 JOIN "USERS" u ON o."USER_ID" = u."USER_ID"
                 WHERE o."ORDER_ID" = ?',
                [$orderId]
            )
        );

        if (!$order) abort(404);

        $items = $this->normalizeRows(DB::select(
            'SELECT oi."ITEM_ID", oi."QUANTITY", oi."UNIT_PRICE", oi."SUBTOTAL",
                    p."NAME", p."IMAGE_URL"
             FROM "ORDER_ITEMS" oi
             JOIN "PRODUCTS" p ON oi."PRODUCT_ID" = p."PRODUCT_ID"
             WHERE oi."ORDER_ID" = ?',
            [$orderId]
        ));

        $payment = $this->normalizeRow(
            DB::selectOne(
                'SELECT * FROM "PAYMENTS" WHERE "ORDER_ID" = ?',
                [$orderId]
            )
        );

        return view('admin.orders.show', compact('order', 'items', 'payment'));
    }

    public function updateStatus(Request $request, $orderId)
    {
        $validated = $request->validate([
            'status' => ['required', 'in:pending,confirmed,shipped,delivered,cancelled'],
        ]);

        // Call the PL/SQL stored procedure — this is database-enforced status validation
        DB::statement(
            'BEGIN PROC_UPDATE_ORDER_STATUS(:order_id, :status); END;',
            [
                'order_id' => $orderId,
                'status' => $validated['status'],
            ]
        );

        return back()->with('status', 'Order status updated to ' . ucfirst($validated['status']) . '.');
    }
}