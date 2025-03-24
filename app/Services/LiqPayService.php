<?php

namespace App\Services;

use Illuminate\Support\Facades\Log;
use App\Models\Orders\Payment;
use App\Models\Orders\Order;

/**
 * Сервис для онлайн оплаты
 */
class LiqPayService
{
    public static function pay($data, $orderId)
    {
        // $price = array_sum($data);
        $price = 0;
        foreach ($data as $value) {
            $price += $value['price'];
        }
        Log::info($price);

        $public_key = env('LIQPAY_PUBLIC_KEY');
        $private_key = env('LIQPAY_PRIVATE_KEY');

        $liqpay = new \LiqPay($public_key, $private_key);

        $params = [
            'action'   => 'pay',
            'amount'   => "$price", // Сумма платежа
            'currency' => 'UAH', // Валюта
            'description' => 'Оплата заказа',
            'order_id' => $orderId,
            'version'  => '3',
            'sandbox'  => 1, // 1 - тестовый режим, 0 - боевой
            'server_url' => route('liqpay.callback'), // URL для ответа сервера
            'result_url' => route('cart.show') // Куда перенаправлять после оплаты
        ];

        $html = $liqpay->cnb_form($params);

        if (empty($html)) {
            Log::error('Ошибка оплаты картой');
            throw new \Exception('оплата картой недоступна');
        }

        return $html;
    }

    public static function callback($data, $signature)
    {
        $liqpay = new \LiqPay(env('LIQPAY_PUBLIC_KEY'), env('LIQPAY_PRIVATE_KEY'));

        // Проверяем подпись с помощью библиотеки
        $generated_signature = $liqpay->cnb_signature($data);

        if ($signature !== $generated_signature) {
            Log::error("Ошибка верификации платежа! Возможная подделка данных.");
            return ['message' => 'Ошибка верификации платежа', 'status' => 400];
        }

        // Декодируем ответ LiqPay
        $decoded_data = json_decode(base64_decode($data), true);

        $order_id = $decoded_data['order_id'];
        $payment_id = $decoded_data['payment_id'];
        $status = $decoded_data['status'];

        $order = Order::where('id', $order_id)->first();
        $payment = Payment::where('order_id', $order_id)->first();

        if (!$order || !$payment)
        {
            Log::error("Заказ с ID {$order_id} не найден!");
            return ['message' => 'Некорректные данные', 'status' => 400];
        }


        // Проверяем статус платежа
        if ($status === 'success') {
            $order->update(['status' => 'paid']);
            $payment->update(['status' => 'success', 'transaction_id' => $payment_id]);
            Log::info('Оплата прошла успешно!');
            return ['message' => 'Оплата прошла успешно', 'status' => 200];
        } else {
            $order->update(['status' => 'failed']);
            $payment->update(['status' => 'failed', 'transaction_id' => $payment_id]);
            Log::error('Ошибка оплаты: ' . $decoded_data['status']);
            return ['message' => 'Ошибка оплаты', 'status' => 400];
        }
    }
}
