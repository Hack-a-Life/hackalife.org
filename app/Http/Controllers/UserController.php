<?php

namespace App\Http\Controllers;

use App\User;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Laravel\Socialite\Facades\Socialite;

class UserController extends Controller
{

    /**
     * Logs the user in via OAuth2 and Discord.
     *
     * @return \Illuminate\Routing\Redirector
     */
    public function login()
    {
        if (! request()->has('code')) {
            return $this->getAuthorizationFirst();
        }
        
        $user = $this->findByEmailOrCreate($this->getDiscordUser());

        Auth::login($user, true);

        return redirect('/dashboard');
    }

    /**
     * Logs the user out.
     *
     * @return \Illuminate\Routing\Redirector
     */
    public function logout()
    {
        Auth::logout();

        return redirect('/');
    }

    /**
     * Finds the user by email, if the user doesn't
     * exists we'll create a record for them.
     *
     * @param  SocialiteProviders\Manager\OAuth2\User $discordUser
     * @return App\User
     */
    protected function findByEmailOrCreate($discordUser)
    {
        $user = User::where('email', $discordUser->email)->first();

        if ($user !== null) {
            return $this->updateAndReturnUser($discordUser, $user);
        }

        return User::create([
            'discord_id'           => $discordUser->user['id'],
            'email'                => $discordUser->user['email'],
            'username'             => $discordUser->user['username'],
            'discriminator'        => $discordUser->user['discriminator'],
            'avatar'               => $discordUser->user['avatar'],
            'discord_token'        => $discordUser->token,
            'discord_refreshToken' => $discordUser->refreshToken,
            'discord_expires'      => Carbon::now()->addSeconds($discordUser->expiresIn)
        ]);
    }

    /**
     * Gets the discord OAuth2 login url.
     *
     * @return \Illuminate\Routing\Redirector
     */
    protected function getAuthorizationFirst()
    {
        return Socialite::driver('discord')->scopes([
            'identify'
        ])->redirect();
    }

    /**
     * Gets the Discord user object via Socialite.
     *
     * @return SocialiteProviders\Manager\OAuth2\User
     */
    protected function getDiscordUser()
    {
        return Socialite::driver('discord')->user();
    }

    /**
     * Updates the user if the tokens has been
     * changed, and returns the user object.
     *
     * @param  SocialiteProviders\Manager\OAuth2\User $discordUser
     * @param  \App\User                              $user
     * @return \App\User
     */
    protected function updateAndReturnUser($discordUser, $user)
    {
        if ($user->discord_token === $discordUser->token) {
            return $user;
        }

        $user->discord_token        = $discordUser->token;
        $user->discord_refreshToken = $discordUser->refreshToken;
        $user->discord_expires      = Carbon::now()->addSeconds($discordUser->expiresIn);

        $user->save();

        return $user;
    }
}
