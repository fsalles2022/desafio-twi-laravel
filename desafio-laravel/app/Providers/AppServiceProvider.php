<?php

namespace App\Providers;

use Illuminate\Support\ServiceProvider;
use Illuminate\Support\Facades\Gate;
use App\Models\Course;
use App\Policies\CoursePolicy;
use App\Repositories\Contracts\UserRepositoryInterface;
use App\Repositories\Eloquent\UserRepository;

class AppServiceProvider extends ServiceProvider
{
    public function boot(): void
    {
        Gate::policy(Course::class, CoursePolicy::class);
    }

    public function register(): void
    {
      
    }
}
