<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Traits\NormalizesRawRows;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

class ReviewController extends Controller
{
    use NormalizesRawRows;

    public function index()
    {
        // JOIN — all reviews with product and customer info
        $rows = DB::select(
            'SELECT r."REVIEW_ID", r."RATING", r."COMMENT", r."IS_APPROVED",
                    p."NAME" AS "PRODUCT_NAME",
                    u."FULL_NAME"
             FROM "REVIEWS" r
             JOIN "PRODUCTS" p ON r."PRODUCT_ID" = p."PRODUCT_ID"
             JOIN "USERS" u ON r."USER_ID" = u."USER_ID"
             ORDER BY r."IS_APPROVED" ASC, r."REVIEW_ID" DESC'
        );

        $reviews = $this->normalizeRows($rows);
        return view('admin.reviews.index', compact('reviews'));
    }

    public function approve($reviewId)
    {
        DB::update(
            'UPDATE "REVIEWS" SET "IS_APPROVED" = 1, "APPROVED_BY" = ? WHERE "REVIEW_ID" = ?',
            [Auth::guard('admin')->id(), $reviewId]
        );

        return back()->with('status', 'Review approved.');
    }

    public function reject($reviewId)
    {
        DB::delete('DELETE FROM "REVIEWS" WHERE "REVIEW_ID" = ?', [$reviewId]);
        return back()->with('status', 'Review rejected and removed.');
    }
}