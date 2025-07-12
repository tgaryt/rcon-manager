<?php

declare(strict_types=1);

namespace App\Controllers;

use App\Models\Server;
use App\Services\ServerQueryService;

class ServerController
{
	private Server $serverModel;
	private ServerQueryService $queryService;

	public function __construct()
	{
		$this->serverModel = new Server();
		$this->queryService = new ServerQueryService();
	}

	public function getServers(): void
	{
		header('Content-Type: application/json');

		try {
			$servers = $this->serverModel->getAll();
			$enrichedServers = [];

			foreach ($servers as $server) {
				$serverInfo = $this->queryService->getServerInfo($server['ip'], (int)$server['port']);
				
				if ($serverInfo) {
					$enrichedServers[] = [
						'id' => $server['id'],
						'name' => $serverInfo['hostname'],
						'ip' => $server['ip'],
						'port' => $server['port'],
						'status' => 'online',
						'players' => $serverInfo['players'],
						'max_players' => $serverInfo['max_players'],
						'map' => $serverInfo['map']
					];
				}
			}

			echo json_encode($enrichedServers);
		} catch (Exception $e) {
			error_log('Error in getServers: ' . $e->getMessage());
			http_response_code(500);
			echo json_encode(['error' => 'Internal server error']);
		}
	}

	public function addServer(): void
	{
		header('Content-Type: application/json');

		if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
			http_response_code(405);
			echo json_encode(['error' => 'Method not allowed']);
			return;
		}

		$input = json_decode(file_get_contents('php://input'), true);
		
		if (!$input) {
			$input = $_POST;
		}

		$ip = trim($input['ip'] ?? '');
		$port = (int)($input['port'] ?? 0);
		$rconPassword = trim($input['rcon_password'] ?? '');

		if (empty($ip) || $port <= 0 || $port > 65535) {
			http_response_code(400);
			echo json_encode(['error' => 'Valid IP address and port are required']);
			return;
		}

		if (!filter_var($ip, FILTER_VALIDATE_IP)) {
			http_response_code(400);
			echo json_encode(['error' => 'Invalid IP address format']);
			return;
		}

		if ($this->serverModel->exists($ip, $port)) {
			http_response_code(409);
			echo json_encode(['error' => 'Server with this IP and port already exists']);
			return;
		}

		$rconPassword = empty($rconPassword) ? null : $rconPassword;

		if ($this->serverModel->create($ip, $port, $rconPassword)) {
			echo json_encode(['success' => true, 'message' => 'Server added successfully']);
		} else {
			http_response_code(500);
			echo json_encode(['error' => 'Failed to add server']);
		}
	}

	public function updateRconPassword(): void
	{
		header('Content-Type: application/json');

		if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
			http_response_code(405);
			echo json_encode(['error' => 'Method not allowed']);
			return;
		}

		$input = json_decode(file_get_contents('php://input'), true);
		
		if (!$input) {
			$input = $_POST;
		}

		$serverId = (int)($input['server_id'] ?? 0);
		$newPassword = trim($input['new_rcon_password'] ?? '');

		if ($serverId <= 0 || empty($newPassword)) {
			http_response_code(400);
			echo json_encode(['error' => 'Valid server ID and new password are required']);
			return;
		}

		if ($this->serverModel->updateRconPassword($serverId, $newPassword)) {
			echo json_encode(['success' => true, 'message' => 'RCON password updated successfully']);
		} else {
			http_response_code(500);
			echo json_encode(['error' => 'Failed to update RCON password']);
		}
	}

	public function deleteServer(): void
	{
		header('Content-Type: application/json');

		if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
			http_response_code(405);
			echo json_encode(['error' => 'Method not allowed']);
			return;
		}

		$input = json_decode(file_get_contents('php://input'), true);
		
		if (!$input) {
			$input = $_POST;
		}

		$serverId = (int)($input['server_id'] ?? 0);

		if ($serverId <= 0) {
			http_response_code(400);
			echo json_encode(['error' => 'Valid server ID is required']);
			return;
		}

		if ($this->serverModel->delete($serverId)) {
			echo json_encode(['success' => true, 'message' => 'Server deleted successfully']);
		} else {
			http_response_code(500);
			echo json_encode(['error' => 'Failed to delete server']);
		}
	}
}
