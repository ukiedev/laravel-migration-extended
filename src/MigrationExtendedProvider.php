<?php

namespace App;

use Illuminate\Support\ServiceProvider;

/**
 * Class MigrationExtendedProvider
 * @package App
 */
class MigrationExtendedProvider extends ServiceProvider
{
	public function boot()
	{
		if ($this->app->runningInConsole()) {
			$this->commands([
				MigrationExtended::class
			]);
		}
	}
}
