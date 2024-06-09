<?php

namespace App\Models;


/**
 * Class Comment
 * @package App\Models
 */
class Comment extends EntityModel
{
    private int $id;
    private string $content;
    private string $status;
    private ?int $userId;
    private ?int $postId;

    /**
     * Comment constructor.
     * @param array|null $data
     */

    public function __construct(?array $data = [])
    {
        parent::__construct($data);
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