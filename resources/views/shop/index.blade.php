@extends('layouts.app')

@section('content')
<div class="container mt-4">
    <h1 class="text-center mb-4">Список товаров</h1>

    @if (session('success')) <!--сообщение об успешнос добавлении в корзину -->
    <div class="alert alert-success alert-dismissible fade show" role="alert">
        {{ session('success') }}
        <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
    </div>
    @endif

    <div class="row">
        @foreach ($product as $item)
        <div class="col-md-4 mb-4">
            <div class="card h-100 shadow-sm">
                <img src="{{ asset('images/default-product.jpg') }}" class="card-img-top" alt="Товар">
                <div class="card-body">
                    <h5 class="card-title">{{ $item['product'] }}</h5>
                    <p class="card-text">Цена: <strong>{{ $item['price'] ?? 'Не указана' }} $.</strong></p>
                    <p class="card-text">Количество: <strong>{{ $item['count']  }}</strong></p>

                    <form action="{{ route('cart.add', $item['id']) }}" method="POST">
                        @csrf
                        <button type="submit" class="btn btn-primary w-100">Добавить в корзину {{$cart[$item['id']]['quantity'] ?? '' }}</button>
                    </form>
                </div>
            </div>
        </div>
        @endforeach
    </div>
</div>
@endsection