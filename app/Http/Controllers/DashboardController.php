<?php

namespace App\Http\Controllers;

use App\Traits\NormalizesRawRows;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

class DashboardController extends Controller
{
    use NormalizesRawRows;

    public function index()
    {
        $orders = $this->normalizeRows(DB::select(
            'SELECT o."ORDER_ID", o."ORDER_DATE", o."TOTAL_AMOUNT", o."STATUS",
                    COUNT(oi."ITEM_ID") AS "ITEM_COUNT"
             FROM "ORDERS" o
             LEFT JOIN "ORDER_ITEMS" oi ON o."ORDER_ID" = oi."ORDER_ID"
             WHERE o."USER_ID" = ?
             GROUP BY o."ORDER_ID", o."ORDER_DATE", o."TOTAL_AMOUNT", o."STATUS"
             ORDER BY o."ORDER_DATE" DESC',
            [Auth::id()]
        ));

        $reviewRows = $this->normalizeRows(DB::select(
            'SELECT o."ORDER_ID", o."ORDER_DATE", o."STATUS",
                    p."PRODUCT_ID", p."NAME" AS "PRODUCT_NAME", p."IMAGE_URL", p."PRICE",
                    oi."QUANTITY" AS "ORDER_QUANTITY",
                    NVL(i."QUANTITY", 0) AS "STOCK_QUANTITY",
                    r."REVIEW_ID", r."RATING" AS "REVIEW_RATING", r."COMMENT" AS "REVIEW_COMMENT",
                    r."IS_APPROVED"
             FROM "ORDERS" o
             JOIN "ORDER_ITEMS" oi ON o."ORDER_ID" = oi."ORDER_ID"
             JOIN "PRODUCTS" p ON oi."PRODUCT_ID" = p."PRODUCT_ID"
             LEFT JOIN "INVENTORY" i ON p."PRODUCT_ID" = i."PRODUCT_ID"
             LEFT JOIN "REVIEWS" r ON r."PRODUCT_ID" = p."PRODUCT_ID" AND r."USER_ID" = ?
             WHERE o."USER_ID" = ? AND o."STATUS" = ?
             ORDER BY o."ORDER_DATE" DESC, o."ORDER_ID" DESC, p."NAME" ASC',
            [Auth::id(), Auth::id(), 'delivered']
        ));

        $reviewOrders = collect($reviewRows)
            ->groupBy('order_id')
            ->map(function ($items) {
                $first = $items->first();

                return (object) [
                    'order_id' => $first->order_id,
                    'order_date' => $first->order_date,
                    'status' => $first->status,
                    'items' => $items->values()->all(),
                ];
            })
            ->values()
            ->all();

        return view('dashboard', compact('orders', 'reviewOrders'));
    }
}