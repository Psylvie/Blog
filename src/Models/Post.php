<?php

namespace App\Models;

use DateTime;
/**
 * Class Post
 * @package App\Models
 */
class Post
{
    private int $id;
    private string $title;
    private string $chapo;
    private string $author;
    private string $content;
    private ?string $image;
    private int $user_id;
    private bool $published;
    private DateTime $createdAt;
    private \DateTime $updatedAt;
    private ?array $comments;

    /**
     * @param int|null $id
     * @param string|null $title
     * @param string|null $chapo
     * @param string|null $author
     * @param string|null $content
     * @param string|null $image
     * @param int|null $user_id
     * @param bool|null $published
     * @param DateTime|null $createdAt
     * @param DateTime|null $updatedAt
     * @param array|null $comments array
     */
    public function __construct(?int $id, ?string $title, ?string $chapo, ?string $author, ?string $content, ?string $image, ?int $user_id, ?bool $published, ?DateTime $createdAt, ?DateTime $updatedAt, ?array $comments = [])
    {
        $this->id = $id;
        $this->title = $title;
        $this->chapo = $chapo;
        $this->author = $author;
        $this->content = $content;
        $this->image = $image;
        $this->user_id = $user_id;
        $this->published = $published;
        $this->createdAt = $createdAt;
        $this->updatedAt = $updatedAt;
        $this->comments = [];
    }

    /**
     * @return int|null
     */
    public function getId(): ?int
    {
        return $this->id;
    }

    /**
     * @param int|null $id
     */
    public function setId(?int $id): void
    {
        $this->id = $id;
    }

    /**
     * @param Comment $comment array
     */
    public function addComment(Comment $comment): void
    {
        $this->comments[] = $comment;
    }

    public function getComments(): array
    {
        return $this->comments;
    }

    /**
     * @return string|null
     */
    public function getTitle(): ?string
    {
        return $this->title;
    }

    /**
     * @param string|null $title
     */
    public function setTitle(?string $title): void
    {
        $this->title = $title;
    }

    /**
     * @return string|null
     */
    public function getChapo(): ?string
    {
        return $this->chapo;
    }

    /**
     * @param string|null $chapo
     */
    public function setChapo(?string $chapo): void
    {
        $this->chapo = $chapo;
    }

    /**
     * @return string|null
     */
    public function getAuthor(): ?string
    {
        return $this->author;
    }

    /**
     * @param string|null $author
     */
    public function setAuthor(?string $author): void
    {
        $this->author = $author;
    }

    /**
     * @return string|null
     */
    public function getContent(): ?string
    {
        return $this->content;
    }

    /**
     * @param string|null $content
     */
    public function setContent(?string $content): void
    {
        $this->content = $content;
    }

    /**
     * @return string|null
     */
    public function getImage(): ?string
    {
        return $this->image;
    }

    /**
     * @param string|null $image
     */
    public function setImage(?string $image): void
    {
        $this->image = $image;
    }

    /**
     * @return int|null
     */
    public function getUserId(): ?int
    {
        return $this->user_id;
    }

    /**
     * @param int|null $user_id
     */
    public function setUserId(?int $user_id): void
    {
        $this->user_id = $user_id;
    }

    /**
     * @return bool|null
     */
    public function getPublished(): ?bool
    {
        return $this->published;
    }

    /**
     * @param bool|null $published
     */
    public function setPublished(?bool $published): void
    {
        $this->published = $published;
    }

    /**
     * @return DateTime|null
     */
    public function getCreatedAt(): ?DateTime
    {
        return $this->createdAt;
    }

    /**
     * @param DateTime|null $createdAt
     */
    public function setCreatedAt(?DateTime $createdAt): void
    {
        $this->createdAt = $createdAt;
    }

    /**
     * @return DateTime|null
     */
    public function getUpdatedAt(): ?DateTime
    {
        return $this->updatedAt;
    }

    /**
     * @param DateTime|null $updatedAt
     */
    public function setUpdatedAt(?DateTime $updatedAt): void
    {
        $this->updatedAt = $updatedAt;
    }

}
	