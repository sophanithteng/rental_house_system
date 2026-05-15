<?php
namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Models\User;
use App\Models\Tenant;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Str;
use Laravel\Socialite\Facades\Socialite;

class SocialController extends Controller
{
    public function redirect(Request $request, $provider)
    {
        return Socialite::driver($provider)->redirect();
    }

    public function callback(Request $request, $provider)
    {
        try {
            $socialUser = Socialite::driver($provider)->user();
        } catch (\Exception $e) {
            return redirect()->route('login')->withErrors([
                'email' => 'Unable to authenticate with ' . ucfirst($provider) . '. Please try again.',
            ]);
        }

        $baseUsername = Str::slug($socialUser->getName() ?? $socialUser->getNickname(), '_');
        $username = $baseUsername;

        while (User::where('username', $username)->where('email', '!=', $socialUser->getEmail())->exists()) {
            $username = $baseUsername . '_' . Str::random(5);
        }

        $user = User::updateOrCreate([
            'email' => $socialUser->getEmail(),
        ], [
            'username' => $username,
            'social_id' => $socialUser->getId(),
            'social_type' => $provider,
            'role' => 'User',
        ]);

        // Ensure the user has a tenant profile if it's a new account
        Tenant::firstOrCreate([
            'user_id' => $user->id,
        ], [
            'full_name' => $socialUser->getName() ?? $username,
        ]);

        Auth::login($user);

        $request->session()->regenerate();

        return redirect()->intended('/dashboard');
    }
}
