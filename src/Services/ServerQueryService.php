<?php

declare(strict_types=1);

namespace App\Services;

use xPaw\SourceQuery\SourceQuery;
use xPaw\SourceQuery\Exception\SourceQueryException;

class ServerQueryService
{
	private SourceQuery $query;

	public function __construct()
	{
		$this->query = new SourceQuery();
	}

	public function getServerInfo(string $ip, int $port): ?array
	{
		try {
			$this->query->Connect($ip, $port, 1, SourceQuery::SOURCE);
			$info = $this->query->GetInfo();
			$this->query->Disconnect();

			if ($info && isset($info['HostName'])) {
				return [
					'hostname' => $this->sanitizeServerName($info['HostName']),
					'players' => $info['Players'] ?? 0,
					'max_players' => $info['MaxPlayers'] ?? 0,
					'map' => $info['Map'] ?? 'Unknown'
				];
			}

			return null;
		} catch (SourceQueryException $e) {
			error_log("Server query failed for {$ip}:{$port} - " . $e->getMessage());
			return null;
		} finally {
			$this->query->Disconnect();
		}
	}

	public function executeRconCommand(string $ip, int $port, string $rconPassword, string $command): array
	{
		try {
			$this->query->Connect($ip, $port, 1, SourceQuery::SOURCE);
			$this->query->SetRconPassword($rconPassword);

			$serverInfo = $this->query->GetInfo();
			$hostname = isset($serverInfo['HostName']) 
				? $this->sanitizeServerName($serverInfo['HostName'])
				: 'Unknown Server';

			$response = $this->query->Rcon($command);
			$this->query->Disconnect();

			if (empty($response)) {
				$response = 'No output received from the command.';
			}

			if (strtolower($command) === '_restart' && !str_contains(strtolower($response), 'error')) {
				$response .= "\nThe server will be restarted shortly.";
			}

			return [
				'success' => true,
				'server' => "{$hostname} ({$ip}:{$port})",
				'response' => htmlspecialchars($response, ENT_QUOTES, 'UTF-8')
			];

		} catch (SourceQueryException $e) {
			error_log("RCON command failed for {$ip}:{$port} - " . $e->getMessage());
			
			return [
				'success' => false,
				'server' => "{$ip}:{$port}",
				'response' => "Error: " . htmlspecialchars($e->getMessage(), ENT_QUOTES, 'UTF-8')
			];
		} finally {
			$this->query->Disconnect();
		}
	}

	private function sanitizeServerName(string $name): string
	{
		$name = preg_replace('/[^\p{L}\p{N}\s\!\#\.(\)\-\+\|\/]/u', '', $name);
		$name = rtrim($name, '.');
		return trim($name);
	}

	public function __destruct()
	{
		$this->query->Disconnect();
	}
}
