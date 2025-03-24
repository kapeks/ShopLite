@extends('layouts.app')

@section('content')
<div class="container mt-4">
    <h1 class="text-center mb-4">Оформление заказа</h1>

    @if (empty($cart))
    <p class="text-center">Добавьте товар в корзину для оформления заказа!</p>
    @else
    <div class="row">
        <!-- Левая колонка: Данные для заказа -->
        <div class="col-md-6">
            <div class="card mb-4">
                <div class="card-header">
                    <h5 class="card-title mb-0">Данные для заказа</h5>
                </div>
                <div class="card-body">

                    @if (session('error'))
                    <div class="alert alert-danger">{{ session('error') }}</div>
                    @endif

                    @if ($errors->any())
                    <div class="alert alert-danger">
                        <ul>
                            @foreach ($errors->all() as $error)
                            <li>{{ $error }}</li>
                            @endforeach
                        </ul>
                    </div>
                    @endif

                    <form action="{{ route('order') }}" method="POST">
                        @csrf


                        <!-- Имя и фамилия -->
                        <div class="mb-3">
                            <label for="firstName" class="form-label">Имя</label>
                            <input type="text" class="form-control" id="firstName" name="first_name" value="{{ old('first_name') }}" required>
                        </div>
                        <div class="mb-3">
                            <label for="lastName" class="form-label">Фамилия</label>
                            <input type="text" class="form-control" id="lastName" name="last_name" value="{{ old('last_name') }}" required>
                        </div>

                        <!-- Способ доставки -->
                        <div class="mb-3">
                            <label class="form-label">Способ доставки</label>
                            <div>
                                <div class="form-check">
                                    <input class="form-check-input" type="radio" name="delivery_method" id="courier" value="courier" checked>
                                    <label class="form-check-label" for="courier">Курьером</label>
                                </div>
                                <div class="form-check">
                                    <input class="form-check-input" type="radio" name="delivery_method" id="pickup" value="pickup">
                                    <label class="form-check-label" for="pickup">В отделение Новой Почты</label>
                                </div>
                            </div>
                        </div>

                        <!-- Поля для курьерской доставки -->
                        <div id="courierFields">
                            <div class="mb-3">
                                <label for="city" class="form-label">Город/Поселок</label>
                                <input type="text" class="form-control" id="city" name="city" value="{{ old('city') }}">
                            </div>
                            <div class="mb-3">
                                <label for="region" class="form-label">Область</label>
                                <input type="text" class="form-control" id="region" name="region" value="{{ old('region') }}">
                            </div>
                            <div class="mb-3">
                                <label for="street" class="form-label">Улица</label>
                                <input type="text" class="form-control" id="street" name="street" value="{{ old('street') }}">
                            </div>
                            <div class="mb-3">
                                <label for="house" class="form-label">Дом</label>
                                <input type="text" class="form-control" id="house" name="house" value="{{ old('house') }}">
                            </div>
                            <div class="mb-3">
                                <label for="apartment" class="form-label">Квартира</label>
                                <input type="text" class="form-control" id="apartment" name="apartment" value="{{ old('apartment') }}">
                            </div>
                        </div>

                        <!-- Поля для доставки в отделение Новой Почты -->
                        <div id="pickupFields" style="display: none;">
                            <div class="mb-3">
                                <label for="np_city" class="form-label">Город</label>
                                <input type="text" class="form-control" id="np_city" name="np_city">
                                <div id="cityResults" class="list-group mt-2"></div> <!-- Результаты поиска городов -->
                            </div>
                            <!-- Поле для отделения (выпадающий список) -->
                            <div class="mb-3">
                                <label for="np_department" class="form-label">Отделение</label>
                                <select class="form-control" id="np_department" name="np_department">
                                    <option value="">Выберите отделение</option>
                                    <!-- Опции будут добавлены динамически -->
                                </select>
                            </div>
                        </div>

                        <!-- Способ оплаты -->
                        <div class="mb-3">
                            <label class="form-label">Способ оплаты</label>
                            <div>
                                <div class="form-check">
                                    <input class="form-check-input" type="radio" name="payment_method" id="cash" value="cash" checked>
                                    <label class="form-check-label" for="cash">Наличными при получении</label>
                                </div>
                                <div class="form-check">
                                    <input class="form-check-input" type="radio" name="payment_method" id="card" value="card">
                                    <label class="form-check-label" for="card">Картой</label>
                                </div>
                            </div>
                        </div>

                        <!-- Кнопка оформления заказа -->
                        <button type="submit" class="btn btn-primary w-100">Оформить заказ</button>
                    </form>
                </div>
            </div>
        </div>

        <!-- Правая колонка: Товары в корзине -->
        <div class="col-md-6">
            <div class="card mb-4">
                <div class="card-header">
                    <h5 class="card-title mb-0">Товары в корзине</h5>
                </div>
                <div class="card-body">
                    @foreach ($cart as $id => $item)
                    <div class="d-flex justify-content-between align-items-center mb-3">
                        <div>
                            <h6 class="mb-0">{{ $item['product'] }}</h6>
                            <small class="text-muted">Количество: {{ $item['quantity'] }}</small>
                        </div>
                        <form action="{{ route('cart.remove', $id) }}" method="POST">
                            @csrf
                            <button type="submit" class="btn btn-link text-danger p-0">Удалить</button>
                        </form>
                    </div>
                    @endforeach
                </div>
            </div>
        </div>
    </div>
    @endif
</div>
<script src="https://cdn.jsdelivr.net/npm/axios/dist/axios.min.js"></script>
<script>
    document.addEventListener('DOMContentLoaded', function() {
        const deliveryMethodRadios = document.querySelectorAll('input[name="delivery_method"]');
        const courierFields = document.getElementById('courierFields');
        const pickupFields = document.getElementById('pickupFields');
        const npCityInput = document.getElementById('np_city');
        const cityResultsContainer = document.getElementById('cityResults');
        const npDepartmentSelect = document.getElementById('np_department');

        // Функция переключения видимости полей в зависимости от способа доставки
        function toggleDeliveryFields() {
            if (document.getElementById('pickup').checked) {
                pickupFields.style.display = 'block';
                courierFields.style.display = 'none';
            } else {
                pickupFields.style.display = 'none';
                courierFields.style.display = 'block';
            }
        }

        // Следим за изменением способа доставки
        deliveryMethodRadios.forEach(radio => {
            radio.addEventListener('change', toggleDeliveryFields);
        });

        // Функция для отображения результатов поиска городов
        function displayCityResults(cities) {
            cityResultsContainer.innerHTML = ''; // Очищаем предыдущие результаты
            cities.forEach(city => {
                const option = document.createElement('div');
                option.classList.add('list-group-item', 'list-group-item-action');
                option.textContent = city.name;
                option.addEventListener('click', function() {
                    npCityInput.value = city.name;
                    cityResultsContainer.innerHTML = '';
                    fetchDepartments(city.name); // Запрос на отделения
                });
                cityResultsContainer.appendChild(option);
            });
        }

        // Функция отправки запроса на сервер для поиска городов
        function fetchCities(query) {
            if (query.length === 0) {
                cityResultsContainer.innerHTML = '';
                return;
            }

            axios.get('/delivery', {
                    params: {
                        query: query,
                        field: 'city'
                    }
                })
                .then(response => {
                    displayCityResults(response.data);
                })
                .catch(error => {
                    console.error('Ошибка при загрузке городов:', error);
                });
        }

        // Функция отправки запроса на сервер для поиска отделений
        function fetchDepartments(city) {
            axios.get('/delivery', {
                    params: {
                        query: city,
                        field: 'department'
                    }
                })
                .then(response => {
                    npDepartmentSelect.innerHTML = '<option value="">Выберите отделение</option>';
                    response.data.forEach(department => {
                        const option = document.createElement('option');
                        option.value = department.name;
                        option.textContent = department.name;
                        npDepartmentSelect.appendChild(option);
                    });
                })
                .catch(error => {
                    console.error('Ошибка при загрузке отделений:', error);
                });
        }

        // Обработчик ввода в поле города
        npCityInput.addEventListener('input', function() {
            fetchCities(this.value);
        });

        // Инициализация при загрузке страницы
        toggleDeliveryFields();
    });
</script>

@endsection