<?php

use Kelvinho\Virus\User\User;
use PHPUnit\Framework\TestCase;

class UserTest extends TestCase {
    /** @var User */
    private User $user;

    public function setUp(): void {
        $mysqli_answer = $this->createMock("\mysqli_result");
        $mysqli_answer->method("fetch_assoc")->willReturn(array("name" => "Ynes", "timezone" => -5, "hold" => 1));
        $mysqli = $this->createMock("\mysqli");
        $mysqli->method("query")->willReturn($mysqli_answer);
        $usageFactory = $this->createMock("\Kelvinho\Virus\Usage\UsageFactory");
        $this->user = new Kelvinho\Virus\User\User("user_A", $mysqli, $usageFactory);
    }

    public function testGetTimezone() {
        $this->assertEquals(-5, $this->user->getTimezone());
    }

    public function testIsHold() {
        $this->assertTrue($this->user->isHold());
    }

    public function testRemoveHold() {
        $this->user->removeHold();
        $this->assertFalse($this->user->isHold());
    }

    public function testApplyHold() {
        $this->user->removeHold();
        $this->user->applyHold();
        $this->assertTrue($this->user->isHold());
    }
}