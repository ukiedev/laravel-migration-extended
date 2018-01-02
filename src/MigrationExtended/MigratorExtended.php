<?php

namespace App\MigrationExtended;

use Illuminate\Database\Migrations\Migrator;

/**
 * Class MigratorExtended
 * @package App\Console\Commands\Dev
 */
class MigratorExtended extends Migrator
{
	/**
	 * @param array $migrations
	 * @param array $options
	 */
	public function runPending(array $migrations, array $options = [])
	{
		// First we will just make sure that there are any migrations to run. If there
		// aren't, we will just make a note of it to the developer so they're aware
		// that all of the migrations have been run against this database system.
		if (count($migrations) == 0) {
			$this->note('<info>Nothing to migrate.</info>');

			return;
		}

		// Next, we will get the next batch number for the migrations so we can insert
		// correct batch number in the database migrations repository when we store
		// each migration's execution. We will also extract a few of the options.
		$batch = $this->repository->getNextBatchNumber();

		$pretend = $options['pretend'] ?? false;

		$step = $options['step'] ?? false;

		/**
		 * Added first option
		 */

		if (array_get($options, 'first')) {
			$migrations = [array_first($migrations)];
		}

		// Once we have the array of migrations, we will spin through them and run the
		// migrations "up" so the changes are made to the databases. We'll then log
		// that the migration was run so we don't repeat it next time we execute.
		foreach ($migrations as $file) {
			$this->runUp($file, $batch, $pretend);

			if ($step) {
				$batch++;
			}
		}
	}
}
