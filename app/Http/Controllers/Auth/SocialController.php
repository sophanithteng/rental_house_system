<?php
namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Models\User;
use Illuminate\Support\Facades\Auth;
use Laravel\Socialite\Facades\Socialite;

class SocialController extends Controller
{
    public function redirect($provider)
    {
        return Socialite::driver($provider)->redirect();
    }

    public function callback($provider)
    {
        $socialUser = Socialite::driver($provider)->user();

        // Since your system uses 'username', we map the social name or email to it.
        // You might need a migration to add 'social_id' and 'social_type' to your users table.
        $user = User::updateOrCreate([
            'email' => $socialUser->getEmail(),
        ], [
            'username' => $socialUser->getName() ?? $socialUser->getNickname(),
            'social_id' => $socialUser->getId(),
            'social_type' => $provider,
            // Password can be null for social users if your migration allows it
        ]);

        Auth::login($user);

        return redirect()->intended('/dashboard');
    }
}
