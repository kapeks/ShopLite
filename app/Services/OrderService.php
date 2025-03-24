<?php

namespace App\Services;

use Illuminate\Support\Facades\DB;
use App\Models\Orders\Order;
use App\Models\Orders\OrderItem;
use App\Models\Orders\Payment;
use Exception;
use Illuminate\Support\Facades\Log;
use App\Models\Shop;

/**
 * Сервис управляет заказами.
 */
class OrderService
{
    public static function createOrder(array $data, array $cart)
    {
        if (empty($cart))
        {
            throw new Exception();
        }

        DB::beginTransaction();

        try {
            // Создаём заказ
            $order = Order::create([
                'first_name'      => $data['first_name'],
                'last_name'       => $data['last_name'],
                'city'            => $data['city'],
                'region'          => $data['region'],
                'street'          => $data['street'],
                'house'           => $data['house'],
                'apartment'       => $data['apartment'],
                'delivery_method' => $data['delivery_method'],
                'payment_method'  => $data['payment_method'],
                'np_department'  => $data['np_department'],
                'payment_status'  => 'pending'
            ]);

            if (!$order) {
                throw new Exception('произошла ошибка оформления заказа');
            }

            // Добавляем товары в заказ
            foreach ($cart as $id => $value)
            {
                //обновляем количество товара
                $product = Shop::find($id);
                if ($product->count == 0)
                {
                    throw new Exception('товар закончился');
                }
                $product->update(['count' => $product->count - $value['quantity']]);

                Log::info($product);
                OrderItem::create([
                    'order_id'     => $order->id,
                    'product_name' => $value['product'],
                    'quantity'     => $value['quantity'],
                    'price'        => $value['price'],
                ]);
            }

            // Если оплата картой, создаем запись о платеже
            if ($data['payment_method'] == 'card') {
                $totalAmount = array_sum(array_map(fn($item) => $item['price'], $cart));
                Payment::create([
                    'order_id' => $order->id,
                    'status'   => 'pending',
                    'amount'   => $totalAmount,
                ]);
            }
            
            DB::commit();

            return $order;
        } catch (\Exception $e) {
            DB::rollBack();
            Log::error("Ошибка создания заказа: " . $e->getMessage());
            return null;
        }
    }

}