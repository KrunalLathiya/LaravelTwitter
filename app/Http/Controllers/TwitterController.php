<?php

namespace App\Http\Controllers;

use App\Models\User;
use Illuminate\Http\RedirectResponse;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Str;
use Laravel\Socialite\Facades\Socialite;
use Throwable;

class TwitterController extends Controller
{
    /**
     * Redirect to Twitter for authentication.
     *
     * @return RedirectResponse
     */
    public function redirectToTwitter(): RedirectResponse
    {
        return Socialite::driver('twitter')->redirect();
    }

    /**
     * Handle Twitter authentication callback.
     *
     * @return RedirectResponse
     */
    public function handleTwitterCallback(): RedirectResponse
    {
        try {
            $twitterUser = Socialite::driver('twitter')->user();
        } catch (Throwable $e) {
            return redirect()->route('login')->with('error', 'Twitter authentication failed.');
        }
        // Retrieve user from the database by twitter_id or create a new user
        $user = User::firstOrCreate(
            ['twitter_id' => $twitterUser->id],
            [
                'name' => $twitterUser->name,
                'email' => $twitterUser->email,
                'password' => Hash::make(Str::random(16))
            ]
        );

        // Login the user
        Auth::login($user, true); // Remember the user

        return redirect()->intended('dashboard');
    }
}