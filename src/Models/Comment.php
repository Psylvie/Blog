<?php
	namespace App\Models;
	
	use DateTime;
	
	class Comment
	{
		private int $id;
		private string $content;
		private int $published;
		private datetime $createdAt;
		private datetime $updateAt;
		
		/**
		 * @param int $id
		 * @param string $content
		 * @param int $published
		 * @param DateTime $createdAt
		 * @param DateTime $updateAt
		 */
		public function __construct(int $id, string $content, int $published, DateTime $createdAt, DateTime $updateAt)
		{
			$this->id = $id;
			$this->content = $content;
			$this->published = $published;
			$this->createdAt = $createdAt;
			$this->updateAt = $updateAt;
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
		 * @return int
		 */
		public function getPublished(): int
		{
			return $this->published;
		}
		
		/**
		 * @param int $published
		 */
		public function setPublished(int $published): void
		{
			$this->published = $published;
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
		
	}