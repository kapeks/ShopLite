<?php

namespace App\Services;

use Illuminate\Support\Facades\Cookie;

class CookieService
{
    public static function clearCookies()
    {
        Cookie::queue(Cookie::forget('cart'));
        Cookie::queue(Cookie::forget('countCart'));
    }

    public static function saveCookies(array $data)
    {
        // Сохраняем корзину в куки на 7 дней
        Cookie::queue('cart', json_encode($data['cart']), 60 * 24 * 7);
        Cookie::queue('countCart', $data['countCart'], 60 * 24 * 7);
    }
}
