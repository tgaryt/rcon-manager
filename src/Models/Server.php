<?php

declare(strict_types=1);

namespace App\Models;

use App\Config\Database;
use PDO;
use PDOException;

class Server
{
	private PDO $db;

	public function __construct()
	{
		$this->db = Database::getConnection();
	}

	public function create(string $ip, int $port, ?string $rconPassword = null): bool
	{
		try {
			$stmt = $this->db->prepare(
				'INSERT INTO servers (ip, port, rcon_password) VALUES (:ip, :port, :rcon_password)'
			);

			return $stmt->execute([
				'ip' => $ip,
				'port' => $port,
				'rcon_password' => $rconPassword
			]);
		} catch (PDOException $e) {
			error_log('Server creation failed: ' . $e->getMessage());
			return false;
		}
	}

	public function getAll(): array
	{
		try {
			$stmt = $this->db->query('SELECT id, ip, port FROM servers ORDER BY id ASC');
			return $stmt->fetchAll();
		} catch (PDOException $e) {
			error_log('Failed to fetch servers: ' . $e->getMessage());
			return [];
		}
	}

	public function getById(int $id): ?array
	{
		try {
			$stmt = $this->db->prepare('SELECT * FROM servers WHERE id = :id');
			$stmt->execute(['id' => $id]);
			$result = $stmt->fetch();
			return $result ?: null;
		} catch (PDOException $e) {
			error_log('Failed to fetch server: ' . $e->getMessage());
			return null;
		}
	}

	public function getByIds(array $ids): array
	{
		if (empty($ids)) {
			return [];
		}

		try {
			$placeholders = str_repeat('?,', count($ids) - 1) . '?';
			$stmt = $this->db->prepare(
				"SELECT id, ip, port, rcon_password FROM servers WHERE id IN ($placeholders)"
			);
			$stmt->execute($ids);
			return $stmt->fetchAll();
		} catch (PDOException $e) {
			error_log('Failed to fetch servers by IDs: ' . $e->getMessage());
			return [];
		}
	}

	public function exists(string $ip, int $port): bool
	{
		try {
			$stmt = $this->db->prepare('SELECT COUNT(*) FROM servers WHERE ip = :ip AND port = :port');
			$stmt->execute(['ip' => $ip, 'port' => $port]);
			return (int)$stmt->fetchColumn() > 0;
		} catch (PDOException $e) {
			error_log('Failed to check server existence: ' . $e->getMessage());
			return true;
		}
	}

	public function updateRconPassword(int $id, string $newPassword): bool
	{
		try {
			$stmt = $this->db->prepare('UPDATE servers SET rcon_password = :password WHERE id = :id');
			return $stmt->execute(['password' => $newPassword, 'id' => $id]);
		} catch (PDOException $e) {
			error_log('Failed to update RCON password: ' . $e->getMessage());
			return false;
		}
	}

	public function delete(int $id): bool
	{
		try {
			$stmt = $this->db->prepare('DELETE FROM servers WHERE id = :id');
			return $stmt->execute(['id' => $id]);
		} catch (PDOException $e) {
			error_log('Failed to delete server: ' . $e->getMessage());
			return false;
		}
	}
}
