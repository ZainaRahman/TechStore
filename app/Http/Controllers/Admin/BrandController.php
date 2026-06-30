<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Traits\NormalizesRawRows;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class BrandController extends Controller
{
    use NormalizesRawRows;

    public function index()
    {
        $brands = $this->normalizeRows(DB::select('SELECT * FROM "BRANDS" ORDER BY "NAME"'));
        return view('admin.brands.index', compact('brands'));
    }

    public function create()
    {
        return view('admin.brands.create');
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'name'        => ['required', 'string', 'max:100'],
            'country'     => ['nullable', 'string', 'max:50'],
            'logo_url'    => ['nullable', 'string', 'max:255'],
            'description' => ['nullable', 'string', 'max:500'],
        ]);

        DB::insert(
            'INSERT INTO "BRANDS" ("NAME", "COUNTRY", "LOGO_URL", "DESCRIPTION") VALUES (?, ?, ?, ?)',
            [
                $validated['name'],
                $validated['country'] ?? null,
                $validated['logo_url'] ?? null,
                $validated['description'] ?? null,
            ]
        );

        return redirect()->route('admin.brands.index')->with('status', 'Brand created successfully.');
    }

    public function edit($id)
    {
        $row = DB::selectOne('SELECT * FROM "BRANDS" WHERE "BRAND_ID" = ?', [$id]);

        if (!$row) {
            abort(404);
        }

        $brand = $this->normalizeRow($row);
        return view('admin.brands.edit', compact('brand'));
    }

    public function update(Request $request, $id)
    {
        $validated = $request->validate([
            'name'        => ['required', 'string', 'max:100'],
            'country'     => ['nullable', 'string', 'max:50'],
            'logo_url'    => ['nullable', 'string', 'max:255'],
            'description' => ['nullable', 'string', 'max:500'],
        ]);

        DB::update(
            'UPDATE "BRANDS" SET "NAME" = ?, "COUNTRY" = ?, "LOGO_URL" = ?, "DESCRIPTION" = ? WHERE "BRAND_ID" = ?',
            [
                $validated['name'],
                $validated['country'] ?? null,
                $validated['logo_url'] ?? null,
                $validated['description'] ?? null,
                $id,
            ]
        );

        return redirect()->route('admin.brands.index')->with('status', 'Brand updated successfully.');
    }

    public function destroy($id)
    {
        DB::delete('DELETE FROM "BRANDS" WHERE "BRAND_ID" = ?', [$id]);
        return redirect()->route('admin.brands.index')->with('status', 'Brand deleted.');
    }
}