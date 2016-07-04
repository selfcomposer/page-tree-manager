<?php
/*
 * Licensed to the Apache Software Foundation (ASF) under one
 * or more contributor license agreements.  See the NOTICE file
 * distributed with this work for additional information
 * regarding copyright ownership.  The ASF licenses this file
 * to you under the Apache License, Version 2.0 (the
 * "License"); you may not use this file except in compliance
 * with the License.  You may obtain a copy of the License at
 *
 * http://www.apache.org/licenses/LICENSE-2.0
 *
 * Unless required by applicable law or agreed to in writing, software
 * distributed under the License is distributed on an "AS IS" BASIS,
 * WITHOUT WARRANTIES OR CONDITIONS OF ANY KIND, either express or implied.
 * See the License for the specific language governing permissions and
 * limitations under the License.
 */

namespace SelfComposer\PageTreeManager\Providers;

use Illuminate\Support\ServiceProvider as BaseServiceProvider;

use SelfComposer\PageTreeManager\PageObserver;
use SelfComposer\PageTreeManager\Services\SlugService;

use Cocur\Slugify\Slugify;

/**
 * The ServiceProvider of the package.
 *
 * @package  SelfComposer\PageTreeManager
 */
class ServiceProvider extends BaseServiceProvider
{
    /**
     * Bootstrap the application events.
     *
     * @return void
     */
    public function boot()
    {
        // Publish config files
        $this->publishes([
            __DIR__ . '/../../config/config.php' => $this->app->configPath().'/'.'page-tree-manager.php',
        ], 'config');

        if (! class_exists('CreateTreesTables'))
        {
            // Publish the migration
            $timestamp = date('Y_m_d_His', time());

            $this->publishes([
                __DIR__.'/../../migrations/create_pages_tables.php.stub' => $this->app->databasePath().'/migrations/'.$timestamp.'_create_pages_tables.php',
            ], 'migrations');
        }
    }

    /**
     * Register the service provider.
     *
     * @return void
     */
    public function register()
    {
        $this->app->singleton(PageObserver::class, function ($app)
        {
            return new PageObserver(new SlugService(new Slugify()));
        });
    }
}