<?php

namespace App\Providers;

use App\Contracts\Organization\RelationInserter;
use App\Contracts\Organization\RelationQuery;
use App\Services\Organization\Inserter;
use App\Services\Organization\Query;
use Illuminate\Support\ServiceProvider;

class AppServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     *
     * @return void
     */
    public function register()
    {
        $this->app->bind(RelationQuery::class, Query::class);
        $this->app->bind(RelationInserter::class, Inserter::class);
    }
}
