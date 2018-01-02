<?php

namespace App;

use App\MigrationExtended\MigratorExtended;
use Illuminate\Console\ConfirmableTrait;
use Illuminate\Database\Console\Migrations\MigrateCommand;

/**
 * Class MigrationRun
 * @package App\Console\Commands\Dev
 */
class MigrationExtended extends MigrateCommand
{
	use ConfirmableTrait;

	protected $migrator;

	/**
	 * @var string
	 */
	protected $signature = 'migrate_extended {--database= : The database connection to use.}
                {--force : Force the operation to run when in production.}
                {--path= : The path of migrations files to be executed.}
                {--pretend : Dump the SQL queries that would be run.}
                {--seed : Indicates if the seed task should be re-run.}
                {--step : Force the migrations to be run so they can be rolled back individually.}
                {--first : Run only first migration}';

	/**
	 * @var string
	 */
	protected $description = 'Run the database migrations with custom options';

	/**
	 * MigrationRun constructor.
	 */
	public function __construct()
	{
		parent::__construct(new MigratorExtended(app('migration.repository'), app('db'), app('files')));
	}

	public function handle()
	{
		if (! $this->confirmToProceed()) {
			return;
		}

		$this->prepareDatabase();

		$migrations = $this->getMigrationPaths();

		// Next, we will check to see if a path option has been defined. If it has
		// we will use the path relative to the root of this installation folder
		// so that migrations may be run for any path within the applications.
		$this->migrator->run($migrations, [
			'pretend' => $this->option('pretend'),
			'step' => $this->option('step'),
			'first' => $this->option('first')
		]);

		// Once the migrator has run we will grab the note output and send it out to
		// the console screen, since the migrator itself functions without having
		// any instances of the OutputInterface contract passed into the class.
		foreach ($this->migrator->getNotes() as $note) {
			$this->output->writeln($note);
		}

		// Finally, if the "seed" option has been given, we will re-run the database
		// seed task to re-populate the database, which is convenient when adding
		// a migration and a seed at the same time, as it is only this command.
		if ($this->option('seed')) {
			$this->call('db:seed', ['--force' => true]);
		}
	}
}
