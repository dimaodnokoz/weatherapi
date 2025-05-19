<?php

namespace App\Http\Controllers;

use App\Traits\WeatherAPIRequestsTrait;
use GuzzleHttp\Client;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;

class WeatherAPIController extends Controller
{
    use WeatherAPIRequestsTrait;

    /**
     * @var Client
     */
    private $httpClient;

    public function __construct(Client $httpClient){
        $this->httpClient = $httpClient;
    }

    /**
     * Handle the incoming request.
     */
    public function __invoke(Request $request)
    {
        // Get city from query
        $city = $request->query('city');
        if (!$city) {
            return response()->json(['message' => 'Invalid request'], 400);
        }

        $result = $this->getWeatherForCity($this->httpClient, $city);

        return response()->json($result['array'], $result['code']);
    }
}
