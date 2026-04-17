<?php
class Router {
    private $routes = [];
    
    public function add($route, $controller, $action, $method = "GET") {
        $this->routes[] = [
            "pattern" => $this->compilePattern($route),
            "controller" => $controller,
            "action" => $action,
            "method" => strtoupper($method)
        ];
    }
    
    private function compilePattern($route) {
        $route = preg_replace("/\//", "\\/", $route);
        $route = preg_replace("/\{([a-z]+)\}/", "(?P<$1>[^\/]+)", $route);
        return "/^" . $route . "$/";
    }
    
    public function dispatch($url, $method) {
        if (empty($url)) {
            $url = "/";
        }
        
        $method = strtoupper($method);
        
        foreach ($this->routes as $route) {
            if ($route["method"] !== $method && $route["method"] !== "GET") {
                continue;
            }
            
            if (preg_match($route["pattern"], $url, $matches)) {
                $params = [];
                foreach ($matches as $key => $value) {
                    if (is_string($key)) {
                        $params[$key] = $value;
                    }
                }
                
                $controllerName = $route["controller"];
                $actionName = $route["action"];
                
                if (class_exists($controllerName)) {
                    $controller = new $controllerName();
                    if (method_exists($controller, $actionName)) {
                        $controller->$actionName($params);
                        return;
                    }
                }
            }
        }
        
        header("HTTP/1.0 404 Not Found");
        echo "<h1>404 - Страница не найдена</h1>";
        echo "<p>URL: " . htmlspecialchars($url) . "</p>";
    }
}
?>