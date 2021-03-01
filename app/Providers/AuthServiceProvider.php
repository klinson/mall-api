<?php

namespace App\Providers;

use App\Models\User;
use Illuminate\Support\Facades\Gate;
use Illuminate\Foundation\Support\Providers\AuthServiceProvider as ServiceProvider;

class AuthServiceProvider extends ServiceProvider
{
    /**
     * The policy mappings for the application.
     *
     * @var array
     */
    protected $policies = [
        'App\Model' => 'App\Policies\ModelPolicy',
    ];

    /**
     * Register any authentication / authorization services.
     *
     * @return void
     */
    public function boot()
    {
        $this->registerPolicies();

        Gate::define('is-mine', function (User $user, $model) {
            return $user->id == $model->user_id;
        });

        Gate::define('is-agency', function (User $user) {
            return $user->isAgency();
        });

        Gate::define('is-staff', function (User $user) {
            return $user->isStaff();
        });

        Gate::define('enabled', function (User $user, $model) {
            return $model->has_enabled === 1;
        });
    }
}
