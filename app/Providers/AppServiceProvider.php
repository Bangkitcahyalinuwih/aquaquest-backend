<?php

namespace App\Providers;

use Illuminate\Cache\RateLimiting\Limit;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\RateLimiter;
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
        Model::preventLazyLoading(! app()->isProduction());
        Model::handleLazyLoadingViolationUsing(function ($model, $relation) {
            logger()->warning('Lazy loading terdeteksi: '.get_class($model).'::'.$relation);
        });

        // 60 permintaan per menit per pengguna untuk endpoint kuis (dipakai di minggu 2)
        RateLimiter::for('kuis', fn (Request $request) => Limit::perMinute(60)
            ->by($request->user()?->id ?: $request->ip()));
    }
}
