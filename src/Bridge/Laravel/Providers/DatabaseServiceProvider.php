<?php

namespace Luclin2\Laravel\Providers;

// use Luclin\Cabin\Fun\ConnectionFactory;

use Illuminate\Support\ServiceProvider;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Schema\Grammars\PostgresGrammar;
use \Illuminate\Support\Fluent;

class DatabaseServiceProvider extends ServiceProvider
{
    public function boot()
    {

    }

    /**
     * Register the service provider.
     *
     * @return void
     */
    public function register()
    {
        // $this->app->singleton('db.factory', function ($app) {
        //     return new ConnectionFactory($app);
        // });
    }
}
