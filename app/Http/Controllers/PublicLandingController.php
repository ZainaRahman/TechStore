<?php

namespace App\Http\Controllers;

use App\Traits\NormalizesRawRows;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class PublicLandingController extends Controller
{
    use NormalizesRawRows;

    public function index(Request $request)
    {
        $filters = $request->only(['q', 'brand_id', 'stock', 'min_price', 'max_price', 'sort']);

        // Brands for the filter dropdown
        $brands = $this->normalizeRows(
            DB::select('SELECT "BRAND_ID", "NAME" FROM "BRANDS" ORDER BY "NAME" ASC')
        );

        // Base query — products with brand, stock, and average approved rating
        $sql = '
            SELECT
                p."PRODUCT_ID",
                p."NAME",
                p."DESCRIPTION",
                p."PRICE",
                p."IMAGE_URL",
                b."NAME"                           AS "BRAND_NAME",
                NVL(i."QUANTITY", 0)               AS "QUANTITY",
                ROUND(NVL(AVG(r."RATING"), 0), 1)  AS "AVG_RATING",
                COUNT(r."REVIEW_ID")               AS "REVIEW_COUNT"
            FROM "PRODUCTS" p
            LEFT JOIN "BRANDS" b    ON p."BRAND_ID"   = b."BRAND_ID"
            LEFT JOIN "INVENTORY" i ON p."PRODUCT_ID" = i."PRODUCT_ID"
            LEFT JOIN "REVIEWS" r   ON p."PRODUCT_ID" = r."PRODUCT_ID"
                                   AND r."IS_APPROVED" = 1
        ';

        $conditions = [];
        $bindings   = [];

        // Keyword search — matches product name, description, or brand name
        if (!empty($filters['q'])) {
            $conditions[] = '(
                UPPER(p."NAME")        LIKE ? OR
                UPPER(p."DESCRIPTION") LIKE ? OR
                UPPER(b."NAME")        LIKE ?
            )';
            $term       = '%' . strtoupper($filters['q']) . '%';
            $bindings[] = $term;
            $bindings[] = $term;
            $bindings[] = $term;
        }

        // Filter by brand
        if (!empty($filters['brand_id'])) {
            $conditions[] = 'p."BRAND_ID" = ?';
            $bindings[]   = $filters['brand_id'];
        }

        // Filter by stock availability
        if (!empty($filters['stock'])) {
            if ($filters['stock'] === 'in_stock') {
                $conditions[] = 'NVL(i."QUANTITY", 0) > 0';
            } elseif ($filters['stock'] === 'out_of_stock') {
                $conditions[] = 'NVL(i."QUANTITY", 0) <= 0';
            }
        }

        // Price range filters
        if ($request->filled('min_price')) {
            $conditions[] = 'p."PRICE" >= ?';
            $bindings[]   = $filters['min_price'];
        }

        if ($request->filled('max_price')) {
            $conditions[] = 'p."PRICE" <= ?';
            $bindings[]   = $filters['max_price'];
        }

        if ($conditions) {
            $sql .= ' WHERE ' . implode(' AND ', $conditions);
        }

        // GROUP BY required because of AVG/COUNT aggregates
        $sql .= '
            GROUP BY
                p."PRODUCT_ID", p."NAME", p."DESCRIPTION",
                p."PRICE", p."IMAGE_URL", b."NAME", i."QUANTITY"
        ';

        // Sorting
        $sql .= match ($filters['sort'] ?? 'newest') {
            'price_low'  => ' ORDER BY p."PRICE" ASC,  p."PRODUCT_ID" DESC',
            'price_high' => ' ORDER BY p."PRICE" DESC, p."PRODUCT_ID" DESC',
            'name_az'    => ' ORDER BY p."NAME" ASC',
            'top_rated'  => ' ORDER BY "AVG_RATING" DESC, p."PRODUCT_ID" DESC',
            default      => ' ORDER BY p."PRODUCT_ID" DESC',
        };

        $products = $this->normalizeRows(DB::select($sql, $bindings));

        return view('landing', compact('products', 'brands', 'filters'));
    }
}