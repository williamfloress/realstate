<?php

namespace App\Providers;

use App\Models\Prop\HomeType;
use App\Models\Prop\Property;
use Illuminate\Auth\Middleware\RedirectIfAuthenticated;
use Illuminate\Http\Request;
use Illuminate\Pagination\Paginator;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\View;
use Illuminate\Support\ServiceProvider;

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
        RedirectIfAuthenticated::redirectUsing(function (Request $request) {
            if (Auth::guard('admin')->check()) {
                return route('admin.dashboard');
            }
            return route('home');
        });

        Paginator::useBootstrapFour();

        $homeTypes = Schema::hasTable('home_types')
            ? HomeType::orderBy('order')->get()
            : collect();
        View::share('homeTypes', $homeTypes);

        View::composer('home', function ($view) {
            $cities = Schema::hasTable('properties')
                ? Property::distinct()->whereNotNull('city')->orderBy('city')->pluck('city')
                : collect();
            $view->with('cities', $cities);
        });
    }
}
