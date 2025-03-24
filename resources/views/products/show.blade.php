@extends('layouts.app')
@section('content')
<div class="container">
    <h1 style="text-align: center;">список товаров</h1>
    <h2 style="text-align: center;">купить</h2>
    <p>товар: {{ $productId['product'] }}</p> 
    <p>количество: {{ $productId['count'] }}</p>
    
    <div style="display: flex; justify-content: space-between; align-items: center;">
    <p>Добавить в корзину</p>
    <p>Оформить заказ</p>
    </div>

</div>
@endsection