<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Traits\NormalizesRawRows;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class ProductController extends Controller
{
    use NormalizesRawRows;

    public function index()
    {
        // JOIN across PRODUCTS, BRANDS, INVENTORY for the admin list view
        $rows = DB::select(
            'SELECT p."PRODUCT_ID", p."NAME", p."DESCRIPTION", p."PRICE", p."BRAND_ID", p."IMAGE_URL",
                    b."NAME" AS "BRAND_NAME",
                    i."QUANTITY", i."REORDER_LEVEL"
             FROM "PRODUCTS" p
             JOIN "BRANDS" b ON p."BRAND_ID" = b."BRAND_ID"
             LEFT JOIN "INVENTORY" i ON p."PRODUCT_ID" = i."PRODUCT_ID"
             ORDER BY p."NAME"'
        );

        $products = $this->normalizeRows($rows);
        return view('admin.products.index', compact('products'));
    }

    public function create()
    {
        $brands = $this->normalizeRows(DB::select('SELECT * FROM "BRANDS" ORDER BY "NAME"'));
        return view('admin.products.create', compact('brands'));
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'name'          => ['required', 'string', 'max:200'],
            'description'   => ['nullable', 'string', 'max:1000'],
            'price'         => ['required', 'numeric', 'min:0'],
            'brand_id'      => ['required', 'integer'],
            'image_url'     => ['nullable', 'string', 'max:255'],
            'quantity'      => ['required', 'integer', 'min:0'],
            'reorder_level' => ['required', 'integer', 'min:0'],
        ]);

        DB::beginTransaction();

        try {
            DB::insert(
                'INSERT INTO "PRODUCTS" ("NAME", "DESCRIPTION", "PRICE", "BRAND_ID", "IMAGE_URL") VALUES (?, ?, ?, ?, ?)',
                [
                    $validated['name'],
                    $validated['description'] ?? null,
                    $validated['price'],
                    $validated['brand_id'],
                    $validated['image_url'] ?? null,
                ]
            );

            // Oracle IDENTITY columns don't return lastInsertId() via PDO the same way MySQL does,
            // so we fetch the row we just inserted by its unique name + most recent ID.
            $newProduct = DB::selectOne(
            'SELECT * FROM (SELECT "PRODUCT_ID" FROM "PRODUCTS" WHERE "NAME" = ? ORDER BY "PRODUCT_ID" DESC) WHERE ROWNUM = 1',
             [$validated['name']]
           );
            DB::insert(
                'INSERT INTO "INVENTORY" ("PRODUCT_ID", "QUANTITY", "REORDER_LEVEL") VALUES (?, ?, ?)',
                [$newProduct->product_id, $validated['quantity'], $validated['reorder_level']]
            );

            DB::commit();
        } catch (\Exception $e) {
            DB::rollBack();
            return back()->withErrors(['name' => 'Could not create product: ' . $e->getMessage()])->withInput();
        }

        return redirect()->route('admin.products.index')->with('status', 'Product created successfully.');
    }

    public function edit($id)
    {
        $row = DB::selectOne('SELECT * FROM "PRODUCTS" WHERE "PRODUCT_ID" = ?', [$id]);

        if (!$row) {
            abort(404);
        }

        $product = $this->normalizeRow($row);

        $inventoryRow = DB::selectOne('SELECT * FROM "INVENTORY" WHERE "PRODUCT_ID" = ?', [$id]);
        $product->quantity = $inventoryRow->quantity ?? 0;
        $product->reorder_level = $inventoryRow->reorder_level ?? 5;

        $brands = $this->normalizeRows(DB::select('SELECT * FROM "BRANDS" ORDER BY "NAME"'));

        return view('admin.products.edit', compact('product', 'brands'));
    }

    public function update(Request $request, $id)
    {
        $validated = $request->validate([
            'name'          => ['required', 'string', 'max:200'],
            'description'   => ['nullable', 'string', 'max:1000'],
            'price'         => ['required', 'numeric', 'min:0'],
            'brand_id'      => ['required', 'integer'],
            'image_url'     => ['nullable', 'string', 'max:255'],
            'quantity'      => ['required', 'integer', 'min:0'],
            'reorder_level' => ['required', 'integer', 'min:0'],
        ]);

        DB::beginTransaction();

        try {
            DB::update(
                'UPDATE "PRODUCTS" SET "NAME" = ?, "DESCRIPTION" = ?, "PRICE" = ?, "BRAND_ID" = ?, "IMAGE_URL" = ? WHERE "PRODUCT_ID" = ?',
                [
                    $validated['name'],
                    $validated['description'] ?? null,
                    $validated['price'],
                    $validated['brand_id'],
                    $validated['image_url'] ?? null,
                    $id,
                ]
            );

            $existingInventory = DB::selectOne('SELECT * FROM "INVENTORY" WHERE "PRODUCT_ID" = ?', [$id]);

            if ($existingInventory) {
                DB::update(
                    'UPDATE "INVENTORY" SET "QUANTITY" = ?, "REORDER_LEVEL" = ? WHERE "PRODUCT_ID" = ?',
                    [$validated['quantity'], $validated['reorder_level'], $id]
                );
            } else {
                DB::insert(
                    'INSERT INTO "INVENTORY" ("PRODUCT_ID", "QUANTITY", "REORDER_LEVEL") VALUES (?, ?, ?)',
                    [$id, $validated['quantity'], $validated['reorder_level']]
                );
            }

            DB::commit();
        } catch (\Exception $e) {
            DB::rollBack();
            return back()->withErrors(['name' => 'Could not update product: ' . $e->getMessage()])->withInput();
        }

        return redirect()->route('admin.products.index')->with('status', 'Product updated successfully.');
    }

    public function destroy($id)
    {
        DB::beginTransaction();

        try {
            DB::delete('DELETE FROM "INVENTORY" WHERE "PRODUCT_ID" = ?', [$id]);
            DB::delete('DELETE FROM "PRODUCTS" WHERE "PRODUCT_ID" = ?', [$id]);
            DB::commit();
        } catch (\Exception $e) {
            DB::rollBack();
            return back()->withErrors(['name' => 'Could not delete product: ' . $e->getMessage()]);
        }

        return redirect()->route('admin.products.index')->with('status', 'Product deleted.');
    }
}