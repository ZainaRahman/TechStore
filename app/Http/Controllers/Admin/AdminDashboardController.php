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
            'total_orders'    => 0, // wired up in Feature 4
            'pending_reviews' => 0, // wired up in Feature 6
        ];

        return view('admin.dashboard', compact('stats'));
    }
}