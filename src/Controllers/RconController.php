<?php

declare(strict_types=1);

namespace App\Controllers;

use App\Services\RconService;

class RconController
{
	private RconService $rconService;

	public function __construct()
	{
		$this->rconService = new RconService();
	}

	public function executeCommand(): void
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

		$command = trim($input['command'] ?? '');
		$serverIds = $input['server_ids'] ?? [];

		if (!is_array($serverIds)) {
			$serverIds = [];
		}

		$serverIds = array_map('intval', array_filter($serverIds, 'is_numeric'));

		if (empty($command)) {
			http_response_code(400);
			echo json_encode(['error' => 'Command is required']);
			return;
		}

		if (empty($serverIds)) {
			http_response_code(400);
			echo json_encode(['error' => 'At least one server must be selected']);
			return;
		}

		$result = $this->rconService->executeCommand($command, $serverIds);

		if (isset($result['error'])) {
			http_response_code(400);
			echo json_encode($result);
			return;
		}

		echo json_encode($result);
	}
}
