<?php

namespace App\Http\Controllers;

use App\Traits\NormalizesRawRows;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

class ReviewController extends Controller
{
    use NormalizesRawRows;

    public function store(Request $request, $productId)
    {
        $validated = $request->validate([
            'rating'  => ['required', 'integer', 'min:1', 'max:5'],
            'comment' => ['nullable', 'string', 'max:1000'],
        ]);

        // Check if customer has already reviewed this product
        $existing = DB::selectOne(
            'SELECT * FROM "REVIEWS" WHERE "USER_ID" = ? AND "PRODUCT_ID" = ?',
            [Auth::id(), $productId]
        );

        if ($existing) {
            return back()->withErrors(['rating' => 'You have already reviewed this product.']);
        }

        DB::insert(
            'INSERT INTO "REVIEWS" ("PRODUCT_ID", "USER_ID", "RATING", "COMMENT", "IS_APPROVED")
             VALUES (?, ?, ?, ?, 0)',
            [$productId, Auth::id(), $validated['rating'], $validated['comment'] ?? null]
        );

        return back()->with('status', 'Review submitted and awaiting approval.');
    }
}
