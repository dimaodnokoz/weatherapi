<?php

namespace App\Http\Controllers;

use App\Traits\WeatherAPIRequestsTrait;
use GuzzleHttp\Client;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Validator;
use App\Models\Subscription;
use Illuminate\Support\Facades\Mail;
use App\Mail\ConfirmSubscriptionMail;

class SubscriptionController extends Controller
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
        // Validation
        $rules = [
            'email' => 'required|email|max:100',
            'city' => 'required|string|max:100',
            'frequency' => 'required|in:hourly,daily',
        ];

        $contentType = $request->header('Content-Type');

        if ('application/json' == $contentType) {
            $dataToValidate = $request->json()?->all();
        } elseif ('application/x-www-form-urlencoded' === $contentType) {
            $dataToValidate = $request->input();
        } else {
            Log::warning('Unsupported Content-Type', ['Content-Type' => $contentType]);
            // It's not quite correct error description, but used here to meet project doc
            response()->json(['message' => 'Invalid input'], 400);
        }

        if(!isset($dataToValidate)){
            return response()->json(['message' => 'Invalid input'], 400);
        }

        $validator = Validator::make($dataToValidate, $rules);
        if ($validator->fails()) {
            return response()->json(['message' => 'Invalid input'], 400);
        }

        $email = $dataToValidate['email'];
        $city = $dataToValidate['city'];
        $frequency = $dataToValidate['frequency'];

        // Check if email subscribed already
        $subscription = Subscription::query()->where('email', $email)->first();
        if(null !== $subscription){
            return response()->json(['message' => 'Email already subscribed'], 409);
        }

        // Validate city by call to Weather API
        $result = $this->getWeatherForCity($this->httpClient, $city);
        if(200 != $result['code']){
            return response()->json(['message' => 'Invalid input'], 400);
        }

        // Add subscription to db (not confirmed)
        $subscription = Subscription::query()->create([
            'email' => $email,
            'city' => $city,
            'frequency' => $frequency,
        ]);

        // Send subscription token to email
        try {
            $token = base64_encode($subscription->id . '_' . $city);
            Mail::to($email)->send(new ConfirmSubscriptionMail($token));
        } catch (\Exception $e) {
            Log::error('Send subscription confirmation email failed', ['error' => $e->getMessage()]);
        }

        return response()->json(['message' => 'Subscription successful. Confirmation email sent.'], 200);
    }
}
