<?php

namespace App\Providers;

use App\Enums\Roles;
use App\Models\Role;
use App\Models\User;
use Illuminate\Support\Facades\Gate;
use Illuminate\Support\ServiceProvider;
use Illuminate\Database\Eloquent\Builder;

class AppServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     */
    public function register(): void
    {
        //
    }

    /**
     * Bootstrap any application services.
     */
    public function boot(): void
    {
        // Should return TRUE or FALSE
        Gate::define('admin', function (User $user) {
            $admin = Role::where('name', Roles::ADMIN)->first();

            $user = User::where('users.id', $user->id)->whereRelation('roles', 'roles.id', $admin->id)->first();

            if ($user) {
                return true;
            }
        });

        // Should return TRUE or FALSE
        Gate::define('moderator', function (User $user) {
            $moderator = Role::where('name', Roles::MODERATOR)->first();

            $user = User::where('users.id', $user->id)->whereRelation('roles', 'roles.id', $moderator->id)->first();

            if ($user) {
                return true;
            }
        });
    }
}
