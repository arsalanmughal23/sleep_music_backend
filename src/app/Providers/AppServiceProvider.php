<?php

namespace App\Providers;

use App\Models\Media;
use App\Models\Module;
use App\Models\NotificationUser;
use App\Models\Playlist;
use App\Observers\MediaObserver;
use App\Observers\ModuleObserver;
use App\Observers\NotificationObserver;
use App\Observers\PlaylistObserver;
use Illuminate\Support\Facades\App;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\ServiceProvider;


class AppServiceProvider extends ServiceProvider
{
    /**
     * Bootstrap any application services.
     *
     * @return void
     */
    public function boot()
    {
        App::setLocale('en');
        Schema::defaultStringLength(191);
        Module::observe(ModuleObserver::class);
        Media::observe(MediaObserver::class);
        // NotificationUser::observe(NotificationObserver::class);
        Playlist::observe(PlaylistObserver::class);
        if (env('APP_ENV') == "production") {
            $this->app['request']->server->set('HTTPS', true);
        }
    }

    /**
     * Register any application services.
     *
     * @return void
     */
    public function register()
    {
        //
    }
}
