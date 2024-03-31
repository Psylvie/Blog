<?php
	
	namespace App\Repository;
	
	use App\Config\DatabaseConnect;
	use App\Models\Comment;
	use App\Models\Post;
	use DateTime;
	use PDO;
	
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
				$userId = isset($postData['user_id']) ? $postData['user_id'] : null;
				$published = isset($postData['published']) ? $postData['published'] : null;
				
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
                   c.createdAt AS comment_created_at, c.updateAt AS comment_updated_at
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
						$commentUpdatedAt
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
                   c.createdAt AS comment_created_at, c.updateAt AS comment_updated_at
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
				if (!empty($data['comment_id'])) {
					$commentCreatedAt = new DateTime($data['comment_created_at']);
					$commentUpdatedAt = new DateTime($data['comment_updated_at']);
					$comment = new Comment(
						$data['comment_id'],
						$data['comment_content'],
						$data['comment_status'],
						$commentCreatedAt,
						$commentUpdatedAt
					);
					$post->addComment($comment);
				}
			}
			return $post;
		}
	}
	