<?php

namespace App\Providers;

use Illuminate\Support\ServiceProvider;
use Illuminate\Support\Facades\Gate; 
use App\Policies\DashboardPolicy; 
use App\Models\User;
use App\Models\UserProfiles;
use Illuminate\Auth\Access\Response;
use Illuminate\Pagination\Paginator;

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
        //pagination 
        Paginator::useBootstrapFive();
        //authintication gates
        Gate::define('accessDashboard',function(User $user)
        {
            if ($user->userProfile()->exists()) {
                return Response::allow();
            }
            return Response::deny();
        });
        // Gate::define('accessProfile',function(User $user){
        //     if ($user->exists()){
        //     }
        // });
        // Gate::defin('accessProfile',function(User $user))
    }
}
