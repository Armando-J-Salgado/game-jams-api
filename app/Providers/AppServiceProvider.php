<?php

namespace App\Providers;

use Illuminate\Support\Facades\Gate;
use App\Models\Category;
use App\Models\Competition;
use App\Models\Handover;
use App\Models\Module;
use App\Models\Team;
use App\Models\User;
use App\Policies\CategoryPolicy;
use App\Policies\CompetitionPolicy;
use App\Policies\HandoverPolicy;
use App\Policies\ModulePolicy;
use App\Policies\TeamPolicy;
use App\Policies\UserPolicy;
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
        Gate::policy(Category::class, CategoryPolicy::class);
        Gate::policy(Competition::class, CompetitionPolicy::class);
        Gate::policy(Handover::class, HandoverPolicy::class);
        Gate::policy(Module::class, ModulePolicy::class);
        Gate::policy(Team::class, TeamPolicy::class);
        Gate::policy(User::class, UserPolicy::class);
    }
}
