<?php

namespace App\Providers;

use App\Services\TicketService;
use Illuminate\Support\ServiceProvider;
use App\Interfaces\TicketServiceInterface;

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
        $this->app->bind(TicketServiceInterface::class, TicketService::class);
    }
}
