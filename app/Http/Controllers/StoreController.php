<?php

namespace App\Http\Controllers;

use App\Models\Product;
use Illuminate\Http\Request;

class StoreController
{
    function index(Request $request)
    {
        $products = Product::where('status', true)
            ->paginate($request->get('per_page'));

        return $products;
    }
}
