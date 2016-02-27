<?php

class User {
	public function fetch_all() {
		global $pdo;

		$query = $pdo->prepare('SELECT * FROM users');
		$query->execute();

		return $query->fetchAll(PDO::FETCH_OBJ);
	}

	public function check_login($username, $password) {
		global $pdo;

		$query = $pdo->prepare('SELECT count(user_id) FROM users WHERE username = ? AND password = ?');

		$query->execute(array(
			$username,
			$password
		));

		return $query->fetchColumn() == 1;
	}

	public function username() {
		return isset($_SESSION['username']) ? $_SESSION['username'] : 'invalid';
	}

	public function delete($user_id) {
		global $pdo;

		$query = $pdo->prepare('DELETE FROM users WHERE user_id = ?');

		return $query->execute(array(
			$user_id
		));
	}

	public function user_exists($username) {
		global $pdo;

		$query = $pdo->prepare('SELECT count(user_id) FROM users WHERE username = ?');

		$query->execute(array(
			$username
		));

		return $query->fetchColumn() == 1;
	}

	public function add($username, $password) {
		global $pdo;

		$query = $pdo->prepare('INSERT INTO users (username, password) VALUES (?, ?)');

		return $query->execute(array(
			$username,
			$password
		));
	}
}

?>