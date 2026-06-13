<?php

class Router
{
    private array $routes = [];

    public function get(string $pattern, callable $handler): void
    {
        $this->routes['GET'][$pattern] = $handler;
    }

    public function post(string $pattern, callable $handler): void
    {
        $this->routes['POST'][$pattern] = $handler;
    }

    public function dispatch(string $method, string $uri): void
    {
        $uri = parse_url($uri, PHP_URL_PATH);
        $uri = rtrim($uri, '/') ?: '/';

        if (!isset($this->routes[$method])) {
            $this->notFound();
            return;
        }

        foreach ($this->routes[$method] as $pattern => $handler) {
            $regex = $this->patternToRegex($pattern);

            if (preg_match($regex, $uri, $matches)) {
                $params = array_filter($matches, 'is_string', ARRAY_FILTER_USE_KEY);
                call_user_func($handler, $params);
                return;
            }
        }

        $this->notFound();
    }

    private function patternToRegex(string $pattern): string
    {
        $regex = preg_replace('/\{(\w+)\}/', '(?P<$1>[^/]+)', $pattern);
        return '#^' . $regex . '$#';
    }

    public static function render(string $view, array $data = [], ?string $layout = null): void
    {
        extract($data);

        ob_start();
        require __DIR__ . "/Views/{$view}.php";
        $content = ob_get_clean();

        if ($layout === null) {
            echo $content;
            return;
        }

        require __DIR__ . "/Views/layouts/{$layout}.php";
    }

    public static function redirect(string $path): void
    {
        header('Location: ' . self::url($path));
        exit;
    }

    public static function url(string $path): string
    {
        return $path;
    }

    private function notFound(): void
    {
        http_response_code(404);
        self::render('errors/404', [], 'main');
    }
}
