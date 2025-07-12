<?php

declare(strict_types=1);

namespace App\Services;

use App\Models\Server;

class RconService
{
	private Server $serverModel;
	private ServerQueryService $queryService;
	private CommandService $commandService;
	private CooldownService $cooldownService;

	public function __construct()
	{
		$this->serverModel = new Server();
		$this->queryService = new ServerQueryService();
		$this->commandService = new CommandService();
		$this->cooldownService = new CooldownService();
	}

	public function executeCommand(string $command, array $serverIds): array
	{
		$command = trim($command);

		if (empty($command) || empty($serverIds)) {
			return ['error' => 'Command and server selection are required'];
		}

		if (!$this->commandService->isCommandAllowed($command)) {
			return ['error' => 'The command is not allowed due to restrictions'];
		}

		if ($this->cooldownService->isOnCooldown()) {
			$remaining = $this->cooldownService->getRemainingCooldown();
			return ['error' => "You must wait {$remaining} seconds before sending another command"];
		}

		$this->cooldownService->setCooldown();

		$servers = $this->serverModel->getByIds($serverIds);
		if (empty($servers)) {
			return ['error' => 'No valid servers found'];
		}

		$responses = [];
		foreach ($servers as $server) {
			$rconPassword = !empty($server['rcon_password']) 
				? $server['rcon_password'] 
				: $_ENV['RCON_PASSWORD'];

			$result = $this->queryService->executeRconCommand(
				$server['ip'],
				(int)$server['port'],
				$rconPassword,
				$command
			);

			$responses[] = $result;
		}

		return $responses;
	}
}
