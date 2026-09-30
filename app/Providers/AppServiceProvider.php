<?php

namespace App\Providers;

use App\Models\Curso;
use App\Models\Ficha;
use App\Policies\CursoPolicy;
use App\Policies\FichaPolicy;
use Carbon\CarbonImmutable;
use Illuminate\Support\Facades\Date;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Gate;
use Illuminate\Support\ServiceProvider;
use Illuminate\Validation\Rules\Password;

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
        $this->configureDefaults();
        $this->configurePolicies();
    }

    /**
     * Register the policies for the course and enrolment models.
     *
     * These abilities are checked against a Team instance, so they cannot be
     * resolved by the model name convention and must be mapped explicitly.
     */
    protected function configurePolicies(): void
    {
        Gate::policy(Curso::class, CursoPolicy::class);
        Gate::policy(Ficha::class, FichaPolicy::class);
    }

    /**
     * Configure default behaviors for production-ready applications.
     */
    protected function configureDefaults(): void
    {
        Date::use(CarbonImmutable::class);

        DB::prohibitDestructiveCommands(
            app()->isProduction(),
        );

        Password::defaults(fn (): ?Password => app()->isProduction()
            ? Password::min(12)
                ->mixedCase()
                ->letters()
                ->numbers()
                ->symbols()
                ->uncompromised()
            : null,
        );
    }
}
