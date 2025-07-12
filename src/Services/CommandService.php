<?php

declare(strict_types=1);

namespace App\Services;

class CommandService
{
	private string $allowedCommandsFile;
	private string $blockedCommandsFile;

	public function __construct()
	{
		$this->allowedCommandsFile = $_ENV['ALLOWED_COMMANDS_FILE'];
		$this->blockedCommandsFile = $_ENV['BLOCKED_COMMANDS_FILE'];
		$this->ensureFilesExist();
	}

	public function isCommandAllowed(string $command): bool
	{
		$command = trim($command);
		
		if ($this->isCommandBlocked($command)) {
			return false;
		}

		$allowedCommands = $this->readCommandsFromFile($this->allowedCommandsFile);
		
		if (empty($allowedCommands)) {
			return true;
		}

		return in_array($command, $allowedCommands, true);
	}

	private function isCommandBlocked(string $command): bool
	{
		$blockedCommands = $this->readCommandsFromFile($this->blockedCommandsFile);
		return in_array($command, $blockedCommands, true);
	}

	private function readCommandsFromFile(string $file): array
	{
		if (!file_exists($file)) {
			return [];
		}

		$content = file_get_contents($file);
		if ($content === false) {
			return [];
		}

		$lines = array_filter(
			array_map('trim', explode("\n", $content)),
			fn($line) => !empty($line) && !str_starts_with($line, '#')
		);

		return array_values($lines);
	}

	private function ensureFilesExist(): void
	{
		foreach ([$this->allowedCommandsFile, $this->blockedCommandsFile] as $file) {
			if (!file_exists($file)) {
				$dir = dirname($file);
				if (!is_dir($dir)) {
					mkdir($dir, 0755, true);
				}
				file_put_contents($file, '');
			}
		}
	}
}
