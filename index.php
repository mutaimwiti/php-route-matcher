<?php

$regexMappers = [
    // replace {x with ([0-9a-zA-Z-]+)
    ['@(\{[0-9a-zA-Z-]+)+@', '([0-9a-zA-Z-]+)'],
    // remove }
    ['@\}+@', ''],
    // replace ?/ with ?/?
    ['@(\?/)+@', '?/?'],
    // make closing slash optional if it is followed by an optional argument
    ['@/\(\[0-9a-zA-Z-\]\+\)\?@', '/?([0-9a-zA-Z-]+)?'],
];

function prepareRouteRegex($route)
{
    global $regexMappers;

    $regex = $route;

    foreach ($regexMappers as $mapper) {
        $regex = preg_replace($mapper[0], $mapper[1], $regex);
    }

    return "@^$regex$@";
}

$routes = [
    (Object)[
        'pattern' => 'foo/{id}/bar/{name?}',
        'tests' => [
            'fo' => ['match' => false, 'values' => []],
            'foo/8/bar' => ['match' => true, 'values' => ['8', null]],
            'foo/8/bar/' => ['match' => true, 'values' => ['8', null]],
            'foo/8/bar/7' => ['match' => true, 'values' => ['8', '7']],
            'foo/32/bar/11/k' => ['match' => false, 'values' => []],
        ]
    ],
    (Object)[
        'pattern' => 'foo/{id?}/{name?}',
        'tests' => [
            'fo' => ['match' => false, 'values' => []],
            'foo' => ['match' => true, 'values' => [null, null]],
            'foo/' => ['match' => true, 'values' => [null, null]],
            'foo/8/7' => ['match' => true, 'values' => ['8', '7']],
            'foo/34/55/2' => ['match' => false, 'values' => []],
        ]
    ],
    (Object)[
        'pattern' => 'foo/{id}/{name?}/{col?}/',
        'tests' => [
            'fo' => ['match' => false, 'values' => []],
            'foo' => ['match' => false, 'values' => []],
            'foo/' => ['match' => false, 'values' => []],
            'foo/4' => ['match' => true, 'values' => ['4', null, null]],
            'foo/4/' => ['match' => true, 'values' => ['4', null, null]],
            'foo/4/55' => ['match' => true, 'values' => ['4', '55', null]],
            'foo/4/55/' => ['match' => true, 'values' => ['4', '55', null]],
            'foo/4/55/3' => ['match' => true, 'values' => ['4', '55', '3']],
            'foo/4/55/3/' => ['match' => true, 'values' => ['4', '55', '3']],
            'foo/4/55/3/7' => ['match' => false, 'values' => []],
        ]
    ],
];


foreach ($routes as $route) {
    echo "\n" . str_repeat('*', 54);

    $pattern = $route->pattern;

    echo "\nRoute: $pattern";

    $regex = prepareRouteRegex($pattern);

    echo "\nRegex: $regex";

    echo "\n" . str_repeat('-', 54);

    foreach ($route->tests as $test => $expected) {
        echo "\nURI: $test";

        $routeMatch = boolval(preg_match_all($regex, $test, $matches)) === $expected['match'];

        array_shift($matches);
        $values = [];

        foreach ($matches as $match) {
            if (count($match)) {
                $values[] = $match[0] == "" ? null : $match[0];
            }
        }

        $valueMatch = $values === $expected['values'];

        if ($routeMatch && $valueMatch) {
            echo "\nValues:\n";
            print_r($values);
            echo("Pass");
        } else {
            echo "\nFailure";
        }
        echo "\n" . str_repeat('=', 54);
    }
}
