<?php

declare(strict_types=1);

require_once __DIR__ . '/../../vendor/autoload.php';

use Dotenv\Dotenv;
use App\Controllers\RconController;

$dotenv = Dotenv::createImmutable(__DIR__ . '/../../');
$dotenv->load();

$controller = new RconController();

try {
	$controller->executeCommand();
} catch (Exception $e) {
	error_log('RCON API Error: ' . $e->getMessage());
	http_response_code(500);
	header('Content-Type: application/json');
	echo json_encode(['error' => 'Internal server error']);
}
