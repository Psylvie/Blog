<?php

namespace App\Models;

use DateTime;

/**
 * Class Comment
 * @package App\Models
 */
class Comment
{
    private int $id;
    private string $content;
    private string $status;
    private datetime $createdAt;
    private datetime $updateAt;
    private ?int $userId;
    private ?int $postId;

    /**
     * @param int $id
     * @param string $content
     * @param string $status
     * @param DateTime $createdAt
     * @param DateTime $updateAt
     * @param ?int $userId
     * @param ?int $postId
     */

    public function __construct(int $id, string $content, string $status, DateTime $createdAt, DateTime $updateAt, ?int $userId, ?int $postId)
    {
        $this->id = $id;
        $this->content = $content;
        $this->status = $status;
        $this->createdAt = $createdAt;
        $this->updateAt = $updateAt;
        $this->userId = $userId;
        $this->postId = $postId;
    }

    /**
     * @return int
     */
    public function getId(): int
    {
        return $this->id;
    }

    /**
     * @param int $id
     */
    public function setId(int $id): void
    {
        $this->id = $id;
    }

    /**
     * @return string
     */
    public function getContent(): string
    {
        return $this->content;
    }

    /**
     * @param string $content
     */
    public function setContent(string $content): void
    {
        $this->content = $content;
    }

    /**
     * @return string
     */
    public function getStatus(): string
    {
        return $this->status;
    }

    /**
     * @param string $status
     */
    public function setStatus(string $status): void
    {
        $this->status = $status;
    }

    /**
     * @return DateTime
     */
    public function getCreatedAt(): DateTime
    {
        return $this->createdAt;
    }

    /**
     * @param DateTime $createdAt
     */
    public function setCreatedAt(DateTime $createdAt): void
    {
        $this->createdAt = $createdAt;
    }

    /**
     * @return DateTime
     */
    public function getUpdateAt(): DateTime
    {
        return $this->updateAt;
    }

    /**
     * @param DateTime $updateAt
     */
    public function setUpdateAt(DateTime $updateAt): void
    {
        $this->updateAt = $updateAt;
    }

    /**
     * @return int|null
     */
    public function getUserId(): ?int
    {
        return $this->userId;
    }

    /**
     * @param int|null $userId
     */
    public function setUserId(?int $userId): void
    {
        $this->userId = $userId;
    }

    /**
     * @return int|null
     */
    public function getPostId(): ?int
    {
        return $this->postId;
    }

    /**
     * @param int|null $postId
     */
    public function setPostId(?int $postId): void
    {
        $this->postId = $postId;
    }
}