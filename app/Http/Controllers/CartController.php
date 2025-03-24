<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Cookie;
use App\Services\CartService;
use App\Services\CookieService;

class CartController extends Controller
{
    protected $cartService;

    public function __construct(CartService $cartService)
    {
        $this->cartService = $cartService;
    }

    public function addToCart(Request $request, $id)
    {
        $cart = json_decode(Cookie::get('cart', '[]'), true);

        $cartData = $this->cartService->addToCart($id, $cart);

        if (!$cartData)
        {
            return redirect()->back()->with('success', 'Больше товара нет!');
        }

        CookieService::saveCookies($cartData);

        Log::info($cart);

        return redirect()->back()->with('success', 'Товар добавлен в корзину!');
    }

    public function getCart()
    {
        $cart = json_decode(Cookie::get('cart', '[]'), true);

        $result = $this->cartService->getCart($cart);

        CookieService::saveCookies($result);

        return view('cart', ['errors' => $result['errors']]);
    }

    public function removeFromCart($id)
    {
        $cart = json_decode(Cookie::get('cart', '[]'), true);

        $cartData = $this->cartService->removeFromCart($id, $cart);

        CookieService::saveCookies($cartData);

        return redirect()->back()->with('success', 'Товар удален из корзины!');
    }

    public function clearCart()
    {
        CookieService::clearCookies();

        return redirect()->back()->with('success', 'Корзина очищена!');
    }

    public function updateCart(Request $request, $id)
    {
        $cart = json_decode(Cookie::get('cart', '[]'), true);

        $data = $request->all();

        try {

            $result = $this->cartService->updateCart($id, $data, $cart);

            CookieService::saveCookies($result);

            return redirect()->route('cart.show')->with('success', 'Корзина обновлена!');
        
        } catch (\Exception $e)
        {
            return redirect()->back()->with('error', $e->getMessage());
        }
    }

}
