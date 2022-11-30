<?php

namespace App\Providers;

use Illuminate\Foundation\Support\Providers\AuthServiceProvider as ServiceProvider;
use Illuminate\Support\Facades\Gate;

use App\Models\User;

class AuthServiceProvider extends ServiceProvider
{
    /**
     * The policy mappings for the application.
     *
     * @var array<class-string, class-string>
     */
    protected $policies = [
        // 'App\Models\Model' => 'App\Policies\ModelPolicy',
    ];

    /**
     * Register any authentication / authorization services.
     *
     * @return void
     */
    public function boot()
    {
        $this->registerPolicies();

        // Storage Gates
        Gate::define('reception', function(User $user) {
            return $user->roles->first()->permissions->pluck('name')->contains('reception');
        });

        Gate::define('sending', function(User $user) {
            return $user->roles->first()->permissions->pluck('name')->contains('sending');
        });

        Gate::define('arrival', function(User $user) {
            return $user->roles->first()->permissions->pluck('name')->contains('arrival');
        });
    }
}
