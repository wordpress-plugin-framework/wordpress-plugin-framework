<?php

namespace WordPressPluginFramework\Database\Migrator;

use WordPressPluginFramework\Repositories\Database\Migrator\RepositoryInterface;
use wpdb;

readonly class Migrator implements MigratorInterface
{
	public function __construct(
		protected RepositoryInterface $repository,
		protected wpdb $wpdb,
		protected string $path,
	) {
	}

	public function up(): void
	{
		foreach ($this->names() as $name) {
			if ($this->repository->has($name)) {
				continue;
			}

			$this->migrate($name, 'up');
			$this->repository->add($name);
		}
	}

	public function down(): void
	{
		foreach (array_reverse($this->names()) as $name) {
			if (!$this->repository->has($name)) {
				continue;
			}

			$this->migrate($name, 'down');
			$this->repository->remove($name);
		}
	}

	protected function migrate(string $name, string $direction): void
	{
		$query = strtr(file_get_contents("{$this->path}/migrations/{$name}/{$direction}.sql"), [
			':prefix' => $this->wpdb->prefix,
		]);

		$this->wpdb->query($query);

		if ($this->wpdb->last_error) {
			throw new MigratorException($this->wpdb->last_error);
		}
	}

	protected function names(): array
	{
		$paths = glob("{$this->path}/migrations/*/", GLOB_ONLYDIR);
		if ($paths === false) {
			throw new MigratorException('unable to list migrations');
		}

		$names = array_map(basename(...), $paths);
		foreach ($names as $name) {
			foreach ([
				'up',
				'down'
			] as $direction) {
				if (!file_exists("{$this->path}/migrations/{$name}/{$direction}.sql")) {
					throw new MigratorException("missing {$direction}.sql for {$name}");
				}
			}
		}

		return $names;
	}
}
