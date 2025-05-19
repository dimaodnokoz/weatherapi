<?php

namespace App\Http\Controllers;

use App\Models\Subscription;
use Illuminate\Http\Request;

class UnsubscriptionController extends Controller
{
    /**
     * Handle the incoming request.
     */
    public function __invoke(Request $request)
    {
        $encodedToken = $request->route('token');
        $token = base64_decode($encodedToken);
        $id = explode('_', $token)['0'];
        $city = explode('_', $token)['1'];

        if(!isset($id) || !isset($city)){
            return response()->json(['message' => 'Invalid token'], 400);
        }

        // Check if subscription exists and delete it
        $subscription = Subscription::where('id', $id)
            ->where('city', $city)
            ->first();

        if ($subscription) {
            $subscription->delete();
            return response()->json(['message' => 'Unsubscribed successfully'], 200);
        } else {
            return response()->json(['message' => 'Token not found'], 404);
        }
    }
}
