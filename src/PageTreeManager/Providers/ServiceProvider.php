<?php
namespace PageTreeManager\Providers;

use Illuminate\Support\ServiceProvider as BaseServiceProvider;

class ServiceProvider extends BaseServiceProvider {

  /**
     * Bootstrap the application events.
     *
     * @return void
     */
    public function boot()
    {
        // Publish config files
        $this->publishes([
            __DIR__ . '/../../config/config.php' => config_path('page-tree-manager.php'),
        ]);
    }

    public function register()
    {

    }
}