<?php

namespace App\Http\Controllers;

use App\Http\Requests\OrderProcessing\OrderRequest;
use App\Services\LiqPayService;
use App\Services\OrderService;
use App\Services\DeliveryService;
use App\Http\Requests\OrderProcessing\DeliveryRequest;
use App\Models\Orders\Order;
use Illuminate\Support\Facades\Cookie;
use App\Services\CookieService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;

class OrderProcessingController extends Controller
{

    // создание заказа
    public function order(OrderRequest $request)
    {
        try {
            $data = $request->validated();
            $cart = json_decode(Cookie::get('cart', '[]'), true);

            $order = OrderService::createOrder($data, $cart);

            if ($data['payment_method'] == 'card')
            {
                return $this->CardPaymentProcess($order);
            }

            CookieService::clearCookies();

            return redirect()->route('cart.show')->with('success', 'Заказ успешно оформлен!');
        } catch (\Exception $e)
        {
            return redirect()->back()->with('error', $e->getMessage());
        }
    }

    // оплата заказа онлайн
    public function CardPaymentProcess(Order $order)
    {
        try {

            $html = LiqPayService::pay($order->items, $order->id);

            CookieService::clearCookies();

            return view('payment.form', compact('html'));

        } catch (\Exception $e)
        {
            return redirect()->back()->with('error', $e->getMessage());
        }
    }

    // возврат ответа от платежной системы
    public function CardPaymentCallback(Request $request)
    {
        $data = $request->input('data');
        $signature = $request->input('signature');

        Log::info("Ответ от LiqPay: " . json_encode($request->all()));

        $result = LiqPayService::callback($data, $signature);

        return response()->json($result['message'], $result['status']);
    }

    // возвращает возможные отделения доставки нп
    public function delivery(DeliveryRequest $request)
    { 
        $data = $request->validated();
    
        return response()->json(DeliveryService::getDeliveryPoints($data));
    }
}
