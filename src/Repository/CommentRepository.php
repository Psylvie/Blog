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

    /**
     * CommentRepository constructor.
     */
    public function __construct()
    {
        $this->mysqlClient = DatabaseConnect::connect();
    }

    /**
     * Adds a comment to the database
     * @param string $commentContent content of the comment
     * @param int $postId id of the post
     * @param int $userId id of the user who wrote the comment
     * @throws Exception
     */
    public function addComment(string $commentContent, int $postId, int $userId): void
    {
        $sql = 'INSERT INTO comments (content, status, user_id, post_id) VALUES (:content, :status, :userId, :postId)';
        $statement = $this->mysqlClient->prepare($sql);
        $statement->bindParam(':content', $commentContent, PDO::PARAM_STR);
        $statement->bindValue(':status', 'pending', PDO::PARAM_STR);
        $statement->bindValue(':userId', $userId, PDO::PARAM_INT);
        $statement->bindValue(':postId', $postId, PDO::PARAM_INT);
        $statement->execute();
    }

    /**
     * Finds all comments by post id
     * @param int $postId id of the post
     * @throws Exception
     */
    public function findAllByPostId(int $postId): array
    {
        $sql = 'SELECT * FROM comments WHERE post_id = :post_id ORDER BY createdAt DESC';

        $statement = $this->mysqlClient->prepare($sql);
        $statement->execute(['post_id' => $postId]);
        $commentsData = $statement->fetchAll(PDO::FETCH_ASSOC);
        $comments = [];

        foreach ($commentsData as $commentData) {
            $createdAt = new DateTime($commentData['createdAt']);
            $updatedAt = new DateTime($commentData['updatedAt']);
            $comment = new Comment([
                'id' => $commentData['id'],
                'content' => $commentData['content'],
                'status' => $commentData['status'],
                'createdAt' => $createdAt,
                'updatedAt' => $updatedAt,
                'userId' => $commentData['user_id'],
                'postId' => $postId
            ]);
            $comments[] = $comment;
        }

        return $comments;
    }

    /**
     * Finds all comments by user id
     * @param int $commentId id of the comment
     * @param string $status status of the comment
     * @throws Exception
     */
    public function updateCommentStatus(int $commentId, string $status): void
    {

        $sql = 'UPDATE comments SET status = :status WHERE id = :commentId';
        $statement = $this->mysqlClient->prepare($sql);
        try {
            $statement->execute(['status' => $status, 'commentId' => $commentId]);
        } catch (\PDOException $e) {
            throw new Exception('Error while updating comment status');
        }
    }

    /**
     * Finds the status of a comment by its id
     * @param int $commentId id of the comment
     * @return string status of the comment
     */
    public function findStatusByComment(int $commentId): string
    {
        $sql = 'SELECT status FROM comments WHERE id = :commentId';
        $statement = $this->mysqlClient->prepare($sql);
        $statement->execute(['commentId' => $commentId]);
        $status = $statement->fetch(PDO::FETCH_ASSOC);
        return $status['status'];
    }

    /**
     * Finds all pending comments
     * @return array
     * @throws Exception
     */
    public function findAllPendingComments(): array
    {
        $sql = 'SELECT * FROM comments WHERE status = :status ORDER BY createdAt DESC';
        $statement = $this->mysqlClient->prepare($sql);
        $statement->execute(['status' => 'pending']);
        $commentsData = $statement->fetchAll(PDO::FETCH_ASSOC);
        $comments = [];

        foreach ($commentsData as $commentData) {
            $createdAt = new DateTime($commentData['createdAt']);
            $updatedAt = new DateTime($commentData['updatedAt']);
            $comment = new Comment([
                'id' => $commentData['id'],
                'content' => $commentData['content'],
                'status' => $commentData['status'],
                'createdAt' => $createdAt,
                'updatedAt' => $updatedAt,
                'userId' => $commentData['user_id'],
                'postId' => $commentData['post_id']
            ]);
            $comments[] = $comment;
        }

        return $comments;
    }

    /**
     * Deletes a comment by its id
     * @param int $postId
     * @return void
     */
    public function deleteCommentByPostId(int $postId): void
    {
        $sql = 'DELETE FROM comments WHERE post_id = :postId';
        $statement = $this->mysqlClient->prepare($sql);
        $statement->execute(['postId' => $postId]);
    }


    /**
     * Finds the user who wrote a comment
     * @param int $commentId
     * @return array|null
     */
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
	