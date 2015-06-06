<?php namespace Borla\Chikka;

use Illuminate\Support\ServiceProvider;
use Config;

/**
 * Service
 */

class Service extends ServiceProvider {

  /**
   * On boot
   */
  public function boot() {
    // Config path
    $configPath = dirname(__FILE__) . '/../../config';
    // Publish config
    $this->publishes([
      // Publish config
      $configPath . '/chikka.php' => config_path('chikka.php'),
    ]);
    // Merge config
    $this->mergeConfigFrom($configPath . '/defaults.php', 'chikka');
  }

  /**
   * Register the service provider.
   *
   * @return void
   */
  public function register() {
    // Call all to register
    $this->registerChikka();
  }

  /**
   * Register the authenticator services.
   *
   * @return void
   */
  protected function registerChikka() {
    // Register chikka
    $this->app->singleton('chikka', function($app) {
      // Use chikka
      return new Chikka(Config::get('chikka'));
    });
  }

}
