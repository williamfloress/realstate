<?php

namespace App\Providers;

use App\Models\Prop\HomeType;
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
        $homeTypes = Schema::hasTable('home_types')
            ? HomeType::orderBy('order')->get()
            : collect();
        View::share('homeTypes', $homeTypes);
    }
}
