<?php

namespace App\Providers;

use Illuminate\Support\Facades\Schema;

use Illuminate\Support\ServiceProvider;
use App\Models\Message;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\View;
use Illuminate\Support\Facades\DB;


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
        Schema::defaultStringLength(191);  // Ensure string length is 191
        View::composer('*', function ($view) {
            $unreadCount = 0;
            $latestMessages = collect();

            if (Auth::check()) {
                $adminId = Auth::id();

                $baseQuery = Message::with('sender')
                    ->where('receiver_id', $adminId)
                    ->where('is_read', 0);

                $unreadCount = (clone $baseQuery)->count();
                $latestMessages = (clone $baseQuery)
                    ->latest()
                    ->take(5)
                    ->get();
            }

            $view->with(compact('unreadCount', 'latestMessages'));
        });
        
        date_default_timezone_set(config('app.timezone'));

        DB::statement("SET time_zone = '+02:00'");
    }
}
