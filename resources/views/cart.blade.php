@extends('layouts.app')

@section('content')
<div class="container mt-4">
    <h1 class="text-center">Корзина</h1>

    @if (session('success'))
    <div class="alert alert-success">{{ session('success') }}</div>
    @endif
    @if (session('error'))
    <div class="alert alert-danger">{{ session('error') }}</div>
    @endif

    @if (empty($cart))
    <p class="text-center">Корзина пуста</p>
    @else
    <table class="table">
        <thead>
            <tr>
                <th>Товар</th>

                <th>Количество</th>
                <th>Цена</th>
                <th>Действие</th>
            </tr>
        </thead>
        <tbody>
            @foreach ($cart as $id => $item)
            <tr>
                <td>{{ $item['product'] }}</td>
                <td>
                    <form action="{{ route('cart.update', $id) }}" method="POST" class="d-inline">
                        @csrf
                        @method('PATCH')
                        <button type="submit" name="action" value="decrease" class="btn btn-sm btn-warning">-</button>
                        <span class="mx-2">{{ $item['quantity'] }}</span>
                        <button type="submit" name="action" value="increase" class="btn btn-sm btn-success">+</button>
                    </form>
                </td>
                <td>{{ $item['price'] }}</td>
                <td>
                    <form action="{{ route('cart.remove', $id) }}" method="POST">
                        @csrf
                        <button type="submit" class="btn btn-danger btn-sm">Удалить</button>
                    </form>
                </td>
            </tr>
            @endforeach
        </tbody>
    </table>

    @if (!empty($errors))
    <div class="alert alert-danger">
        <ul>
            @foreach ($errors as $error)
            <li>{{ $error }}</li>
            @endforeach
        </ul>
    </div>
    @else
    <a href="{{ url('/shipping') }}">Оформить заказ</a>
    @endif

    <form action="{{ route('cart.clear') }}" method="POST">
        @csrf
        <button type="submit" class="btn btn-warning w-100">Очистить корзину</button>
    </form>
    @endif
</div>
@endsection