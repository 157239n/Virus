<?php

use Kelvinho\Virus\Network\Route;
use PHPUnit\Framework\TestCase;

class RouteTest extends TestCase {
    public function testMatches1() {
        $route = new Route("routeA", "POST", function () {
        });
        $this->assertTrue($route->matches(["routeA"], "POST"));
    }

    public function testMatches2() {
        $route = new Route("routeA/", "POST", function () {
        });
        $this->assertTrue($route->matches(["routeA"], "POST"));
    }

    public function testMatches3() {
        $route = new Route("*", "GET", function() {
        });
        $this->assertTrue($route->matches(["anything"], "GET"));
    }

    public function testMatches4() {
        $route = new Route("/*/", "GET", function() {
        });
        $this->assertTrue($route->matches(["anything"], "GET"));
    }

    public function testMatches5() {
        $route = new Route("*", "POST", function() {
        });
        $this->assertFalse($route->matches(["anything"], "GET"));
    }

    public function testMatches6() {
        $route = new Route("a/*/b", "POST", function() {
        });
        $this->assertTrue($route->matches(["a", "456", "b"], "POST"));
    }

    public function testRun() {
        try {
            $route = new Route("*", "POST", function() {
                throw new Exception();
            });
            $route->run();
            $this->fail();
        } catch (Exception $e) {
            $this->assertTrue(true);
        }
    }
}