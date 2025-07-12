<?php

declare(strict_types=1);

namespace App\Config;

use PDO;
use PDOException;

class Database
{
	private static ?PDO $connection = null;

	public static function getConnection(): PDO
	{
		if (self::$connection === null) {
			try {
				$dsn = sprintf(
					'mysql:host=%s;dbname=%s;charset=utf8mb4',
					$_ENV['DB_HOST'],
					$_ENV['DB_NAME']
				);

				self::$connection = new PDO(
					$dsn,
					$_ENV['DB_USER'],
					$_ENV['DB_PASSWORD'],
					[
						PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
						PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
						PDO::ATTR_EMULATE_PREPARES => false,
						PDO::MYSQL_ATTR_INIT_COMMAND => "SET NAMES utf8mb4"
					]
				);
			} catch (PDOException $e) {
				error_log('Database connection failed: ' . $e->getMessage());
				throw new PDOException('Database connection failed');
			}
		}

		return self::$connection;
	}

	public static function closeConnection(): void
	{
		self::$connection = null;
	}
}
