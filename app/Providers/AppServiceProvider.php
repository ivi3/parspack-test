<?php

namespace App\Providers;

use App\Packages\Process\LinuxProcess;
use App\Packages\Zip\Zip;
use App\Repositories\UserEloquentRepository;
use App\Repositories\UserRepositoryInterface;
use Illuminate\Support\ServiceProvider;
use \Symfony\Component\Process\Process;

class AppServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     *
     * @return void
     */
    public function register()
    {
        // Get list of running processes on the server injection
        $this->app->when(LinuxProcess::class)->needs(Process::class)->give(function (){
            return new Process(["ps","aux"]);
        });
        // inject User repository
        $this->app->bind(
            UserRepositoryInterface::class,
            UserEloquentRepository::class,
        );
    }

    /**
     * Bootstrap any application services.
     *
     * @return void
     */
    public function boot()
    {
        //
    }
}
