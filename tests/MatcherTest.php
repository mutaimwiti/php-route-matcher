<?php

namespace Tests;

use App\Matcher;
use PHPUnit\Framework\TestCase;

class MatcherTest extends TestCase
{
    protected $routes = [
        'foo/{id}/bar/{name?}',
        'bar/{id?}/{name?}',
        'baz/{id}/{name?}/{col?}/',
    ];

    /* @var Matcher */
    protected $matcher;

    protected function setUp(): void
    {
        parent::setUp();

        $this->matcher = new Matcher();
    }

    /** @test */
    function test_case_1()
    {
        // foo/{id}/bar/{name?}
        $this->assertEquals(false, $this->matcher->match($this->routes, 'fo'));
        $this->assertEquals(false, $this->matcher->match($this->routes, 'foo'));
        $this->assertEquals(['8', null], $this->matcher->match($this->routes, 'foo/8/bar'));
        $this->assertEquals(['8', null], $this->matcher->match($this->routes, 'foo/8/bar/'));
        $this->assertEquals(['8', '7'], $this->matcher->match($this->routes, 'foo/8/bar/7'));
        $this->assertEquals(false, $this->matcher->match($this->routes, 'foo/32/bar/11/k'));
    }

    /** @test */
    function test_case_2()
    {
        // foo/{id?}/{name?}
        $this->assertEquals(false, $this->matcher->match($this->routes, 'ba'));
        $this->assertEquals([null, null], $this->matcher->match($this->routes, 'bar'));
        $this->assertEquals([null, null], $this->matcher->match($this->routes, 'bar/'));
        $this->assertEquals(['8', '7'], $this->matcher->match($this->routes, 'bar/8/7'));
        $this->assertEquals(false, $this->matcher->match($this->routes, 'bar/34/55/2'));
    }

    /** @test */
    function test_case_3()
    {
        // foo/{id}/{name?}/{col?}/
        $this->assertEquals(false, $this->matcher->match($this->routes, 'ba'));
        $this->assertEquals(false, $this->matcher->match($this->routes, 'baz'));
        $this->assertEquals(false, $this->matcher->match($this->routes, 'baz/'));
        $this->assertEquals(['4', null, null], $this->matcher->match($this->routes, 'baz/4'));
        $this->assertEquals(['4', null, null], $this->matcher->match($this->routes, 'baz/4/'));
        $this->assertEquals(['4', '55', null], $this->matcher->match($this->routes, 'baz/4/55'));
        $this->assertEquals(['4', '55', null], $this->matcher->match($this->routes, 'baz/4/55/'));
        $this->assertEquals(['4', '55', '3'], $this->matcher->match($this->routes, 'baz/4/55/3'));
        $this->assertEquals(['4', '55', '3'], $this->matcher->match($this->routes, 'baz/4/55/3/'));
        $this->assertEquals(false, $this->matcher->match($this->routes, 'baz/4/55/7/1'));
    }
}
