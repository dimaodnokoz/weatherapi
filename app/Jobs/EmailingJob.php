<?php

namespace App\Jobs;

use App\Mail\WeatherMail;
use App\Models\Subscription;
use App\Traits\WeatherAPIRequestsTrait;
use Carbon\Carbon;
use GuzzleHttp\Client;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Queue\Queueable;
use Illuminate\Support\Facades\Bus;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Mail;

class EmailingJob implements ShouldQueue
{
    use Queueable;
    use WeatherAPIRequestsTrait;

    public int $tries = 3;
    // The number of seconds to wait before retrying the job (release job back to queue)
    public int $backoff = 10;
    // The number of seconds the job can run before timing out
    public int $timeout = 60;
    // Note:
    // retry_after property from config/queue.php counts from the start of the job
    // should be bigger than timeout obviously, here it is 90

    public bool $failOnTimeout = true;

    /**
     * Create a new job instance.
     */
    public function __construct()
    {
    }

    /**
     * Execute the job.
     */
    public function handle(Client $httpClient): void
    {
        /**
         * Exceptions, which are not handed explicitly here, are used to trigger failed job retrying process
         * according to this class params configuration. Will be logged in this class 'failed' method.
         */

        $now = Carbon::now();
        $hour = $now->format('H'); // 00-23

        if(10 == $hour){
            // All confirmed subscriptions
            $query = Subscription::query()->where('confirmed', true);
        } else{
            // All confirmed subscriptions with hourly frequency
            $query = Subscription::query()->where('confirmed', true)->where('frequency', 'hourly');
        }

        // Get all unique cities collection from selected subscriptions
        $cities = $query->distinct()->pluck('city');

        foreach($cities as $city){
            $result = $this->getWeatherForCity($httpClient, $city);
            if(200 == $result['code']){
                // Get weather data
                $t = $result['array']['temperature'];
                $h = $result['array']['humidity'];
                $d = $result['array']['description'];

                // Send mails to users
                $rows = $query->where('city', $city)->select('email')->get();
                foreach($rows as $row){
                    Mail::to($row->email)->send(new WeatherMail($city, $t, $h, $d));
                }
            } else{
                Log::warning('Get weather request failed', ['code' => $result['code'], 'message' => $result['array']['message']]);
            }
        }
    }
}
