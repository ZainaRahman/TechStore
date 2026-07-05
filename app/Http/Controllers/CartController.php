<?php

namespace App\Http\Controllers;

use App\Traits\NormalizesRawRows;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

class CartController extends Controller
{
    use NormalizesRawRows;

    public function index()
    {
        // JOIN — cart items with product and brand info
        $rows = DB::select(
            'SELECT c."CART_ID", c."QUANTITY" AS "CART_QUANTITY",
                    p."PRODUCT_ID", p."NAME", p."PRICE", p."IMAGE_URL",
                    b."NAME" AS "BRAND_NAME"
             FROM "CART" c
             JOIN "PRODUCTS" p ON c."PRODUCT_ID" = p."PRODUCT_ID"
             JOIN "BRANDS" b ON p."BRAND_ID" = b."BRAND_ID"
             WHERE c."USER_ID" = ?',
            [Auth::id()]
        );

        $cartItems = $this->normalizeRows($rows);

        $total = array_sum(array_map(
            fn($item) => $item->price * $item->cart_quantity,
            $cartItems
        ));

        return view('cart.index', compact('cartItems', 'total'));
    }

    public function add(Request $request, $productId)
    {
        $userId = Auth::id();

        // Check if already in cart
        $existing = DB::selectOne(
            'SELECT * FROM "CART" WHERE "USER_ID" = ? AND "PRODUCT_ID" = ?',
            [$userId, $productId]
        );

        if ($existing) {
            DB::update(
                'UPDATE "CART" SET "QUANTITY" = "QUANTITY" + 1
                 WHERE "USER_ID" = ? AND "PRODUCT_ID" = ?',
                [$userId, $productId]
            );
        } else {
            DB::insert(
                'INSERT INTO "CART" ("USER_ID", "PRODUCT_ID", "QUANTITY") VALUES (?, ?, 1)',
                [$userId, $productId]
            );
        }

        // Fetch product name for the flash message
        $product = DB::selectOne('SELECT "NAME" FROM "PRODUCTS" WHERE "PRODUCT_ID" = ?', [$productId]);

        return back()->with('status', 'Added ' . ($product->NAME ?? 'item') . ' to cart.');
    }

    public function update(Request $request, $cartId)
    {
        $validated = $request->validate([
            'quantity' => ['required', 'integer', 'min:1', 'max:99'],
        ]);

        // Confirm this cart item belongs to the logged-in user
        $item = DB::selectOne(
            'SELECT * FROM "CART" WHERE "CART_ID" = ? AND "USER_ID" = ?',
            [$cartId, Auth::id()]
        );

        if (!$item) {
            abort(403);
        }

        DB::update(
            'UPDATE "CART" SET "QUANTITY" = ? WHERE "CART_ID" = ?',
            [$validated['quantity'], $cartId]
        );

        return back()->with('status', 'Cart updated.');
    }

    public function remove($cartId)
    {
        // Confirm ownership before deleting
        $item = DB::selectOne(
            'SELECT * FROM "CART" WHERE "CART_ID" = ? AND "USER_ID" = ?',
            [$cartId, Auth::id()]
        );

        if (!$item) {
            abort(403);
        }

        DB::delete('DELETE FROM "CART" WHERE "CART_ID" = ?', [$cartId]);

        return back()->with('status', 'Item removed from cart.');
    }
}