<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Str;
use Laravel\Socialite\Facades\Socialite;

class GoogleAuthController extends Controller
{
    /**
     * Redirect to Google OAuth
     */
    public function redirectToGoogle()
    {
        return Socialite::driver('google')
            ->scopes([
                'https://www.googleapis.com/auth/youtube.readonly',
                'https://www.googleapis.com/auth/youtube.upload',
                'https://www.googleapis.com/auth/drive.file',
            ])
            ->redirect();
    }

    /**
     * Handle Google OAuth callback
     */
    public function handleGoogleCallback()
    {
        try {
            $googleUser = Socialite::driver('google')->user();

            $user = User::where('google_id', $googleUser->getId())
                ->orWhere('email', $googleUser->getEmail())
                ->first();

            if ($user) {
                // Update existing user
                $user->update([
                    'google_id' => $googleUser->getId(),
                    'google_token' => $googleUser->token,
                    'google_refresh_token' => $googleUser->refreshToken ?? $user->google_refresh_token,
                    'avatar' => $googleUser->getAvatar(),
                ]);
            } else {
                // Create new user
                $user = User::create([
                    'name' => $googleUser->getName(),
                    'email' => $googleUser->getEmail(),
                    'google_id' => $googleUser->getId(),
                    'google_token' => $googleUser->token,
                    'google_refresh_token' => $googleUser->refreshToken,
                    'avatar' => $googleUser->getAvatar(),
                    'password' => Hash::make(Str::random(24)),
                    'email_verified_at' => now(),
                ]);
            }

            Auth::login($user, true);

            return redirect()->route('dashboard')->with('success', 'Successfully logged in with Google!');
        } catch (\Exception $e) {
            return redirect()->route('login')->with('error', 'Unable to login with Google. Please try again.');
        }
    }

    /**
     * Disconnect Google account
     */
    public function disconnect(Request $request)
    {
        $user = $request->user();
        
        $user->update([
            'google_token' => null,
            'google_refresh_token' => null,
        ]);

        return back()->with('success', 'Google account disconnected successfully.');
    }
}
