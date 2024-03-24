<?php
	
	namespace App;
	
	class Router {
		//tableau pr stocker routes
		protected array $routes = [];
		
		//methode pr ajouter une nouvelle route
		public function addRoute($route, $controller, $action): void
		{
			//stocker controller et action dans le tableau des routes
			$this->routes[$route] = [
				'Controllers' => $controller,
				'action' => $action
			];
		}
		
		/**
		 * @throws \Exception
		 */
		public function dispatch($uri): void
		{
			// verifier si la route a ete definie
			$found = false;
			// boucle pr parcourir toutes  les routes
			foreach ($this->routes as $route => $config) {
				// remplace la partie dynamique de l'URI par un pattern
				$pattern = str_replace('/', '\/', $route);
				$pattern = preg_replace('/{[^\/]+}/', '([^\/]+)', $pattern);
				$pattern = "/^$pattern$/";
				
				// verifie si l'URI correspond au pattern
				if (preg_match($pattern, $uri, $matches)) {
					// si oui, on extrait le controller et l'action
					$controller = $config['Controllers'];
					$action = $config['action'];
					
					//si des parametres sont presents dans l'URI
					if (count($matches) > 1) {
						// on extrait les parametres de l'URI
						$params = array_slice($matches, 1);
						$controller = new $controller;
						call_user_func_array([$controller, $action], $params);
					} else {
						// si pas de parametres, on appelle l'action sans parametres
						$controller = new $controller;
						$controller->$action();
					}
					
					$found = true;
					break;
				}
			}
			//si aucune route n'a ete trouvee lance exception
			if (!$found) {
				throw new \Exception('No route defined for this URI: ' . $uri);
			}
		}
	}


