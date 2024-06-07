<?php

namespace App\Repository;

use App\Config\DatabaseConnect;
use App\Models\Comment;
use DateTime;
use Exception;
use PDO;

class CommentRepository
{
    private PDO $mysqlClient;

    public function __construct()
    {
        $this->mysqlClient = DatabaseConnect::connect();
    }

    public function addComment(string $commentContent, int $postId, int $userId): void
    {
        $sql = 'INSERT INTO comments (content, status, createdAt, updateAt, user_id, post_id) VALUES (:content, :status, :createdAt, :updateAt, :userId, :postId)';
        $statement = $this->mysqlClient->prepare($sql);
        $statement->bindParam(':content', $commentContent, PDO::PARAM_STR);
        $statement->bindValue(':status', 'pending', PDO::PARAM_STR);
        $statement->bindValue(':userId', $userId, PDO::PARAM_INT);
        $statement->bindValue(':postId', $postId, PDO::PARAM_INT);
        $createdAt = (new DateTime())->format('Y-m-d H:i:s');
        $updatedAt = $createdAt;
        $statement->bindValue(':createdAt', $createdAt, PDO::PARAM_STR);
        $statement->bindValue(':updateAt', $updatedAt, PDO::PARAM_STR);
        $statement->execute();
    }

    /**
     * @throws Exception
     */
    public function findAllByPostId(int $postId): array
    {
        $sql = 'SELECT * FROM comments WHERE post_id = :post_id';

        $statement = $this->mysqlClient->prepare($sql);
        $statement->execute(['post_id' => $postId]);
        $commentsData = $statement->fetchAll(PDO::FETCH_ASSOC);
        $comments = [];

        foreach ($commentsData as $commentData) {
            $createdAt = new DateTime($commentData['createdAt']);
            $updatedAt = new DateTime($commentData['updateAt']);
            $comment = new Comment(
                $commentData['id'],
                $commentData['content'],
                $commentData['status'],
                $createdAt,
                $updatedAt,
                $commentData['user_id'],
                $postId
            );
            $comments[] = $comment;
        }

        return $comments;
    }

    public function updateCommentStatus(int $commentId, string $status): void
    {

        $sql = 'UPDATE comments SET status = :status WHERE id = :commentId';
        $statement = $this->mysqlClient->prepare($sql);
        try {
            $statement->execute(['status' => $status, 'commentId' => $commentId]);
        } catch (\PDOException $e) {
            echo 'Error: ' . $e->getMessage();
        }
    }

    public function findStatusByComment(int $commentId): string
    {
        $sql = 'SELECT status FROM comments WHERE id = :commentId';
        $statement = $this->mysqlClient->prepare($sql);
        $statement->execute(['commentId' => $commentId]);
        $status = $statement->fetch(PDO::FETCH_ASSOC);
        return $status['status'];
    }

    public function deleteCommentByPostId(int $postId): void
    {
        $sql = 'DELETE FROM comments WHERE post_id = :postId';
        $statement = $this->mysqlClient->prepare($sql);
        $statement->execute(['postId' => $postId]);
    }


    public function findUserByComment(int $commentId): ?array
    {
        $sql = "SELECT users.* FROM users
            JOIN comments ON users.id = comments.user_id
            WHERE comments.id = :commentId";

        $statement = $this->mysqlClient->prepare($sql);
        $statement->execute(['commentId' => $commentId]);
        $user = $statement->fetch(PDO::FETCH_ASSOC);

        return $user ?: null;
    }
}
	