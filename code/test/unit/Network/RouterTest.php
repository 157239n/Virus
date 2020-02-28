<?php


use Kelvinho\Virus\Network\Router;
use PHPUnit\Framework\TestCase;

class RouterTest extends TestCase {
    public function testGet() {
        $requestData = $this->createMock("\Kelvinho\Virus\Network\RequestData");
        $requestData->method("getExplodedPath")->willReturn(["routeA"]);
        $requestData->method("getRequestMethod")->willReturn("GET");
        $requestData->method("rightHost")->willReturn(true);
        $router = new Router($requestData);
        $router->get("routeA", function () {
            throw new Exception();
        });
        $router->get("routeB", function() {
        });
        try {
            $router->run();
            $this->fail();
        } catch (Exception $exception) {
            $this->assertTrue(true);
        }
    }
}
