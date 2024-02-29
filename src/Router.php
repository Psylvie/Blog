<?php
	
	namespace App;
	
	class Router {
		protected $routes = [];
		
		public function addRoute($route, $controller, $action): void
		{
			$this->routes[$route] = [
				'controller' => $controller,
				'action' => $action
			];
		}
		
		/**
		 * @throws \Exception
		 */
		public function dispatch($uri): void
		{
			if (array_key_exists($uri, $this->routes)) {
				$controller = $this->routes[$uri]['controller'];
				$action = $this->routes[$uri]['action'];
				$controller = new $controller;
				$controller->$action();
			} else {
				throw new \Exception('No route defined for this URI: ' . $uri);
			}
		}
	}


