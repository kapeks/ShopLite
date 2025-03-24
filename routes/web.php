<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\ShopController;
use App\Http\Controllers\CartController;
use App\Http\Controllers\OrderProcessingController;
use App\Http\Controllers\PaymentController;
use Illuminate\Support\Facades\Log;

/*
|--------------------------------------------------------------------------
| Web Routes
|--------------------------------------------------------------------------
|
| Here is where you can register web routes for your application. These
| routes are loaded by the RouteServiceProvider and all of them will
| be assigned to the "web" middleware group. Make something great!
|
*/

Route::get('/', [ShopController::class, 'index'])->name('shop.index');
Route::get('/product/{id}', [ShopController::class, 'show'])->name('product.show');

Route::get('/cart', [CartController::class, 'getCart'])->name('cart.show'); //вывод содержимого корзины
Route::post('/cart/add/{id}', [CartController::class, 'addToCart'])->name('cart.add');//добавление в корзину
Route::post('/cart/remove/{id}', [CartController::class, 'removeFromCart'])->name('cart.remove');//удаление позиции из корзины
Route::post('/cart/clear', [CartController::class, 'clearCart'])->name('cart.clear');//очистка всей корзины
Route::patch('/cart/update/{id}', [CartController::class, 'updateCart'])->name('cart.update');


Route::get('/delivery', [OrderProcessingController::class, 'delivery'])->name('delivery');//доставка
Route::post('/order', [OrderProcessingController::class, 'order'])->name('order');//ордер на заказ
Route::post('/liqpay/callback', [OrderProcessingController::class, 'CardPaymentCallback'])->name('liqpay.callback');//ответ от платежной системы
Route::view('/shipping', 'order_processing');



