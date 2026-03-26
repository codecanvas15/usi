<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\RateLimiter;

class RateLimiterController extends Controller
{
    /**
     * Rate limiter for ajax requests
     *
     * @param $key string key
     * @param $attempts integer attempt amount
     * @param $attempts integer decay seconds
     *
     */
    public function rateLimiterAjax(Request $request)
    {
        $rateLimiterAttempts = 2;
        $rateLimiterDecaySeconds = 60;
        $isTooManyAttempts = false;

        if ($request->attempts) {
            $rateLimiterAttempts = $request->attempts;
        }

        if ($request->decay_seconds) {
            $rateLimiterDecaySeconds = $request->decay_seconds;
        }

        // remove rate limiter (development only)
        // RateLimiter::clear($rateLimiterKey);
        // return response()->json('clear!');

        // hit the rate limiter
        RateLimiter::hit($request->key, $rateLimiterDecaySeconds);

        if (RateLimiter::tooManyAttempts($request->key, $rateLimiterAttempts)) {
            $isTooManyAttempts = true;
        }

        if ($isTooManyAttempts) {
            $availableAt = RateLimiter::availableIn($request->key);
            $availableIn = now()->addSeconds($availableAt)->ago();

            $response['is_too_many_requests'] = true;
            $response['available_at_time'] = $availableAt;
            $response['available_at_message'] = $availableIn;
            $response['message'] = 'Terlalu banyak permintaan';

            return response()->json($response);
        } else {
            $response['is_too_many_requests'] = false;
            $response['message'] = 'No rate limiter';

            return response()->json($response);
        }
    }
}
