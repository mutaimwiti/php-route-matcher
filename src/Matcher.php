<?php

namespace App;

class Matcher
{
    public function match($routes, $uri)
    {
        $routes = $this->manyToRegex($routes);

        foreach ($routes as $route) {

            if (preg_match_all($route, $uri, $matches)) {
                array_shift($matches); // remove full route match
                $values = [];

                foreach ($matches as $match) {
                    if (count($match)) {
                        $values[] = $match[0] == "" ? null : $match[0];
                    }
                }

                return $values;
            }
        }

        return false;
    }

    protected function manyToRegex($routes)
    {
        $routePatterns = [];

        foreach ($routes as $route) {
            $routePatterns[] = $this->prepareRouteRegex($route);
        }

        return $routePatterns;
    }

    protected $regexMappers = [
        // replace {x with ([0-9a-zA-Z-]+)
        ['@(\{[0-9a-zA-Z-]+)+@', '([0-9a-zA-Z-]+)'],
        // remove }
        ['@\}+@', ''],
        // replace ?/ with ?/?
        ['@(\?/)+@', '?/?'],
        // make closing slash optional if it is followed by an optional argument
        ['@/\(\[0-9a-zA-Z-\]\+\)\?@', '/?([0-9a-zA-Z-]+)?'],
    ];

    protected function prepareRouteRegex($route)
    {
        $regex = $route;

        foreach ($this->regexMappers as $mapper) {
            $regex = preg_replace($mapper[0], $mapper[1], $regex);
        }

        return "@^$regex$@";
    }
}
