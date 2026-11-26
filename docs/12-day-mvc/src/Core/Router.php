<?php

declare(strict_types=1);

namespace App\Core;

final class Router
{
    /** @var array<int, array{method:string, pattern:string, regex:string, handler:callable|array}> */
    private array $routes = [];

    public function get(string $path, callable|array $handler): void
    {
        $this->add('GET', $path, $handler);
    }

    public function add(string $method, string $path, callable|array $handler): void
    {
        $quoted = preg_quote($path, '#');
        $regex = preg_replace_callback('/\\\\\{([a-zA-Z][a-zA-Z0-9_]*)\\\\\}/', static fn (array $m): string => '(?P<' . $m[1] . '>[^/]+)', $quoted);
        $this->routes[] = [
            'method' => strtoupper($method),
            'pattern' => $path,
            'regex' => '#^' . $regex . '/?$#',
            'handler' => $handler,
        ];
    }

    public function dispatch(string $method, string $uri): void
    {
        $path = parse_url($uri, PHP_URL_PATH);
        $path = is_string($path) && $path !== '' ? $path : '/';
        $method = strtoupper($method);
        $pathMatched = false;

        foreach ($this->routes as $route) {
            if (preg_match($route['regex'], $path, $matches) !== 1) {
                continue;
            }

            $pathMatched = true;
            if ($route['method'] !== $method) {
                continue;
            }

            $params = [];
            foreach ($matches as $key => $value) {
                if (is_string($key)) {
                    $params[$key] = rawurldecode($value);
                }
            }

            $this->invoke($route['handler'], $params);
            return;
        }

        if ($pathMatched) {
            Response::error(405, '405 — Method Not Allowed', 'Ин барои ин масир иҷозат дода нашудааст.', ['Allow' => 'GET']);
            return;
        }

        Response::error(404, '404 — Page Not Found', 'Саҳифаи дархостшуда ёфт нашуд.');
    }

    /** @param callable|array{class-string, string} $handler */
    private function invoke(callable|array $handler, array $params): void
    {
        if (is_array($handler) && is_string($handler[0])) {
            $controller = new $handler[0]();
            $method = $handler[1];
            $controller->{$method}(...array_values($params));
            return;
        }

        $handler(...array_values($params));
    }
}
