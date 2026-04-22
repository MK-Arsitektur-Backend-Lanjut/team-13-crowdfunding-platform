<?php

namespace App\Providers;

use App\Repositories\CampaignRepository;
use App\Repositories\CampaignRepositoryInterface;
use App\Repositories\DonationCategoryRepository;
use App\Repositories\DonationCategoryRepositoryInterface;
use Illuminate\Support\ServiceProvider;

class AppServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     */
    public function register(): void
    {
        $this->app->bind(CampaignRepositoryInterface::class, CampaignRepository::class);
        $this->app->bind(DonationCategoryRepositoryInterface::class, DonationCategoryRepository::class);
    }

    /**
     * Bootstrap any application services.
     */
    public function boot(): void
    {
        //
    }
}
