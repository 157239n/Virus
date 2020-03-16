<?php

use Kelvinho\Virus\User\UserFactoryImp;
use PHPUnit\Framework\TestCase;

class UserFactoryImpTest extends TestCase {
    public function testExists() {
        $mysqli_result = $this->createMock("\mysqli_result");
        $mysqli_result->method("fetch_assoc")->willReturn(array("user_handle" => "user_A"));
        $mysqli = $this->createMock("\mysqli");
        $mysqli->method("query")->willReturn($mysqli_result);
        $usageFactory = $this->createMock("\Kelvinho\Virus\Usage\UsageFactory");
        $userFactory = new UserFactoryImp($mysqli, $usageFactory);
        $this->assertTrue($userFactory->exists("user_A"));
    }
}