<?php

namespace App\Providers;

use URL;
use Illuminate\Support\Facades\Blade;
use Illuminate\Support\ServiceProvider;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Support\Facades\Schema;
use Barryvdh\LaravelIdeHelper\IdeHelperServiceProvider;
use Illuminate\Pagination\Paginator;

class AppServiceProvider extends ServiceProvider
{

  public static function setProp($first, $second, $prop)
  {
    if ($first === $second) {
      return $prop;
    }
    return '';
  }

  /**
   * Register any application services.
   *
   * @return void
   */
  public function register(): void
  {
    if ($this->app->isLocal()) {
      $this->app->register(IdeHelperServiceProvider::class);
    }
  }

  /**
   * Bootstrap any application services.
   *
   * @return void
   */
  public function boot(): void
  {
    URL::forceScheme('https');
    Schema::defaultStringLength(191);
    Paginator::useBootstrap();
    $this->bootBuilderMacroses();
    $this->makeDirectives();
  }

  private function bootBuilderMacroses()
  {
    \Illuminate\Database\Query\Builder::macro('toRawSql', function () {
      return array_reduce($this->getBindings(), function ($sql, $binding) {
        return preg_replace('/\?/', is_numeric($binding) ? $binding : "'{$binding}'", $sql, 1);
      }, $this->toSql());
    });

    Builder::macro('toRawSql', function () {
      return $this->getQuery()->toRawSql();
    });
  }

  private function makeDirectives()
  {
    Blade::directive('selected', function ($expression) {
      return "<?php echo \App\Providers\AppServiceProvider::setProp($expression, 'selected');?>";
    });
    Blade::directive('checked', function ($expression) {
      return "<?php echo \App\Providers\AppServiceProvider::setProp($expression, 'checked');?>";
    });



    Blade::directive('moderator', function () {
      return "<?php if (auth()->check()) {
        if (auth()->user()->is_moderate) { ?>";
    });

    Blade::directive('endmoderator', function () {
      return "<?php } } ?>";
    });
  }
}
