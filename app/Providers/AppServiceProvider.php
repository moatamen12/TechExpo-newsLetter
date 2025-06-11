<?php

namespace App\Providers;

use Illuminate\Support\ServiceProvider;
use Illuminate\Support\Facades\Gate;
use Illuminate\Support\Facades\View; 
use App\Policies\DashboardPolicy; 
use App\Models\User;
use App\Models\UserProfiles;
use Illuminate\Auth\Access\Response;
use Illuminate\Pagination\Paginator;
use App\Models\Categorie;

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
        //for the dashboard access
        Gate::define('accessDashboard',function(User $user)
        {
            if ($user->userProfile()->exists()) {
                return Response::allow();
            }
            return Response::deny('You must be logged in to access this page.');
        });

        // Share categories with all views, or specific views if preferred
        View::composer('components.sub_header', function ($view) {
            $view->with('filterCategories', Categorie::orderBy('name')->get());
        });
    }
}
