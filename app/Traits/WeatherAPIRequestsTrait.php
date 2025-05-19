<?php

namespace App\Traits;

use GuzzleHttp\Client;
use GuzzleHttp\Exception\GuzzleException;

trait WeatherAPIRequestsTrait
{
    public function getWeatherForCity(Client $httpClient, string $city): array {
        $wapiUrl = env('WEATHERAPI_URL');
        $wapiKey = env('WEATHERAPI_KEY');
        $requestUrl = $wapiUrl . '?key=' . $wapiKey . '&q=' . $city;

        try {
            $response = $httpClient->get($requestUrl);
            $bodyArray = json_decode($response->getBody()); // decode into object or into assoc array (true as second param)
            $temperature = $bodyArray->current->temp_c;
            $humidity = $bodyArray->current->humidity;
            $description = 'Last updated at ' . $bodyArray->current->last_updated;
            return array('array' => ['temperature' => $temperature, 'humidity' => $humidity, 'description' => $description ], 'code' => 200);
        } catch(GuzzleException $e) {
            $response = $e->getResponse();
            if ($response && 400 == $response->getStatusCode()) {
                $bodyArray = json_decode($response->getBody());
                if(isset($bodyArray->error) && 1006 == $bodyArray->error->code){
                    return array('array' => ['message' => 'City not found'], 'code' => 404);
                }
            }

            // This is not quite correct in general, because there may be exceptions from Client, Server etc. side
            // But used here to meet project doc
            return array('array' => ['message' => 'Invalid request'], 'code'=> 400);
        }
    }
}
