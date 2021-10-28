<?php

namespace App\Providers;

use App\Models\Channel;
use App\Models\Message;
use App\User;
use Illuminate\Foundation\Support\Providers\AuthServiceProvider as ServiceProvider;
use Illuminate\Support\Facades\Gate;
use Laravel\Passport\Passport;

class AuthServiceProvider extends ServiceProvider
{
    /**
     * The policy mappings for the application.
     *
     * @var array
     */
    protected $policies = [
        // 'App\Model' => 'App\Policies\ModelPolicy',
    ];

    /**
     * Register any authentication / authorization services.
     *
     * @return void
     */
    public function boot()
    {
        $this->registerPolicies();

        //
        Passport::routes();
        Passport::personalAccessClientId(
            config('passport.personal_access_client.id')
        );

        Passport::personalAccessClientSecret(
            config('passport.personal_access_client.secret')
        );

        $this->channelGates();
        $this->messageGates();

    }

    private function channelGates()
    {
        Gate::define('update-channel', function (User $user, Channel $channel) {
            return $user->id === $channel->owner_id;
        });

    }

    private function messageGates()
    {
        Gate::define('update-message', function (User $user, Message $message) {
            return $user->id === $message->user_id;
        });
    }
}
