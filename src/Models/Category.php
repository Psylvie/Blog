<?php
	
	namespace App\Models;
	
	class Category
	{
		private ?int $id = null;
		private ?string $title = null;
		private ?string $slug = null;
		private ?string $image = null;
		private ?string $content = null;
		
		/**
		 * @param int|null $id
		 * @param string|null $title
		 * @param string|null $slug
		 * @param string|null $image
		 * @param string|null $content
		 */
		public function __construct(?int $id, ?string $title, ?string $slug, ?string $image, ?string $content)
		{
			$this->id = $id;
			$this->title = $title;
			$this->slug = $slug;
			$this->image = $image;
			$this->content = $content;
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
		public function getSlug(): ?string
		{
			return $this->slug;
		}
		
		/**
		 * @param string|null $slug
		 */
		public function setSlug(?string $slug): void
		{
			$this->slug = $slug;
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
		
		
	}
	
	