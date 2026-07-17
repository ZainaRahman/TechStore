<?php

namespace App\Http\Controllers;

use App\Traits\NormalizesRawRows;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

class OrderController extends Controller
{
    use NormalizesRawRows;

    public function checkout()
    {
        $rows = DB::select(
            'SELECT c."CART_ID", c."QUANTITY" AS "CART_QUANTITY",
                    p."PRODUCT_ID", p."NAME", p."PRICE"
             FROM "CART" c
             JOIN "PRODUCTS" p ON c."PRODUCT_ID" = p."PRODUCT_ID"
             WHERE c."USER_ID" = ?',
            [Auth::id()]
        );

        $cartItems = $this->normalizeRows($rows);

        if (empty($cartItems)) {
            return redirect()->route('cart.index')->with('status', 'Your cart is empty.');
        }

        $total = array_sum(array_map(
            fn($item) => $item->price * $item->cart_quantity,
            $cartItems
        ));

        return view('cart.checkout', compact('cartItems', 'total'));
    }

    public function placeOrder(Request $request)
    {
        $validated = $request->validate([
            'shipping_address' => ['required', 'string', 'max:255'],
        ]);

        $userId = Auth::id();

        $rows = DB::select(
            'SELECT c."CART_ID", c."QUANTITY" AS "CART_QUANTITY",
                    p."PRODUCT_ID", p."NAME", p."PRICE"
             FROM "CART" c
             JOIN "PRODUCTS" p ON c."PRODUCT_ID" = p."PRODUCT_ID"
             WHERE c."USER_ID" = ?',
            [$userId]
        );

        $cartItems = $this->normalizeRows($rows);

        if (empty($cartItems)) {
            return redirect()->route('cart.index')->with('status', 'Your cart is empty.');
        }

        $total = array_sum(array_map(
            fn($item) => $item->price * $item->cart_quantity,
            $cartItems
        ));

        DB::beginTransaction();

        try {
            // INSERT order
            DB::insert(
                'INSERT INTO "ORDERS" ("USER_ID", "ORDER_DATE", "TOTAL_AMOUNT", "STATUS", "SHIPPING_ADDRESS")
                 VALUES (?, SYSDATE, ?, \'pending\', ?)',
                [$userId, $total, $validated['shipping_address']]
            );

            // Get the order we just created using Oracle 11g ROWNUM syntax
            $newOrder = DB::selectOne(
                'SELECT * FROM (
                    SELECT * FROM "ORDERS"
                    WHERE "USER_ID" = ?
                    ORDER BY "ORDER_ID" DESC
                ) WHERE ROWNUM = 1',
                [$userId]
            );

            // INSERT order items — the TRG_DECREMENT_INVENTORY trigger
            // fires automatically on each INSERT here, reducing stock in Oracle
            foreach ($cartItems as $item) {
                DB::insert(
                    'INSERT INTO "ORDER_ITEMS" ("ORDER_ID", "PRODUCT_ID", "QUANTITY", "UNIT_PRICE", "SUBTOTAL")
                     VALUES (?, ?, ?, ?, ?)',
                    [
                        $newOrder->order_id,
                        $item->product_id,
                        $item->cart_quantity,
                        $item->price,
                        $item->price * $item->cart_quantity,
                    ]
                );
            }

            // Clear the cart after order placed
            DB::delete('DELETE FROM "CART" WHERE "USER_ID" = ?', [$userId]);

            DB::commit();

        } catch (\Exception $e) {
            DB::rollBack();
            return back()->withErrors(['error' => 'Order failed: ' . $e->getMessage()]);
        }

        return redirect()->route('orders.confirmation', $newOrder->order_id)
            ->with('status', 'Order placed successfully.');
    }

    public function confirmation($orderId)
    {
        // Verify this order belongs to the logged-in user
        $order = DB::selectOne(
            'SELECT * FROM "ORDERS" WHERE "ORDER_ID" = ? AND "USER_ID" = ?',
            [$orderId, Auth::id()]
        );

        if (!$order) {
            abort(403);
        }

        $order = $this->normalizeRow($order);

        // JOIN to get items with product names
        $items = $this->normalizeRows(DB::select(
            'SELECT oi."ITEM_ID", oi."QUANTITY", oi."UNIT_PRICE", oi."SUBTOTAL",
                    p."NAME"
             FROM "ORDER_ITEMS" oi
             JOIN "PRODUCTS" p ON oi."PRODUCT_ID" = p."PRODUCT_ID"
             WHERE oi."ORDER_ID" = ?',
            [$orderId]
        ));

        return view('cart.confirmation', compact('order', 'items'));
    }

    public function myOrders()
{
    $userId = Auth::id();

    // All orders for the orders summary table
    $orders = $this->normalizeRows(DB::select(
        'SELECT
            o."ORDER_ID",
            o."ORDER_DATE",
            o."STATUS",
            o."TOTAL_AMOUNT",
            COUNT(oi."ITEM_ID") AS "ITEM_COUNT"
         FROM "ORDERS" o
         LEFT JOIN "ORDER_ITEMS" oi ON o."ORDER_ID" = oi."ORDER_ID"
         WHERE o."USER_ID" = ?
         GROUP BY o."ORDER_ID", o."ORDER_DATE", o."STATUS", o."TOTAL_AMOUNT"
         ORDER BY o."ORDER_DATE" DESC',
        [$userId]
    ));

    // Only delivered orders go to the review section
    $deliveredOrders = $this->normalizeRows(DB::select(
        'SELECT "ORDER_ID", "ORDER_DATE"
         FROM "ORDERS"
         WHERE "USER_ID" = ? AND "STATUS" = \'delivered\'
         ORDER BY "ORDER_DATE" DESC',
        [$userId]
    ));

    // For each delivered order, load its items with product info,
    // stock level, and whether this user has already reviewed each product
    foreach ($deliveredOrders as $order) {
        $order->items = $this->normalizeRows(DB::select(
            'SELECT
                oi."ITEM_ID",
                oi."QUANTITY"                AS "ORDER_QUANTITY",
                p."PRODUCT_ID",
                p."NAME"                     AS "PRODUCT_NAME",
                p."IMAGE_URL",
                NVL(i."QUANTITY", 0)         AS "STOCK_QUANTITY",
                r."REVIEW_ID",
                r."RATING"                   AS "REVIEW_RATING",
                r."COMMENT"                  AS "REVIEW_COMMENT"
             FROM "ORDER_ITEMS" oi
             JOIN "PRODUCTS" p    ON oi."PRODUCT_ID" = p."PRODUCT_ID"
             LEFT JOIN "INVENTORY" i ON p."PRODUCT_ID" = i."PRODUCT_ID"
             LEFT JOIN "REVIEWS" r   ON r."PRODUCT_ID" = oi."PRODUCT_ID"
                                    AND r."USER_ID" = ?
             WHERE oi."ORDER_ID" = ?',
            [$userId, $order->order_id]
        ));
    }

    $reviewOrders = $deliveredOrders;

    return view('orders.index', compact('orders', 'reviewOrders'));
}
}