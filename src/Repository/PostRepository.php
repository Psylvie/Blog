<?php
	
	namespace App\Repository;
	
	use App\Config\DatabaseConnect;
	use App\Models\Comment;
	use App\Models\Post;
	use DateTime;
	use PDO;
	use PDOException;
	
	class PostRepository
	{
		private PDO $mysqlClient;
		
		public function __construct()
		{
			$this->mysqlClient = DatabaseConnect::connect();
		}
		
		/**
		 * @throws \Exception
		 */
		public function findLatestPosts(int $limit = 3): array
		{
			$sql = 'SELECT * FROM posts ORDER BY createdAt DESC LIMIT :limit';
			$statement = $this->mysqlClient->prepare($sql);
			$statement->bindParam(':limit', $limit, PDO::PARAM_INT);
			$statement->execute();
			$postsData = $statement->fetchAll(PDO::FETCH_ASSOC);
			$posts = [];
			
			foreach ($postsData as $postData) {
				$userId = $postData['user_id'] ?? null;
				$published = $postData['published'] ?? null;
				
				$createdAt = new DateTime($postData['createdAt']);
				$post = new Post(
					$postData['id'],
					$postData['title'],
					$postData['chapo'],
					$postData['author'],
					$postData['content'],
					$postData['image'],
					$userId,
					$published,
					$createdAt,
					new DateTime($postData['updateAt']),
					[]
				);
				$posts[] = $post;
			}
			
			return $posts;
		}
		
		/**
		 * @param int|null $limit
		 * @return array
		 * @throws \Exception
		 */
		public function getAllPosts(?int $limit = null): array
		{
			$sql = 'SELECT p.*, c.id AS comment_id, c.content AS comment_content, c.status AS comment_status,
                   c.createdAt AS comment_created_at, c.updateAt AS comment_updated_at, c.user_id AS comment_user_id
            FROM posts p
            LEFT JOIN posts_comments pc ON p.id = pc.post_id
            LEFT JOIN comments c ON pc.comment_id = c.id
            ORDER BY p.createdAt DESC';
			
			if ($limit !== null) {
				$sql .= ' LIMIT :limit';
			}
			$statement = $this->mysqlClient->prepare($sql);
			if ($limit !== null) {
				$statement->bindParam(':limit', $limit, PDO::PARAM_INT);
			}
			$statement->execute();
			$postsData = $statement->fetchAll(PDO::FETCH_ASSOC);
			$posts = [];
			
			foreach ($postsData as $postData) {
				$postId = $postData['id'];
				if (!isset($posts[$postId])) {
					$createdAt = str_replace('/', '-', $postData['createdAt']);
					$createdAtDateTime = new DateTime($createdAt);
					$posts[$postId] = new Post(
						$postId,
						$postData['title'],
						$postData['chapo'],
						$postData['author'],
						$postData['content'],
						$postData['image'],
						$postData['user_id'],
						$postData['published'],
						$createdAtDateTime,
						new DateTime($postData['updateAt']),
						[]
					);
				}
				
				if (!empty($postData['comment_id'])) {
					$commentCreatedAt = new DateTime($postData['comment_created_at']);
					$commentUpdatedAt = new DateTime($postData['comment_updated_at']);
					$comment = new Comment(
						$postData['comment_id'],
						$postData['comment_content'],
						$postData['comment_status'],
						$commentCreatedAt,
						$commentUpdatedAt,
						$postData['comment_user_id']
					);
					$posts[$postId]->addComment($comment);
				}
			}
			
			return array_values($posts);
		}
		
		
		/**
		 * @throws \Exception
		 */
		public function getPostById(int $postId): ?Post
		{
			$sql = 'SELECT p.*,
                   c.id AS comment_id, c.content AS comment_content, c.status AS comment_status,
                   c.createdAt AS comment_created_at, c.updateAt AS comment_updated_at, c.user_id AS comment_user_id
            FROM posts p
            LEFT JOIN posts_comments pc ON p.id = pc.post_id
            LEFT JOIN comments c ON pc.comment_id = c.id
            WHERE p.id = :postId';
			
			$statement = $this->mysqlClient->prepare($sql);
			$statement->bindParam(':postId', $postId, PDO::PARAM_INT);
			$statement->execute();
			$postData = $statement->fetchAll(PDO::FETCH_ASSOC);
			if (empty($postData)) {
				return null;
			}
			$post = null;
			foreach ($postData as $data) {
				if ($post === null) {
					$createdAt = str_replace('/', '-', $data['createdAt']);
					$createdAtDateTime = new DateTime($createdAt);
					$post = new Post(
						$data['id'],
						$data['title'],
						$data['chapo'],
						$data['author'],
						$data['content'],
						$data['image'],
						$data['user_id'],
						$data['published'],
						$createdAtDateTime,
						new DateTime($data['updateAt']),
						[]
					);
				}
				if (!empty($data['comment_id']) && $data['comment_status'] === 'approved') {
					$commentCreatedAt = new DateTime($data['comment_created_at']);
					$commentUpdatedAt = new DateTime($data['comment_updated_at']);
					$comment = new Comment(
						$data['comment_id'],
						$data['comment_content'],
						$data['comment_status'],
						$commentCreatedAt,
						$commentUpdatedAt,
						$data['comment_user_id']
					);
					$post->addComment($comment);
				}
			}
			return $post;
		}
		/**
		 * @throws \Exception
		 */
		public function createPost($title, $chapo, $author, $content, $image, $userId, $published): void
		{
			try {
				$stmt = $this->mysqlClient->prepare("INSERT INTO posts (title, chapo, author, content, image, user_id, published) VALUES (?, ?, ?, ?, ?, ?, ?)");
				$stmt->execute([$title, $chapo, $author, $content, $image, $userId, $published]);
			} catch (PDOException $e) {
				throw new \Exception("Erreur lors de la création du post : " . $e->getMessage());
			}
		}
		
		/**
		 * @throws \Exception
		 */
		public function deletePost($postId): void
		{
			try {
				$stmt = $this->mysqlClient->prepare("DELETE FROM posts WHERE id = ?");
				$stmt->execute([$postId]);
			} catch (PDOException $e) {
				throw new \Exception("Erreur lors de la suppression du post : " . $e->getMessage());
			}
		}
		
		public function updatePost($postId, $title, $chapo, $author, $content): void
		{
			try {
				$sql = "UPDATE posts SET title = :title, chapo = :chapo, author = :author, content = :content WHERE id = :id";
				$stmt = $this->mysqlClient->prepare($sql);
				$stmt->bindParam(':title', $title, PDO::PARAM_STR);
				$stmt->bindParam(':chapo', $chapo, PDO::PARAM_STR);
				$stmt->bindParam(':author', $author, PDO::PARAM_STR);
				$stmt->bindParam(':content', $content, PDO::PARAM_STR);
				$stmt->bindParam(':id', $postId, PDO::PARAM_INT);
				$stmt->execute();
			} catch (PDOException $e) {
				throw new PDOException("Erreur lors de la mise à jour du post : " . $e->getMessage());
			}
		}
	}
	