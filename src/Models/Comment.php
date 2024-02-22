<?php
	
	namespace App\Models;
	
	class Comment {
		
		private ?int $id = null;
		private ?string $content = null;
		private ?bool $published = false;
		private ?\DateTime $createdAt = null;
		private ?\DateTime $updatedAt = null;
		private ?User $user = null;
		private ?Post $post = null;
		
		/**
		 * @param int|null $id
		 * @param string|null $content
		 * @param bool|null $published
		 * @param \DateTime|null $createdAt
		 * @param \DateTime|null $updatedAt
		 * @param User|null $user
		 * @param Post|null $post
		 */
		public function __construct(?int $id, ?string $content, ?bool $published, ?\DateTime $createdAt, ?\DateTime $updatedAt, ?User $user, ?Post $post)
		{
			$this->id = $id;
			$this->content = $content;
			$this->published = $published;
			$this->createdAt = $createdAt;
			$this->updatedAt = $updatedAt;
			$this->user = $user;
			$this->post = $post;
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
		 * @return \DateTime|null
		 */
		public function getCreatedAt(): ?\DateTime
		{
			return $this->createdAt;
		}
		
		/**
		 * @param \DateTime|null $createdAt
		 */
		public function setCreatedAt(?\DateTime $createdAt): void
		{
			$this->createdAt = $createdAt;
		}
		
		/**
		 * @return \DateTime|null
		 */
		public function getUpdatedAt(): ?\DateTime
		{
			return $this->updatedAt;
		}
		
		/**
		 * @param \DateTime|null $updatedAt
		 */
		public function setUpdatedAt(?\DateTime $updatedAt): void
		{
			$this->updatedAt = $updatedAt;
		}
		
		/**
		 * @return User|null
		 */
		public function getUser(): ?User
		{
			return $this->user;
		}
		
		/**
		 * @param User|null $user
		 */
		public function setUser(?User $user): void
		{
			$this->user = $user;
		}
		
		/**
		 * @return Post|null
		 */
		public function getPost(): ?Post
		{
			return $this->post;
		}
		
		/**
		 * @param Post|null $post
		 */
		public function setPost(?Post $post): void
		{
			$this->post = $post;
		}
		
		
	}