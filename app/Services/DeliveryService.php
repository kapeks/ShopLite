<?php

namespace App\Services;

use App\Models\City;

/**
 * Cервис отвечает за поиск отделений нп.
 */
class DeliveryService
{
    public static function getDeliveryPoints(array $data)
    {
        $query = $data['query']; //символы ввода
        $field = $data['field']; //поле ввода: отделение или город

        if ($field === 'city') {
            return City::where('name', 'LIKE', "$query%")->limit(10)->get();
        } elseif ($field === 'department') {
            $city = City::where('name', 'LIKE', "$query%")->first();
            return  $city->post_offices;
        }

        return null;
    }
}
