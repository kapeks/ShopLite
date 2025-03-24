<?php

namespace App\Services;

use App\Models\Shop;
use Exception;
use Illuminate\Support\Facades\Log;


/**
 * Cервис отвечает за управление корзиной заказов.
 */
class CartService
{

    public function getCart(array $cart)
    {

        $products = Shop::whereIn('id', array_keys($cart))->get()->keyBy('id');

        Log::info($products);

        $errors = [];

        foreach ($cart as $id => &$item) {
            if (!isset($products[$id])) {
                $errors[] = "Товар {$item['product']} закончился и был удален из корзины.";
                unset($cart[$id]);
                continue;
            }

            $product = $products[$id];

            if ($product->count == 0)
            {
                $errors[] = "Товар {$item['product']} закончился";
                unset($cart[$id]);
            } elseif ($product->count < $item['quantity'])
            {
                $errors[] = "Товар {$item['product']} доступен только в количестве {$product->count} шт.";
                $item['quantity'] = $product->count;
                $item['price'] = $product->count * $product->price;
            }
        }

        $countCart = array_sum(array_column($cart, 'quantity'));


        return [
            'errors' => $errors,
            'cart' => $cart,
            'countCart' => $countCart
        ];
    }

    public function addToCart(int $id, array $cart)
    {
        $product = Shop::findOrFail($id);

        // $product->update(['count' => 4]);


        if (isset($cart[$id])) // Если товар уже есть в корзине, увеличиваем количество
        {
            $cart[$id]['quantity']++; // Добавляем новый товар в корзину
            $cart[$id]['price'] += $product->price;
        } else {
            $cart[$id] = [
                'product' => $product->product,
                'quantity' => 1,
                'price' => $product->price,
            ];
        }

        if ($cart[$id]['quantity'] > $product->count) {
            return null;
        }

        $countCart = array_sum(array_column($cart, 'quantity'));

        return [
            'cart' => $cart,
            'countCart' => $countCart,
        ];
    }

    public function removeFromCart(int $id, array $cart)
    {
        if (!isset($cart[$id])) {
            abort(404);
        }

        unset($cart[$id]);

        $countCart = array_sum(array_column($cart, 'quantity'));

        return [
            'cart' => $cart,
            'countCart' => $countCart,
        ];
    }

    public function updateCart($id, $data, $cart)
    {
        if (!isset($cart[$id])) {
            throw new Exception('Товар не найден в корзине');
        }
        
        $product = Shop::find($id);

        if (!$product) {
            throw new Exception('Товар отсутствует в базе.');
        }

        if ($data['action'] === 'increase')
        {
            if ($cart[$id]['quantity'] < $product->count)
            {
                $cart[$id]['quantity']++;
                $cart[$id]['price'] = $cart[$id]['quantity'] * $product->price;
            } else {
                throw new Exception('Недостаточно товара на складе');
            }
        } elseif ($data['action'] === 'decrease')
        {
            if ($cart[$id]['quantity'] > 1) {
                $cart[$id]['quantity']--;
                $cart[$id]['price'] = $cart[$id]['quantity'] * $product->price;
            } else {
                unset($cart[$id]);
            }
        }
        $countCart = array_sum(array_column($cart, 'quantity'));

        return [
            'cart' => $cart,
            'countCart' => $countCart
        ];

    }
}
