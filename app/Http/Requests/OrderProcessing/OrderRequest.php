<?php

namespace App\Http\Requests\OrderProcessing;

use Illuminate\Foundation\Http\FormRequest;

class OrderRequest extends FormRequest
{
    protected $stopOnFirstFailure = false;
    /**
     * Determine if the user is authorized to make this request.
     */
    public function authorize(): bool
    {
        return true;
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string, \Illuminate\Contracts\Validation\ValidationRule|array<mixed>|string>
     */
    public function rules(): array
    {
        return [
            'first_name' => 'required|string|max:255',
            'last_name' => 'required|string|max:255',
            'delivery_method' => 'required|in:courier,pickup',

            // Поля для курьерской доставки
            'city' => 'nullable|required_if:delivery_method,courier|string|max:255',
            'region' => 'nullable|required_if:delivery_method,courier|string|max:255',
            'street' => 'nullable|required_if:delivery_method,courier|string|max:255',
            'house' => 'nullable|required_if:delivery_method,courier|string|max:255',
            'apartment' => 'nullable|string|max:255', // Квартира не обязательна

            // Поля для доставки в отделение Новой Почты
            'np_city' => 'nullable|required_if:delivery_method,pickup|string|max:255',
            'np_department' => 'nullable|required_if:delivery_method,pickup|string|max:255',

            // Оплата
            'payment_method' => 'required|in:cash,card',
        ];
    }

    public function messages()
    {
        return [
            'first_name.required' => 'Имя обязательно для заполнения.',
            'last_name.required' => 'Фамилия обязательна.',
            'delivery_method.required' => 'Выберите способ доставки.',
            'delivery_method.in' => 'Неверно выбран способ доставки.',
            'city.required_if' => 'Город обязателен для курьерской доставки.',
            'region.required_if' => 'Область обязана быть указана для курьерской доставки.',
            'house.required_if' => 'дом обязателен для курьерской доставки',
            'street.required_if' => 'улица обязательна для курьерской доставки',
            'payment_method.required' => 'Выберите способ оплаты.',
            'payment_method.in' => 'Неверно выбран способ оплаты.',
        ];
    }

}
