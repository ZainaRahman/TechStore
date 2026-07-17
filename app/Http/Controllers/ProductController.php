<?php

namespace App\Http\Controllers;

use App\Traits\NormalizesRawRows;
use Illuminate\Support\Facades\DB;

class ProductController extends Controller
{
    use NormalizesRawRows;

    public function show($productId)
    {
        $row = DB::selectOne(
            'SELECT p."PRODUCT_ID", p."NAME", p."DESCRIPTION", p."PRICE", p."IMAGE_URL",
                    b."NAME" AS "BRAND_NAME",
                    i."QUANTITY", i."REORDER_LEVEL"
             FROM "PRODUCTS" p
             LEFT JOIN "BRANDS" b ON p."BRAND_ID" = b."BRAND_ID"
             LEFT JOIN "INVENTORY" i ON p."PRODUCT_ID" = i."PRODUCT_ID"
             WHERE p."PRODUCT_ID" = ?',
            [$productId]
        );

        if (!$row) {
            abort(404);
        }

        $product = $this->normalizeRow($row);

        return view('products.show', compact('product'));
    }
}