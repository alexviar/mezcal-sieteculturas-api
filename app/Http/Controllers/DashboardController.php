<?php

namespace App\Http\Controllers;

use App\Models\Product;
use App\Models\Purchase;
use Illuminate\Http\Request;
use Illuminate\Support\Carbon;

class DashboardController
{
    public function index(Request $request)
    {
        $now = now();
        $currentMonth = $now->month;
        $currentYear = $now->year;

        $products = Product::where('status', true)->count();
        $productsInStock = Product::where('status', true)->sum("stock");

        $purchaseCount =  Purchase::whereMonth('created_at', $currentMonth)
            ->whereYear('created_at', $currentYear)
            ->count();

        $monthName = $now->monthName;

        return response()->json([
            'message' => 'Datos obtenidos exitosamente',
            'total_products' => $products,
            'total_stock' => $productsInStock,
            'purchases_per_month' => $purchaseCount,
            'month' => $monthName,
        ], 200);
    }
}
