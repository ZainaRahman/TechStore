<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Traits\NormalizesRawRows;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class PaymentController extends Controller
{
    use NormalizesRawRows;

    public function store(Request $request, $orderId)
    {
        $validated = $request->validate([
            'method'         => ['required', 'in:card,mobile_banking,emi,cash'],
            'transaction_id' => ['nullable', 'string', 'max:100'],
            'amount'         => ['required', 'numeric', 'min:0'],
        ]);

        // Check if payment already exists for this order
        $existing = DB::selectOne(
            'SELECT * FROM "PAYMENTS" WHERE "ORDER_ID" = ?',
            [$orderId]
        );

        if ($existing) {
            return back()->withErrors(['error' => 'Payment already recorded for this order.']);
        }

        DB::insert(
            'INSERT INTO "PAYMENTS" ("ORDER_ID", "METHOD", "STATUS", "TRANSACTION_ID", "AMOUNT")
             VALUES (?, ?, \'pending\', ?, ?)',
            [
                $orderId,
                $validated['method'],
                $validated['transaction_id'] ?? null,
                $validated['amount'],
            ]
        );

        return back()->with('status', 'Payment recorded successfully.');
    }

    public function markPaid($paymentId)
    {
        // UPDATE triggers TRG_PAYMENT_PAID_AT which sets PAID_AT = SYSDATE automatically
        DB::update(
            'UPDATE "PAYMENTS" SET "STATUS" = \'completed\' WHERE "PAYMENT_ID" = ?',
            [$paymentId]
        );

        return back()->with('status', 'Payment marked as completed.');
    }

    public function index()
    {
        // JOIN — all payments with order and customer info
        $rows = DB::select(
            'SELECT pay."PAYMENT_ID", pay."METHOD", pay."STATUS",
                    pay."TRANSACTION_ID", pay."AMOUNT", pay."PAID_AT",
                    o."ORDER_ID", o."STATUS" AS "ORDER_STATUS",
                    u."FULL_NAME"
             FROM "PAYMENTS" pay
             JOIN "ORDERS" o ON pay."ORDER_ID" = o."ORDER_ID"
             JOIN "USERS" u ON o."USER_ID" = u."USER_ID"
             ORDER BY pay."PAYMENT_ID" DESC'
        );

        $payments = $this->normalizeRows($rows);
        return view('admin.payments.index', compact('payments'));
    }
}