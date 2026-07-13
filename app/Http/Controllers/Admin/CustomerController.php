<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Traits\NormalizesRawRows;
use Illuminate\Support\Facades\DB;

class CustomerController extends Controller
{
    use NormalizesRawRows;

    public function index()
    {
        // JOIN — customers with their order count and total spend
        $rows = DB::select(
            'SELECT u."USER_ID", u."FULL_NAME", u."EMAIL", u."PHONE", u."CITY",
                    COUNT(o."ORDER_ID") AS "TOTAL_ORDERS",
                    NVL(SUM(o."TOTAL_AMOUNT"), 0) AS "TOTAL_SPENT"
             FROM "USERS" u
             LEFT JOIN "ORDERS" o ON u."USER_ID" = o."USER_ID"
             GROUP BY u."USER_ID", u."FULL_NAME", u."EMAIL", u."PHONE", u."CITY"
             ORDER BY "TOTAL_SPENT" DESC'
        );

        $customers = $this->normalizeRows($rows);
        return view('admin.customers.index', compact('customers'));
    }

    public function show($userId)
    {
        $customer = $this->normalizeRow(
            DB::selectOne(
                'SELECT * FROM "USERS" WHERE "USER_ID" = ?',
                [$userId]
            )
        );

        if (!$customer) abort(404);

        $orders = $this->normalizeRows(DB::select(
            'SELECT * FROM "ORDERS" WHERE "USER_ID" = ? ORDER BY "ORDER_DATE" DESC',
            [$userId]
        ));

        return view('admin.customers.show', compact('customer', 'orders'));
    }
}