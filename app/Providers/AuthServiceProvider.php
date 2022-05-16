<?php

namespace App\Providers;

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
        'App\Models\Model' => 'App\Policies\ModelPolicy',
    ];

    /**
     * Register any authentication / authorization services.
     *
     * @return void
     */
    public function boot()
    {
        $this->registerPolicies();

        if (! $this->app->routesAreCached()) {
            Passport::routes();
        }

        Gate::before(function ($user, $ability) {
            if ($user->is_admin) {
                return true;
            }
            return null;
        });

        Gate::define('post-user', function($user, $post){
            return $user->id == $post->user_id;
        });

        Gate::define('check-is-user', function($user, $user_check){
            return $user->id == $user_check->id;
        });

        Gate::define('check-admin-user', function($user){
            return $user->is_admin;
        });
    }
}
