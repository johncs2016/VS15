<?php

class Article {
	public function fetch_all() {
		global $pdo;

		$query = $pdo->prepare('SELECT * FROM articles');
		$query->execute();

		return $query->fetchAll(PDO::FETCH_OBJ);
	}

	public function is_last_article($id) {
		global $pdo;

		$query = $pdo->prepare('SELECT count(article_id) FROM articles');
		$query->execute();

		$articles = $query->fetchColumn();

		return $id >= $articles;
	}

	public function add($title, $content) {
		global $pdo;

		$query = $pdo->prepare('INSERT INTO articles (article_title, article_content, article_timestamp) VALUES (?, ?, ?)');

		return $query->execute(array(
			$title,
			$content,
			time()
		));
	}

	public function update($id, $title, $content) {
		global $pdo;

		$query = $pdo->prepare('UPDATE articles SET article_title = ?, article_content = ? WHERE article_id = ?');
		return $query->execute(array($title, $content, $id));
	}

	public function delete($id) {
		global $pdo;

		$query = $pdo->prepare('DELETE FROM articles WHERE article_id = ?');
		return $query->execute(array($id));
	}
}

?>