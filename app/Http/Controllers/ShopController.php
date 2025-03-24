<?php

namespace App\Http\Controllers;

use App\Models\Shop;

class ShopController extends Controller
{
    public function index()
    {
        $product = Shop::all()->toArray();

        return view('shop.index', compact('product'));
    }

    public function show($id)
    {
        $productId = Shop::findOrFail($id);
        
        return view('products.show', compact('productId'));
    }
}
