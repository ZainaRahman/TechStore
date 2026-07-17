<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\DB;

class AdminDashboardController extends Controller
{
    public function index()
    {
        $stats = [
            'total_customers' => DB::selectOne('SELECT COUNT(*) AS "CNT" FROM "USERS"')->cnt,
            'total_products'  => DB::selectOne('SELECT COUNT(*) AS "CNT" FROM "PRODUCTS"')->cnt,
            'total_orders'    => DB::selectOne('SELECT COUNT(*) AS "CNT" FROM "ORDERS"')->cnt,
            'pending_reviews' => DB::selectOne('SELECT COUNT(*) AS "CNT" FROM "REVIEWS" WHERE "IS_APPROVED" = 0')->cnt,
            'pending_orders'  => DB::selectOne('SELECT COUNT(*) AS "CNT" FROM "ORDERS" WHERE "STATUS" = \'pending\'')->cnt,
            'low_stock'       => DB::selectOne('SELECT COUNT(*) AS "CNT" FROM "INVENTORY" WHERE "QUANTITY" < "REORDER_LEVEL"')->cnt,
        ];

        // Top 5 selling products — JOIN with GROUP BY
        $topProducts = DB::select(
            'SELECT p."NAME", SUM(oi."QUANTITY") AS "TOTAL_SOLD",
                    SUM(oi."SUBTOTAL") AS "TOTAL_REVENUE"
             FROM "ORDER_ITEMS" oi
             JOIN "PRODUCTS" p ON oi."PRODUCT_ID" = p."PRODUCT_ID"
             GROUP BY p."NAME"
             ORDER BY "TOTAL_SOLD" DESC'
        );

        // Recent 5 orders
        $recentOrders = DB::select(
            'SELECT * FROM (
                SELECT o."ORDER_ID", o."ORDER_DATE", o."STATUS",
                       o."TOTAL_AMOUNT", u."FULL_NAME"
                FROM "ORDERS" o
                JOIN "USERS" u ON o."USER_ID" = u."USER_ID"
                ORDER BY o."ORDER_DATE" DESC
             ) WHERE ROWNUM <= 5'
        );

        return view('admin.dashboard', compact('stats', 'topProducts', 'recentOrders'));
    }
}