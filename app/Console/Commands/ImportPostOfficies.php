<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\Http;
use App\Models\City;
use App\Models\PostOffice;

class ImportPostOfficies extends Command
{
    /**
     * команда для запуска
     *
     * @var string
     */
    protected $signature = 'import:postofficies';//команда для запуска

    /**
     * Консольная команда Описание
     *
     * @var string
     */
    protected $description = 'Импорт отделений новой почты в бд';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        $apiKey = env('NOVA_POSHTA_API_KEY');
        $page = 1;

        while (true)
        {
            $response = Http::post('https://api.novaposhta.ua/v2.0/json/', [
                'apiKey' => $apiKey,
                'modelName' => 'Address',
                'calledMethod' => 'getWarehouses',
                'methodProperties' => [
                    'Page' => $page,
                    'Limit' => "500"
                ]
            ]);
    
            $data = $response->json();
    
            if (empty($data['data']))
            {
                echo 'данных больше нет';
                break;
            }
    
            foreach ($data['data'] as $value)
            {
                $city = City::firstOrCreate(['name' => $value['CityDescriptionRu']]);
                $postOffice = PostOffice::updateOrCreate(['city_id' => $city->id, 'name' => $value['DescriptionRu']]);
                // PostOffice::insertIntoFts($postOffice->id, $postOffice->name);
                // echo $value['DescriptionRu']; 
                // echo $value['CityDescriptionRu']; 
            }

            $page++;
            sleep(1);
        }
    }
}
