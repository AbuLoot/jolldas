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

        // Joystick
        Gate::define('allow-filemanager', function(User $user) {
            return $user->roles->first()->permissions->pluck('name')->contains('allow-filemanager');
        });

        Gate::define('allow-calc', function(User $user) {
            return $user->roles->first()->permissions->pluck('name')->contains('allow-calc');
        });

        Gate::define('export', function(User $user) {
            return $user->roles->first()->permissions->pluck('name')->contains('export');
        });

        Gate::define('import', function(User $user) {
            return $user->roles->first()->permissions->pluck('name')->contains('import');
        });

        Gate::define('joytable', function(User $user) {
            return $user->roles->first()->permissions->pluck('name')->contains('joytable');
        });

        // Storage Gates
        Gate::define('reception', function(User $user) {
            return $user->roles->first()->permissions->pluck('name')->contains('reception');
        });

        Gate::define('sending', function(User $user) {
            return $user->roles->first()->permissions->pluck('name')->contains('sending');
        });

        Gate::define('sorting', function(User $user) {
            return $user->roles->first()->permissions->pluck('name')->contains('sorting');
        });

        Gate::define('arrival', function(User $user) {
            return $user->roles->first()->permissions->pluck('name')->contains('arrival');
        });

        Gate::define('giving', function(User $user) {
            return $user->roles->first()->permissions->pluck('name')->contains('giving');
        });
    }
}
