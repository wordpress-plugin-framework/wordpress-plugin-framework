<?php

namespace WordPressPluginFramework\Repositories\Database\Migrator;

class Repository implements RepositoryInterface
{
	protected array $migrations;

	public function __construct(
		protected string $key,
	) {
		$this->migrations = get_option($this->key, []);
	}

	public function has(string $name): bool
	{
		return isset($this->migrations[$name]);
	}

	public function add(string $name): void
	{
		if ($this->has($name)) {
			return;
		}

		$this->migrations[$name] = time();

		update_option($this->key, $this->migrations);
	}

	public function remove(string $name): void
	{
		if (!$this->has($name)) {
			return;
		}

		unset($this->migrations[$name]);

		update_option($this->key, $this->migrations);
	}
}
