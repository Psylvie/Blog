<?php
	namespace App\Controllers;
	require_once __DIR__ . '/../../vendor/autoload.php';
	require_once __DIR__ . '/../config/databaseConnect.php';
	use App\Config\DatabaseConnect;
	use DateTime;
	use PDO;
	
	
	class CommentController extends HomeController
	{
		public function addComment()
		{
			if ($_SERVER['REQUEST_METHOD'] === 'POST') {
				$commentContent = $_POST['comment'];
				$postId = $_POST['postId'];
				
				try {
					$mysqlClient = DatabaseConnect::connect();
					
					$sql = 'INSERT INTO comments (content, status, createdAt, updateAt) VALUES (:content, :status, :createdAt, :updateAt)';
					$statement = $mysqlClient->prepare($sql);
					$statement->bindParam(':content', $commentContent, PDO::PARAM_STR);
					$statement->bindValue(':status', 'pending', PDO::PARAM_STR);
					$createdAt = (new DateTime())->format('Y-m-d H:i:s');
					$updatedAt = $createdAt;
					$statement->bindValue(':createdAt', $createdAt, PDO::PARAM_STR);
					$statement->bindValue(':updateAt', $updatedAt, PDO::PARAM_STR);
					$statement->execute();
					$commentId = $mysqlClient->lastInsertId();
					
					$sql = 'INSERT INTO posts_comments (post_id, comment_id) VALUES (:post_id, :comment_id)';
					$statement = $mysqlClient->prepare($sql);
					$statement->bindParam(':post_id', $postId, PDO::PARAM_INT);
					$statement->bindValue(':comment_id', $commentId, PDO::PARAM_INT);
					$statement->execute();
					
					header('Location: /Blog/post/' . $postId);
					exit();
				} catch (\Exception $e) {
					echo 'Error: ' . $e->getMessage();
				}
			}
			
		}
	}