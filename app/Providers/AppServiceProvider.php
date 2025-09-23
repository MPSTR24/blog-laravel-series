<?php

namespace App\Providers;

use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
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
        // Post Two DB::listen example
        DB::listen(static function ($query) {
            Log::info(
                "Query: {$query->sql}, Bindings: " . implode(',', $query->bindings) . ", Time: {$query->time}ms"
            );
        });
    }
}
