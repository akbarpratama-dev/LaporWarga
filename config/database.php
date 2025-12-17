<?php
class Database {
	private $host = 'localhost';
	private $db_name = 'LaporWarga2';
	private $username = 'root';
	private $password = 'Root123';
	private $conn;

	public function getConnection() {
		$this->conn = null;
		try {
			$dsn = "mysql:host={$this->host};dbname={$this->db_name};charset=utf8mb4";
			$this->conn = new PDO($dsn, $this->username, $this->password, [
				PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
				PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
			]);
		} catch (PDOException $e) {
			error_log('DB connection error: ' . $e->getMessage());
		}
		return $this->conn;
	}
}
?>
