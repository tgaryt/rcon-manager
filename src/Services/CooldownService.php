<?php

declare(strict_types=1);

namespace App\Services;

class CooldownService
{
	private string $cooldownFile;
	private int $cooldownPeriod;

	public function __construct()
	{
		$this->cooldownFile = $_ENV['COOLDOWN_FILE'];
		$this->cooldownPeriod = (int)$_ENV['RCON_COMMAND_COOLDOWN'];
		$this->ensureFileExists();
	}

	public function isOnCooldown(): bool
	{
		$data = $this->getCooldownData();
		
		if (!isset($data['global'])) {
			return false;
		}

		$lastCommandTime = (int)$data['global'];
		$currentTime = time();

		return ($currentTime - $lastCommandTime) < $this->cooldownPeriod;
	}

	public function getRemainingCooldown(): int
	{
		$data = $this->getCooldownData();
		
		if (!isset($data['global'])) {
			return 0;
		}

		$lastCommandTime = (int)$data['global'];
		$currentTime = time();
		$remaining = $this->cooldownPeriod - ($currentTime - $lastCommandTime);

		return max(0, $remaining);
	}

	public function setCooldown(): void
	{
		$data = $this->getCooldownData();
		$data['global'] = time();
		$this->saveCooldownData($data);
	}

	private function getCooldownData(): array
	{
		if (!file_exists($this->cooldownFile)) {
			return [];
		}

		$content = file_get_contents($this->cooldownFile);
		if ($content === false) {
			return [];
		}

		$decoded = json_decode($content, true);
		return is_array($decoded) ? $decoded : [];
	}

	private function saveCooldownData(array $data): void
	{
		$encoded = json_encode($data, JSON_PRETTY_PRINT);
		if ($encoded !== false) {
			file_put_contents($this->cooldownFile, $encoded, LOCK_EX);
		}
	}

	private function ensureFileExists(): void
	{
		if (!file_exists($this->cooldownFile)) {
			$dir = dirname($this->cooldownFile);
			if (!is_dir($dir)) {
				mkdir($dir, 0755, true);
			}
			file_put_contents($this->cooldownFile, '{}');
		}
	}
}
